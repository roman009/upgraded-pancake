import config from 'config';
import { authHeader } from '../helpers';

export const userService = {
    login,
    logout,
    handleCallback,
    getMe,
};

function login() {
    const requestOptions = {
        method: 'GET',
        headers: { 'Content-Type': 'application/json' },
    };

    return fetch(`${config.apiUrl}/auth-url`, requestOptions)
        .then(handleResponse)
        .then(response => {
            if (response) {
                return response.auth_url;
            }

            return null;
        });
}

function getMe() {
    const requestOptions = {
        method: 'GET',
        headers: authHeader()
    };

    return fetch(`${config.apiUrl}/me`, requestOptions)
        .then(handleResponse)
        .then(response => {
            return response;
        });
}

function logout() {
    localStorage.removeItem('token');
}

function handleResponse(response) {
    return response.text().then(text => {
        const data = text && JSON.parse(text);
        if (!response.ok) {
            if (response.status === 401) {
                logout();
                location.reload(true);
            }

            const error = (data && data.message) || response.statusText;
            return Promise.reject(error);
        }

        return data;
    });
}

function handleCallback(code) {
    const requestOptions = {
        method: 'GET',
        headers: { 'Content-Type': 'application/json' },
    };

    return fetch(`${config.apiUrl}/callback?code=${encodeURIComponent(code)}`, requestOptions)
        .then(handleResponse)
        .then(response => {
            if (response) {
                return response['X-AUTH-TOKEN'];
            }

            return null;
        });
}