"use client";

import { useState } from 'react';
import { useRouter } from 'next/navigation';
import axios from 'axios';

import Container from '../components/container';

export default function Login() {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');
  const router = useRouter();

  const handleLogin = async (e) => {
    e.preventDefault();

    try {
      const response = await axios.post('http://localhost:9000/api/login', {
        email,
        password,
      });

      localStorage.setItem('auth_token', response.data.access_token);
      router.push('/dashboard');
    } catch (err) {
      setError(err.response?.data?.error || 'Erro ao efetuar o login.');
    }
  };

  return (
      <Container>
        <div className="w-full max-w-md p-8 space-y-6 bg-gray-100 border rounded-lg shadow-md">
          <h2 className="text-3xl font-bold text-center text-gray-800">Login</h2>

          <form onSubmit={handleLogin} className="space-y-5">
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
                className="w-full py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1"
            >
              Entrar
            </button>
          </form>
        </div>
      </Container>
  );
}
