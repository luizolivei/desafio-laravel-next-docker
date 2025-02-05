"use client";

import {useEffect, useState} from 'react';
import {fetchUsersWithMusics} from './services/musicService';
import {useAuth} from './context/AuthContext';
import MusicTable from './components/MusicTable';
import Container from './components/layout/Container';
import LoadingSpinner from "./components/LoadingSpinner";

export default function Home() {
    const {token} = useAuth();
    const [usersWithMusics, setUsersWithMusics] = useState([]);
    const [loading, setLoading] = useState(true);
    const [errorMessage, setErrorMessage] = useState('');

    useEffect(() => {
        const getUsersWithMusics = async () => {
            try {
                const data = await fetchUsersWithMusics(token);
                setUsersWithMusics(data);
            } catch (err) {
                setErrorMessage(err.message);
            } finally {
                setLoading(false);
            }
        };

        getUsersWithMusics();
    }, [token]);

    if (loading) {
        return <LoadingSpinner/>;
    }

    if (errorMessage) {
        return (
            <Container>
                <p className="text-center mt-5 text-red-500">{errorMessage}</p>
            </Container>
        );
    }

    return (
        <Container>
            <div className="grid grid-rows-1">
                <h1 className="text-4xl font-bold text-center mb-10">Confira a JukeBox da galera</h1>
                {usersWithMusics.length > 0 ? (
                    <div className="row-span-1">
                        {usersWithMusics.map((user) => (
                            <div key={user.user_name} className="bg-gray-100 p-8 m-3 rounded-lg shadow-md">
                                <h2 className="text-2xl font-semibold mb-4">{user.user_name}</h2>
                                {user.musics.length > 0 ? (
                                    <MusicTable musics={user.musics}/>
                                ) : (
                                    <p className="text-gray-600">Nenhuma música associada.</p>
                                )}
                            </div>
                        ))}
                    </div>
                ) : (
                    <p className="text-gray-600">Nenhum usuário com músicas associadas.</p>
                )}
            </div>
        </Container>
    );
}
