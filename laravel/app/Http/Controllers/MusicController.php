<?php

namespace App\Http\Controllers;

use App\Models\Music;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MusicController extends Controller
{
    protected $music;
    protected $user;

    /**
     * Busca o usuario e valida a entrada de dados e erros subsequentes
     */
    private function findUser($user_id)
    {
        if (!is_numeric($user_id)) {
            return [
                'error' => 'O ID do usuario está invalido',
                'code' => 400
            ];
        }

        try {
            $this->user = User::findOrFail($user_id);
            return null;
        } catch (\Exception $e) {
            return [
                'error' => 'Usuario não encontrada.',
                'details' => $e->getMessage(),
                'code' => 404
            ];
        }
    }

    /**
     * Busca a musica e valida a entrada de dados e erros subsequentes
     */
    private function findMusic($music_id)
    {
        if (!is_numeric($music_id)) {
            return [
                'error' => 'O id da musica está invalido.',
                'code' => 400
            ];
        }

        try {
            $this->music = Music::findOrFail($music_id);
            return null;
        } catch (\Exception $e) {
            return [
                'error' => 'Música não encontrada.',
                'details' => $e->getMessage(),
                'code' => 404
            ];
        }
    }

    /**
     * Associa uma música ao usuário.
     */
    public function associateToUser($music_id, $user_id)
    {
        $validation = $this->findMusic($music_id);
        if ($validation) {
            return response()->json([
                'error' => $validation['error'],
                'details' => $validation['details'] ?? null
            ], $validation['code']);
        }

        $validation = $this->findUser($user_id);
        if ($validation) {
            return response()->json([
                'error' => $validation['error'],
                'details' => $validation['details'] ?? null
            ], $validation['code']);
        }

        try {
            // Verifica se já existe a associação
            if ($this->music->users()->where('user_id', $this->user->id)->exists()) {
                return response()->json([
                    'error' => 'Esta música já está associada ao usuário.'
                ], 409);  // Código 409 (Conflito)
            }

            // Faz a associação
            $this->music->users()->attach($this->user->id);

            return response()->json([
                'message' => 'Música associada ao usuário com sucesso.',
                'music_id' => $this->music->id,
                'user_id' => $this->user->id
            ], 201);  // Código 201 (Criado)

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

    /**
     * Retorna todas as músicas associadas a um usuário.
     */
    public function getUserMusics($user_id)
    {
        // Validação e busca do usuário
        $validation = $this->findUser($user_id);
        if ($validation) {
            return response()->json([
                'error' => $validation['error'],
                'details' => $validation['details'] ?? null
            ], $validation['code']);
        }

        try {
            $musics = DB::select(
                'SELECT musics.id, musics.title, musics.isrc, musics.trackId, musics.duration, musics.addedDate, musics.url
                FROM musics
                INNER JOIN music_user ON musics.id = music_user.music_id
                WHERE music_user.user_id = ?',
                [$user_id]
            );

            // Verifica se ha musicas associadas
            if (empty($musics)) {
                return response()->json([
                    'message' => 'Nenhuma musica encontrada para este usuario.'
                ], 404);
            }

            return response()->json([
                'message' => count($musics) . ' musicas encontradas',
                'data' => $musics
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao buscar as musicas.',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
