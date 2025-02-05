import apiClient from './apiClient';

/**
 * Busca todas as músicas disponíveis no sistema.
 */
export const fetchAllMusics = async () => {
    try {
        const response = await apiClient.get('/musics');
        return response.data.data;
    } catch (err) {
        throw new Error(err.response?.data?.error || 'Erro ao buscar músicas.');
    }
};

/**
 * Busca as músicas associadas a um usuário.
 */
export const fetchUserMusics = async (userId) => {
    try {
        const response = await apiClient.get(`/user/${userId}/musics`);
        return response.data.data;
    } catch (err) {
        throw new Error('Erro ao buscar músicas do usuário.');
    }
};

/**
 * Adiciona uma música à jukebox do usuário.
 */
export const addMusicToJukebox = async (musicId, userId) => {
    try {
        const response = await apiClient.post(`/music/${musicId}/user/${userId}`);
        return response.data.message || 'Música adicionada com sucesso!';
    } catch (err) {
        throw new Error(err.response?.data?.error || 'Erro ao adicionar música à jukebox.');
    }
};

/**
 * Remove uma música da jukebox do usuário.
 */
export const removeMusicFromJukebox = async (musicId, userId) => {
    try {
        await apiClient.delete(`/music/${musicId}/user/${userId}`);
    } catch (err) {
        throw new Error('Erro ao remover música da jukebox.');
    }
};

/**
 * Busca todos os usuários e suas músicas.
 */
export const fetchUsersWithMusics = async () => {
    try {
        const response = await apiClient.get('/users-with-musics');
        return response.data;
    } catch (err) {
        throw new Error(err.response?.data?.error || 'Erro ao buscar usuários e suas músicas.');
    }
};
