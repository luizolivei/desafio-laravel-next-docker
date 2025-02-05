<?php

namespace App\Http\Controllers;

use App\Models\Music;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MusicController extends Controller
{
    /**
     * Associa uma música ao usuário.
     */
    public function associateMusicToUser($music_id, $user_id)
    {
        $validator = Validator::make([
            'music_id' => $music_id,
            'user_id' => $user_id,
        ], [
            'music_id' => 'required|integer',
            'user_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $music = Music::findOrFail($music_id);
            $user = User::findOrFail($user_id);

            // Verifica se já existe a associação
            if ($music->users()->where('user_id', $user->id)->exists()) {
                return response()->json([
                    'error' => 'Esta música já está associada ao usuário.'
                ], 409);
            }

            // Faz a associação
            $music->users()->attach($user->id);

            return response()->json([
                'message' => 'Música associada ao usuário com sucesso.',
                'music_id' => $music->id,
                'user_id' => $user->id
            ], 201);

        } catch (QueryException $e) {
            return response()->json([
                'error' => 'Erro ao associar a música ao usuário.',
                'details' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Ocorreu um erro inesperado.',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function dissociateMusicToUser($music_id, $user_id)
    {
        $validator = Validator::make([
            'music_id' => $music_id,
            'user_id' => $user_id,
        ], [
            'music_id' => 'required|integer',
            'user_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $music = Music::findOrFail($music_id);
            $user = User::findOrFail($user_id);

            // Verifica se a associação existe antes de tentar remover
            if (!$music->users()->where('user_id', $user->id)->exists()) {
                return response()->json([
                    'error' => 'Esta música não está associada ao usuário.',
                ], 404);
            }

            // Remove a associação
            $music->users()->detach($user->id);

            return response()->json([
                'message' => 'Música desassociada do usuário com sucesso.',
                'music_id' => $music->id,
                'user_id' => $user->id,
            ], 200);

        } catch (QueryException $e) {
            return response()->json([
                'error' => 'Erro ao desassociar a música do usuário.',
                'details' => $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Ocorreu um erro inesperado.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Retorna todas as músicas disponíveis com detalhes completos.
     */
    public function getAllMusics()
    {
        try {
            $musics = DB::select('
            SELECT
                musics.id,
                musics.title,
                GROUP_CONCAT(DISTINCT artists.artist SEPARATOR ", ") AS artists,
                albuns.album AS album_name,
                plataforms.plataform AS platform_name,
                musics.isrc,
                musics.trackId,
                musics.duration,
                musics.addedDate,
                musics.url
            FROM musics
            LEFT JOIN artist_music ON musics.id = artist_music.music_id
            LEFT JOIN artists ON artists.id = artist_music.artist_id
            LEFT JOIN albuns ON musics.album_id = albuns.id
            LEFT JOIN plataforms ON musics.plataform_id = plataforms.id
            GROUP BY musics.id
            ORDER BY musics.title
        ');

            if (empty($musics)) {
                return response()->json([
                    'message' => 'Não há músicas disponíveis.',
                    'data' => []
                ]);
            }

            return response()->json([
                'message' => count($musics) . ' músicas encontradas',
                'data' => $musics
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao buscar músicas.',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retorna todos os usuarios e as suas musicas
     */
    public function getUsersWithMusics()
    {
        try {
        $usersWithMusics = DB::select("
        SELECT
            users.id AS user_id,
            users.name AS user_name,
            musics.id AS music_id,
            musics.title AS music_title,
            GROUP_CONCAT(DISTINCT artists.artist SEPARATOR ', ') AS artists,
            albuns.album AS album_name,
            plataforms.plataform AS platform_name,
            musics.duration,
            musics.url
        FROM users
        LEFT JOIN music_user ON users.id = music_user.user_id
        LEFT JOIN musics ON musics.id = music_user.music_id
        LEFT JOIN artist_music ON musics.id = artist_music.music_id
        LEFT JOIN artists ON artists.id = artist_music.artist_id
        LEFT JOIN albuns ON musics.album_id = albuns.id
        LEFT JOIN plataforms ON musics.plataform_id = plataforms.id
        WHERE musics.id IS NOT NULL
        GROUP BY users.id, musics.id, albuns.album, plataforms.plataform, musics.duration, musics.url, musics.title
        ORDER BY users.name
    ");

        // Reorganiza os dados de saída
        $structuredData = [];
        foreach ($usersWithMusics as $row) {
            $userId = $row->user_id;
            if (!isset($structuredData[$userId])) {
                $structuredData[$userId] = [
                    'user_name' => $row->user_name,
                    'musics' => []
                ];
            }

            $structuredData[$userId]['musics'][] = [
                'id' => $row->music_id,
                'title' => $row->music_title,
                'artists' => $row->artists,
                'album_name' => $row->album_name,
                'platform_name' => $row->platform_name,
                'duration' => $row->duration,
                'url' => $row->url
            ];
        }

        return response()->json(array_values($structuredData));
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao buscar as músicas.',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retorna todas as músicas associadas a um usuário.
     */
    public function getUserMusics($user_id)
    {
        try {
            $musics = DB::select(
                'SELECT
                musics.id,
                musics.title,
                GROUP_CONCAT(artists.artist SEPARATOR \', \') AS artists,
                albuns.album AS album_name,
                plataforms.plataform AS platform_name,
                musics.isrc,
                musics.trackId,
                musics.duration,
                musics.addedDate,
                musics.url
            FROM musics
            INNER JOIN music_user ON musics.id = music_user.music_id
            LEFT JOIN artist_music ON musics.id = artist_music.music_id
            LEFT JOIN artists ON artist_music.artist_id = artists.id
            LEFT JOIN albuns ON musics.album_id = albuns.id
            LEFT JOIN plataforms ON musics.plataform_id = plataforms.id
            WHERE music_user.user_id = ?
            GROUP BY musics.id, musics.title, albuns.album, plataforms.plataform, musics.isrc, musics.trackId, musics.duration, musics.addedDate, musics.url',
                [$user_id]
            );

            if (empty($musics)) {
                return response()->json([
                    'message' => 'Nenhuma música encontrada para este usuário.',
                    'data' => []
                ]);
            }

            return response()->json([
                'message' => count($musics) . ' músicas encontradas',
                'data' => $musics
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao buscar as músicas.',
                'details' => $e->getMessage()
            ], 500);
        }
    }


}
