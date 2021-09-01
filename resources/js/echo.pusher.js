/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

import Echo from 'laravel-echo';

/**
 * Pusher
 */
window.Pusher = require('pusher-js');

window.initPusher = () => {
    if (process.env.MIX_PUSHER_APP_KEY === 'local')
    {
        /**
         * Sample configuration for laravel-websockets
         */
        window.echo = new Echo({
            broadcaster: 'pusher',
            key: 'local',
            wsHost: '127.0.0.1',
            wsPort: 6001,
            forceTLS: false,
            disableStats: true,
        });
    } else {
        /**
         * Sample configuration for pusher
         */
        window.echo = new Echo({
            broadcaster: 'pusher',
            key: process.env.MIX_PUSHER_APP_KEY,
            cluster: process.env.MIX_PUSHER_APP_CLUSTER,
            authorizer: (channel, options) => {
                return {
                    authorize: (socketId, callback) => {
                        axios.post('/api/broadcasting/auth', {
                            socket_id: socketId,
                            channel_name: channel.name
                        }, {
                            'Content-Type': 'application/json',
                            'Access-Control-Allow-Credentials': true,
                        })
                        .then(response => {
                            callback(false, response.data);
                        })
                        .catch(error => {
                            alert(error);
                            callback(true, error);
                        });
                    }
                };
            },
        });
    }

    /**
     * Pusher events
     */
    echo.connector.pusher.connection.bind('state_change', (states) => {
        if (states.current) {
            log(`pusher ${states.current}`);
            Alpine.store('connected', states.current === 'connected');
        }
    });

    echo.connector.pusher.connection.bind_global((event, data) => {
        console.log('global', event, data);
    });
}
