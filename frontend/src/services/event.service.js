import config from 'config';
import { authHeader } from '../helpers';

export const eventService = {
    getAll,
    getOne,
    end,
};

function getAll() {
    let events = localStorage.getItem('events_cache');
    let eventsExpiry = localStorage.getItem('events_cache_expiry');
    if (null === events || null === eventsExpiry || (new Date(eventsExpiry)) < (new Date())) {
        const requestOptions = {
            method: 'GET',
            headers: authHeader()
        };

        return fetch(`${config.apiUrl}/events`, requestOptions)
            .then(handleResponse)
            .then(response => {
                localStorage.setItem('events_cache', JSON.stringify(response));
                let eventsCacheExpiry = new Date();
                eventsCacheExpiry.setSeconds(eventsCacheExpiry.getSeconds() + 1);
                localStorage.setItem('events_cache_expiry', eventsCacheExpiry);

                return response;
            });
    }

    events = JSON.parse(events);

    return new Promise((resolve, reject) => {
        resolve(events);
    });
}

function end(eventId) {
    const requestOptions = {
        method: 'PUT',
        headers: authHeader()
    };

    return fetch(`${config.apiUrl}/events/${encodeURIComponent(eventId)}`, requestOptions)
        .then(handleResponse)
        .then(response => {
            return response;
        });
}

function getOne(eventId) {
    const requestOptions = {
        method: 'GET',
        headers: authHeader()
    };

    return fetch(`${config.apiUrl}/events/${encodeURIComponent(eventId)}`, requestOptions)
        .then(handleResponse)
        .then(response => {
            return response;
        });
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