"use client";

import { useAuth } from '../context/AuthContext';
import { useEffect, useState } from 'react';
import { fetchUserMusics, removeMusicFromJukebox } from '../services/musicService';
import MusicTable from '../components/MusicTable';
import LoadingSpinner from "../components/LoadingSpinner";

export default function Jukebox() {
    const { user, token } = useAuth();  // Renomeando auth loading
    const [musics, setMusics] = useState([]);
    const [apiLoading, setApiLoading] = useState(true);  // Novo estado para o loading da API
    const [successMessage, setSuccessMessage] = useState('');
    const [errorMessage, setErrorMessage] = useState('');

    useEffect(() => {
        const getMusics = async () => {
            if (!user || !token) return;

            setApiLoading(true);  // Inicia o carregamento da API

            try {
                const userMusics = await fetchUserMusics(user.id, token);
                setMusics(userMusics);
            } catch (err) {
                setErrorMessage(err.message);
            } finally {
                setApiLoading(false);  // Finaliza o carregamento da API
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

    // Mostra o loading durante o carregamento da autenticação ou da API
    if (apiLoading) {
        return <LoadingSpinner />;
    }

    return (
        <div className="max-w-5xl mx-auto p-5">
            <h1 className="text-3xl font-bold mb-4 text-center">Sua Jukebox, {user?.name}</h1>
            <a
                href="jukebox/list"
                className="inline-block px-6 py-2 mt-3 text-white font-semibold bg-gradient-to-r from-blue-500 to-blue-700 rounded-lg shadow-lg hover:from-blue-700 hover:to-blue-900 focus:ring-2 focus:ring-offset-2 focus:ring-blue-400"
            >
                Adicionar músicas
            </a>


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
                <MusicTable
                    musics={musics}
                    actions={(music) => (
                        <button
                            onClick={() => removeMusic(music.id)}
                            className="px-3 py-1 text-white bg-red-600 rounded hover:bg-red-700"
                        >
                            Remover
                        </button>
                    )}
                />
            ) : (
                <p className="mt-4 text-gray-600">Nenhuma música encontrada.</p>
            )}
        </div>
    );
}
