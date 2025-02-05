"use client";

import { useAuth } from '../context/AuthContext';
import { useEffect, useState } from 'react';
import { fetchUserMusics, removeMusicFromJukebox } from '../services/musicService';

export default function Jukebox() {
    const { user, token, loading } = useAuth();
    const [musics, setMusics] = useState([]);
    const [successMessage, setSuccessMessage] = useState('');
    const [errorMessage, setErrorMessage] = useState('');

    useEffect(() => {
        const getMusics = async () => {
            if (!user || !token) return;

            try {
                const userMusics = await fetchUserMusics(user.id, token);
                setMusics(userMusics);
            } catch (err) {
                setErrorMessage(err.message);
            }
        };

        getMusics();
    }, [user, token]);

    const removeMusic = async (musicId) => {
        try {
            await removeMusicFromJukebox(musicId, user.id, token);
            setMusics(musics.filter((music) => music.id !== musicId));
            setSuccessMessage('Música removida com sucesso!');
            setTimeout(() => setSuccessMessage(''), 3000);
        } catch (err) {
            setErrorMessage(err.message);
            setTimeout(() => setErrorMessage(''), 3000);
        }
    };

    if (loading) {
        return <p>Carregando...</p>;
    }

    return (
        <div>
            <h1>Sua lista de músicas para ouvir, {user?.name}</h1>
            <a href="jukebox/list">Adicionar músicas</a>

            {successMessage && (
                <div className="p-4 mt-4 text-green-700 bg-green-100 rounded">
                    {successMessage}
                </div>
            )}

            {errorMessage && (
                <div className="p-4 mt-4 text-red-700 bg-red-100 rounded">
                    {errorMessage}
                </div>
            )}

            {musics.length > 0 ? (
                <table className="table-auto border-collapse border border-gray-300 w-full mt-5">
                    <thead>
                    <tr>
                        <th className="border border-gray-300 px-4 py-2">Título</th>
                        <th className="border border-gray-300 px-4 py-2">ISRC</th>
                        <th className="border border-gray-300 px-4 py-2">Duração</th>
                        <th className="border border-gray-300 px-4 py-2">Link</th>
                        <th className="border border-gray-300 px-4 py-2">Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    {musics.map((music) => (
                        <tr key={music.id}>
                            <td className="border border-gray-300 px-4 py-2">{music.title}</td>
                            <td className="border border-gray-300 px-4 py-2">{music.isrc}</td>
                            <td className="border border-gray-300 px-4 py-2">{music.duration}</td>
                            <td className="border border-gray-300 px-4 py-2">
                                <a href={music.url} target="_blank" rel="noopener noreferrer" className="text-blue-500">
                                    Ouvir
                                </a>
                            </td>
                            <td className="border border-gray-300 px-4 py-2">
                                <button
                                    onClick={() => removeMusic(music.id)}
                                    className="px-3 py-1 text-white bg-red-600 rounded hover:bg-red-700"
                                >
                                    Remover
                                </button>
                            </td>
                        </tr>
                    ))}
                    </tbody>
                </table>
            ) : (
                <p>Nenhuma música encontrada.</p>
            )}
        </div>
    );
}