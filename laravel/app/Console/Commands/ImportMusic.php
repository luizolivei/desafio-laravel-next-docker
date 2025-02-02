<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Music;
use App\Models\Artist;
use App\Models\Album;
use App\Models\Plataform;
use Carbon\Carbon;

class ImportMusic extends Command
{
    /**
     * O nome e assinatura do comando.
     *
     * @var string
     */
    protected $signature = 'import {file : Caminho para o arquivo CSV}';

    /**
     * A descrição do comando.
     *
     * @var string
     */
    protected $description = 'Importa os CSV com musicas para dentro do sistema';

    /**
     * Executa o comando.
     */
    public function handle()
    {
        $filePath = $this->argument('file');

        if (!file_exists($filePath)) {
            $this->error("Arquivo {$filePath} não encontrado.");
            return 1;
        }

        if (($handle = fopen($filePath, 'r')) === false) {
            $this->error("Não foi possível abrir o arquivo {$filePath}.");
            return 1;
        }

        // Ler o cabeçalho do CSV para mapear as colunas
        $header = fgetcsv($handle, 1000, ',');
        if (!$header) {
            $this->error("O arquivo CSV não possui cabeçalho, o padrão é: title, artist, album, isrc, platform, trackId, duration, addedDate, addedBy, url");
            return 1;
        }

        $fileContent = file_get_contents($filePath);
        $fileContent = preg_replace('/^\xEF\xBB\xBF/', '', $fileContent);
        file_put_contents($filePath, $fileContent);

        $rowCount = 0;
        $arrErrors = [];
        $handle = fopen($filePath, 'r');
        fgetcsv($handle);
        $delimiter = ',';
        while (($data = fgetcsv($handle, 1000, $delimiter)) !== false) {
            // title, artist, album, isrc, platform, trackId, duration, addedDate, addedBy, url
            $row = array_combine($header, $data);

            dump($row['artist']);


            if (empty($row['title'])) {
                $arrErrors[] = 'Uma das musicas importadas não havia titulo';
                continue;
            }

            $title = $row['title'];

            //Validacoes de itens obrigatorios
            $hasError = false;
            if (empty($row['album'])) {
                $arrErrors[] = 'A musica ' . $title . ' não possui um album';
                $hasError = true;
            }
            if (empty($row['platform'])) {
                $arrErrors[] = 'A musica ' . $title . ' não possui uma plataforma';
                $hasError = true;
            }
            if (empty($row['trackId'])) {
                $arrErrors[] = 'A musica ' . $title . ' não possui uma trackId';
                $hasError = true;
            }
            if (empty($row['duration'])) {
                $arrErrors[] = 'A musica ' . $title . ' não tem duração';
                $hasError = true;
            }
            if (empty($row['url'])) {
                $arrErrors[] = 'A musica ' . $title . ' não possui uma url';
                $hasError = true;
            }

            if ($hasError) continue;

            // separa os artistas atraves da virgula e cria eles
            $arrArtists = explode(',', $row['artist']);
            $arrArtistIds = [];
            foreach ($arrArtists as $artist) {
                $artist = trim($artist);

                // Verificar se já existe no banco
                $existingArtist = Artist::where('artist', $artist)->first();

                if ($existingArtist) {
                    $arrArtistIds[] = $existingArtist->id;
                } else {
                    // Criar se não encontrado
                    $newArtist = Artist::create(['artist' => $artist]);
                    $arrArtistIds[] = $newArtist->id;
                }
            }

            // Buscar ou criar o álbum
            $existingAlbum = Album::where('album', $row['album'])->first();
            $albumId = $existingAlbum ? $existingAlbum->id : Album::create(['album' => $row['album']])->id;

            // Buscar ou criar a plataforma
            $existingPlataform = Plataform::where('plataform', $row['platform'])->first();
            $plataformId = $existingPlataform ? $existingPlataform->id : Plataform::create(['plataform' => $row['platform']])->id;

            dump("plataforma" . $plataformId);

            // Converte a data, se houver
            $addedDate = null;
            if (!empty($row['addedDate']) && strtotime($row['addedDate']) !== false) {
                $addedDate = Carbon::parse($row['addedDate']);
            }

            // Cria o registro na tabela musics
            $existingMusic = Music::where('trackId', $row['trackId'])->first();
            if (!$existingMusic) {
                $music = Music::create([
                    'title' => $title,
                    'album_id' => $albumId,
                    'isrc' => $row['isrc'],
                    'plataform_id' => $plataformId,
                    'trackId' => $row['trackId'],
                    'duration' => $row['duration'],
                    'addedDate' => $addedDate,
                    'addedBy' => !empty($row['addedBy']) ? $row['addedBy'] : null,
                    'url' => ($row['url']),
                ]);

                // associa a lista de artistas com a musica
                $music->artists()->attach($arrArtistIds);
            }

            $rowCount++;
        }
        fclose($handle);

        if (!empty($arrErrors)) {
            $this->info("Não foi possivel importar todos os dados. Apenas {$rowCount} registro(s) foram importado(s).");
            $this->info("Os seguintes erros foram encontrados:");

            foreach ($arrErrors as $error) {
                $this->error($error);
            }
            return 1;
        }

        $this->info("Importação concluída com sucesso. {$rowCount} registro(s) importado(s).");
        return 0;
    }
}
