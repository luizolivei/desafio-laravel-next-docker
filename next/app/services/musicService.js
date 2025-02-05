import axios from 'axios';

/**
 * Busca todas as músicas disponíveis no sistema.
 */
export const fetchAllMusics = async (token) => {
    try {
        const response = await axios.get('http://localhost:9000/api/musics', {
            headers: {
                Authorization: `Bearer ${token}`,
            },
        });
        return response.data.data;
    } catch (err) {
        throw new Error(err.response?.data?.error || 'Erro ao buscar músicas.');
    }
};

/**
 * Busca as músicas associadas a um usuário.
 */
export const fetchUserMusics = async (userId, token) => {
    try {
        const response = await axios.get(`http://localhost:9000/api/user/${userId}/musics`, {
            headers: {
                Authorization: `Bearer ${token}`,
            },
        });
        return response.data.data;
    } catch (err) {
        throw new Error('Erro ao buscar músicas do usuário.');
    }
};

/**
 * Adiciona uma música à jukebox do usuário.
 */
export const addMusicToJukebox = async (musicId, userId, token) => {
    try {
        const response = await axios.post(`http://localhost:9000/api/music/${musicId}/user/${userId}`, {}, {
            headers: {
                Authorization: `Bearer ${token}`,
            },
        });
        return response.data.message || 'Música adicionada com sucesso!';
    } catch (err) {
        throw new Error(err.response?.data?.error || 'Erro ao adicionar música à jukebox.');
    }
};

/**
 * Remove uma música da jukebox do usuário.
 */
export const removeMusicFromJukebox = async (musicId, userId, token) => {
    try {
        await axios.delete(`http://localhost:9000/api/music/${musicId}/user/${userId}`, {
            headers: {
                Authorization: `Bearer ${token}`,
            },
        });
    } catch (err) {
        throw new Error('Erro ao remover música da jukebox.');
    }
};

/**
 * Busca todos os usuários e suas músicas.
 */
export const fetchUsersWithMusics = async (token) => {
    try {
        const response = await axios.get('http://localhost:9000/api/users-with-musics', {
            headers: {
                Authorization: `Bearer ${token}`,
            },
        });
        return response.data;
    } catch (err) {
        throw new Error(err.response?.data?.error || 'Erro ao buscar usuários e suas músicas.');
    }
};
