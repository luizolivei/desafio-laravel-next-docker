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

        $rowCount = 0;
        $arrErrors = [];
        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            // title, artist, album, isrc, platform, trackId, duration, addedDate, addedBy, url
            $row = array_combine($header, $data);

//            if(empty($row['title'])) {
//                $arrErrors[] = 'Uma das musicas importadas não havia titulo';
//                continue;
//            }
//
//            $title = $row['title'];
            $title = "teste";

            //Validacoes de itens obrigatorios
            $hasError = false;
            if(empty($row['album'])) {
                $arrErrors[] = 'A musica ' . $title . ' não possui um album';
                $hasError = true;
            }
            if(empty($row['platform'])) {
                $arrErrors[] = 'A musica ' . $title . ' não possui uma plataforma';
                $hasError = true;
            }
            if(empty($row['trackId'])) {
                $arrErrors[] = 'A musica ' . $title . ' não possui uma trackId';
                $hasError = true;
            }
            if(empty($row['duration'])) {
                $arrErrors[] = 'A musica ' . $title . ' não tem duração';
                $hasError = true;
            }
            if(empty($row['url'])) {
                $arrErrors[] = 'A musica ' . $title . ' não possui uma url';
                $hasError = true;
            }

            if ($hasError) continue;

            // separa os artistas atraves da virgula e cria eles
            $arrArtists = implode(', ', $row['artist']);
            $arrArtistIds = [];
            foreach ($arrArtists as $artist) {
                $artist = trim($artist);
                $artist = Artist::firstOrCreate(
                    ['artist' => $artist]
                );
                $arrArtistIds[] = $artist->id;
            }

            // Procura ou cria o álbum
            $album = Album::firstOrCreate(
                ['album' => $row['album']]
            );

            // Procura ou cria a plataforma
            $plataform = Plataform::firstOrCreate(
                ['plataform' => $row['platform']]
            );

            // Converte a data, se houver
            $addedDate = !empty($row['addedDate']) ? Carbon::parse($row['addedDate']) : null;

            // Cria o registro na tabela musics
            $music = Music::create([
                'title'        => $title,
                'album_id'     => $album->id,
                'isrc'         => $row['isrc'],
                'plataform_id' => $plataform->id,
                'trackId'      => $row['trackId'],
                'duration'     => $row['duration'],
                'addedDate'    => $addedDate,
                'addedBy'      => !empty($row['addedBy']) ? $row['addedBy'] : null,
                'url'      => ($row['url']),
            ]);

            // associa a lista de artistas com a musica
            $music->artists()->attach($arrArtistIds);

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
