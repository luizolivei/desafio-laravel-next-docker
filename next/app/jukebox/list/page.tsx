"use client";

import { useEffect, useState } from 'react';
import { useAuth } from '../../context/AuthContext';
import LoadingSpinner from '../../components/LoadingSpinner';
import { fetchAllMusics, addMusicToJukebox } from '../../services/musicService';

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
        <div className="max-w-4xl mx-auto mt-8">
            <h1 className="text-2xl font-bold text-center">Lista de Músicas</h1>
            <a href="/jukebox">Voltar para a jukebox</a>

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

            <table className="table-auto border-collapse border border-gray-300 w-full mt-5">
                <thead>
                <tr>
                    <th className="border border-gray-300 px-4 py-2">Título</th>
                    <th className="border border-gray-300 px-4 py-2">ISRC</th>
                    <th className="border border-gray-300 px-4 py-2">Duração</th>
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
                            <button
                                onClick={() => addToJukebox(music.id)}
                                className="px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-700"
                            >
                                Adicionar à Jukebox
                            </button>
                        </td>
                    </tr>
                ))}
                </tbody>
            </table>
        </div>
    );
}