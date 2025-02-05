import axios from 'axios';
import { useAuth } from '../context/AuthContext';

const apiClient = axios.create({
    baseURL: 'http://localhost:9000/api',
});

apiClient.interceptors.request.use(
    (config) => {
        const storedToken = localStorage.getItem('auth_token');
        if (storedToken) {
            config.headers.Authorization = `Bearer ${storedToken}`;
        }
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

export default apiClient;
