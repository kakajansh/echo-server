/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

import Echo from 'laravel-echo';

/**
 * socket.io
 */
window.io = require('socket.io-client');

window.initSocketIO = () => {
    window.echo = new Echo({
        broadcaster: 'socket.io',
        host: 'http://localhost:6002',
        auth: {
            endpoint: '/api/broadcasting/auth',
            headers: {
                Authorization: 'Bearer 18|bvcr6e4XCzjH9277gsDsr36yjcFDFAdIsAhmGfQb',
            }
        },
    });

    echo.connector.socket.on('connect', () => {
        Alpine.store('connected', true);
        log('socket.io connected');
    });

    echo.connector.socket.on('disconnect', () => {
        Alpine.store('connected', false);
        log('socket.io disconnected');
    });
}
