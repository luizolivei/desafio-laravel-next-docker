"use client";

import { createContext, useContext, useEffect, useState } from 'react';
import { useRouter } from 'next/navigation';

const AuthContext = createContext();

export const AuthProvider = ({ children }) => {
    const [user, setUser] = useState(null);
    const [token, setToken] = useState(null);  // Armazena o token de acesso
    const router = useRouter();

    // Verificar se o usuário está logado ao carregar o aplicativo
    useEffect(() => {
        const storedToken = localStorage.getItem('auth_token');
        const storedUser = JSON.parse(localStorage.getItem('auth_user'));

        if (storedToken && storedUser) {
            setToken(storedToken);
            setUser(storedUser);
        } else {
            router.push('/login');
        }

    }, [router]);

    // Função de login
    const login = (userData, accessToken) => {
        setUser(userData);
        setToken(accessToken);
        localStorage.setItem('auth_token', accessToken);
        localStorage.setItem('auth_user', JSON.stringify(userData));
    };

    // Função de logout
    const logout = () => {
        localStorage.removeItem('auth_token');
        localStorage.removeItem('auth_user');
        setUser(null);
        setToken(null);
        router.push('/login');
    };

    return (
        <AuthContext.Provider value={{ user, token, login, logout }}>
            {children}
        </AuthContext.Provider>
    );
};

export const useAuth = () => useContext(AuthContext);
