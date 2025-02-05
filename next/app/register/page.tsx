"use client";

import { useState } from 'react';
import { useRouter } from 'next/navigation';
import axios from 'axios';
import { useAuth } from '../context/AuthContext';
import Container from '../components/Container';

export default function Register() {
  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');
  const [isLoading, setIsLoading] = useState(false);  // Estado de carregamento
  const { login } = useAuth();
  const router = useRouter();

  const handleRegister = async (e) => {
    e.preventDefault();
    setIsLoading(true);  // Ativa o estado de carregamento

    try {
      const response = await axios.post('http://localhost:9000/api/register', {
        name,
        email,
        password,
      });

      login(response.data.user, response.data.access_token);
      router.push('/jukebox');
    } catch (err) {
      setError(err.response?.data?.message || 'Erro ao efetuar o registro.');
    } finally {
      setIsLoading(false);  // Desativa o estado de carregamento
    }
  };

  return (
      <Container>
        <div className="w-full max-w-md p-8 space-y-6 bg-gray-100 border rounded-lg shadow-md">
          <h2 className="text-3xl font-bold text-center text-gray-800">Registrar</h2>

          <form onSubmit={handleRegister} className="space-y-5">
            <div>
              <label htmlFor="name" className="block text-sm font-medium text-gray-700">Nome</label>
              <input
                  id="name"
                  value={name}
                  onChange={(e) => setName(e.target.value)}
                  required
                  className="w-full px-4 py-2 mt-1 border rounded-md shadow-sm border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200"
              />
            </div>

            <div>
              <label htmlFor="email" className="block text-sm font-medium text-gray-700">Email</label>
              <input
                  type="email"
                  id="email"
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                  required
                  className="w-full px-4 py-2 mt-1 border rounded-md shadow-sm border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200"
              />
            </div>

            <div>
              <label htmlFor="password" className="block text-sm font-medium text-gray-700">Senha</label>
              <input
                  type="password"
                  id="password"
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  required
                  className="w-full px-4 py-2 mt-1 border rounded-md shadow-sm border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200"
              />
            </div>

            {error && <p className="text-red-500 text-sm">{error}</p>}

            <button
                type="submit"
                disabled={isLoading}
                className={`w-full py-2 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1
              ${isLoading ? 'bg-blue-400 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700'}`}
            >
              {isLoading ? 'Registrando...' : 'Registrar'}
            </button>
          </form>

          <span className="text-gray-800">
          Já tem uma conta?
          <a className="text-blue-700 font-bold" href="/login"> Faça login aqui</a>
        </span>
        </div>
      </Container>
  );
}
