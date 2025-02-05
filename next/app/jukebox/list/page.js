"use client";

import { useEffect, useState } from 'react';
import { useAuth } from '../../context/AuthContext';
import LoadingSpinner from '../../components/LoadingSpinner';
import { fetchAllMusics, addMusicToJukebox } from '../../services/musicService';
import MusicTable from '../../components/MusicTable';

export default function List() {
    const [musics, setMusics] = useState([]);
    const [loading, setLoading] = useState(true);
    const [successMessage, setSuccessMessage] = useState('');
    const [errorMessage, setErrorMessage] = useState('');
    const { user, token } = useAuth();

    useEffect(() => {
        const getMusics = async () => {
            try {
                const allMusics = await fetchAllMusics(token);
                setMusics(allMusics);
            } catch (err) {
                setErrorMessage(err.message);
            } finally {
                setLoading(false);
            }
        };

        getMusics();
    }, [token]);

    const addToJukebox = async (musicId) => {
        try {
            const message = await addMusicToJukebox(musicId, user.id, token);
            setSuccessMessage(message);
            setErrorMessage('');
            setTimeout(() => setSuccessMessage(''), 3000);
        } catch (err) {
            setErrorMessage(err.message);
        }
    };

    if (loading) {
        return <LoadingSpinner />;
    }

    return (
        <div className="max-w-5xl mx-auto p-5">
            <h1 className="text-3xl font-bold mb-4 text-center">Lista de Músicas</h1>
            <a href="/jukebox"
               className="inline-block px-6 py-2 mt-3 text-white font-semibold bg-gradient-to-r from-blue-500 to-blue-700 rounded-lg shadow-lg hover:from-blue-700 hover:to-blue-900 focus:ring-2 focus:ring-offset-2 focus:ring-blue-400"
            >
                Voltar para a jukebox
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

            <MusicTable
                musics={musics}
                actions={(music) => (
                    <button
                        onClick={() => addToJukebox(music.id)}
                        className="px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-700"
                    >
                        Adicionar à Jukebox
                    </button>
                )}
            />
        </div>
    );
}