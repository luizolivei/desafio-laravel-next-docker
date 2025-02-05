<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Music;
use App\Models\Artist;
use App\Models\Album;
use App\Models\Plataform;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;

class ImportMusic extends Command
{
    protected $signature = 'import {file : Caminho para o arquivo CSV}';
    protected $description = 'Importa os CSV com músicas para dentro do sistema';
    protected $erros = [];

    /**
     * Executa o comando.
     */
    public function handle()
    {
        $filePath = $this->argument('file');
        if (!$this->fileExists($filePath)) {
            return 1;
        }

        $header = $this->getCsvHeader($filePath);
        if (!$header) {
            $this->error("O arquivo CSV não possui cabeçalho válido.");
            return 1;
        }

        $rowCount = $this->processCsv($filePath, $header);

        // Exibe os erros, se existirem
        if (!empty($this->erros)) {
            $this->error("Foram encontrados erros durante a importação:");
            foreach ($this->erros as $erro) {
                $this->error($erro);
            }
        }

        if ($rowCount > 0) {
            $this->info("Importação concluída com sucesso. {$rowCount} registro(s) importado(s).");
        } else {
            $this->warn("Nenhum registro foi importado.");
        }

        return 0;
    }

    /**
     * Verifica se o arquivo existe.
     */
    private function fileExists($filePath): bool
    {
        if (!file_exists($filePath)) {
            $this->error("Arquivo {$filePath} não encontrado.");
            return false;
        }
        return true;
    }

    /**
     * Lê e retorna o cabeçalho do CSV.
     */
    private function getCsvHeader($filePath): ?array
    {
        $handle = fopen($filePath, 'r');
        $header = fgetcsv($handle, 1000, ',');
        fclose($handle);
        return $header ?: null;
    }

    /**
     * Processa o CSV linha a linha.
     */
    private function processCsv($filePath, $header): int
    {
        $rowCount = 0;
        $handle = fopen($filePath, 'r');
        fgetcsv($handle);  // Ignora o cabeçalho

        DB::beginTransaction();

        try {
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                $row = array_combine($header, $data);

                $validationErrors = $this->validateRow($row);
                if (!empty($validationErrors)) {
                    $this->erros = array_merge($this->erros, $validationErrors);
                    continue;
                }

                $artistIds = $this->getOrCreateArtistis($row['artist']);
                $albumId = $this->getOrCreateAlbum($row['album']);
                $platformId = $this->getOrCreatePlatform($row['platform']);
                $addedDate = $this->parseDate($row['addedDate']);

                $this->createMusicRecord($row, $albumId, $platformId, $artistIds, $addedDate);
                $rowCount++;
            }

            DB::commit();  // Finaliza a transação se tudo estiver correto

        } catch (Exception $e) {
            DB::rollBack();  // Reverte a transação em caso de erro crítico
            $this->error("Erro durante a importação: " . $e->getMessage());
        } finally {
            fclose($handle);
        }

        return $rowCount;
    }

    /**
     * Valida as colunas que nao podem ser nulas no bd.
     */
    private function validateRow($row): array
    {
        $errors = [];
        $requiredFields = ['title', 'album', 'platform', 'trackId', 'duration', 'url'];

        foreach ($requiredFields as $field) {
            if (empty($row[$field])) {
                $errors[] = "O campo '{$field}' é obrigatório para a música '{$row['title']}' ou foi omitido.";
            }
        }

        return $errors;
    }

    /**
     * Busca ou cria artistas e retorna os ids deles.
     */
    private function getOrCreateArtistis(string $artists): array
    {
        $artistNames = explode(',', $artists);
        $artistIds = [];

        foreach ($artistNames as $artist) {
            $artist = trim($artist);
            $existingArtist = Artist::firstOrCreate(['artist' => $artist]);
            $artistIds[] = $existingArtist->id;
        }

        return $artistIds;
    }

    /**
     * Busca ou cria um album e retorna o ID.
     */
    private function getOrCreateAlbum(string $album): int
    {
        $existingAlbum = Album::firstOrCreate(['album' => $album]);
        return $existingAlbum->id;
    }

    /**
     * Busca ou cria uma plataforma e retorna o ID.
     */
    private function getOrCreatePlatform(string $platform): int
    {
        $existingPlatform = Plataform::firstOrCreate(['plataform' => $platform]);
        return $existingPlatform->id;
    }

    /**
     * Converte uma data de string para objeto Carbon.
     */
    private function parseDate(?string $date): ?Carbon
    {
        return !empty($date) && strtotime($date) !== false ? Carbon::parse($date) : null;
    }

    /**
     * Cria ou ignora um registro de música se o `trackId` já existir.
     */
    private function createMusicRecord($row, $albumId, $platformId, $artistIds, $addedDate)
    {
        if (Music::where('trackId', $row['trackId'])->exists()) {
            $this->info("Música com trackId {$row['trackId']} já existe. Ignorando.");
            return;
        }

        $music = Music::create([
            'title' => $row['title'],
            'album_id' => $albumId,
            'isrc' => $row['isrc'] ?? null,
            'plataform_id' => $platformId,
            'trackId' => $row['trackId'],
            'duration' => $row['duration'],
            'addedDate' => $addedDate,
            'addedBy' => empty($row['addedBy']) ? null : $row['addedBy'],
            'url' => $row['url'],
        ]);

        $music->artists()->attach($artistIds);
    }
}
