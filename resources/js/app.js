require('./bootstrap');

require('./store');

require('./echo.pusher');

require('./echo.socket.io');

document.addEventListener('DOMContentLoaded', () => {
    if (Alpine.store('broadcaster') === 'socket.io') {
        initSocketIO();
    } else {
        initPusher();
    }
});

/**
 * Switch broadcaster type
 */
switchBroadcaster = (type = 'pusher') => {
    echo.disconnect();

    if (type === 'socket.io') {
        initSocketIO();
    } else {
        initPusher();
    }

    Alpine.store('broadcaster', type);
    log(`switched to ${type}`);
}

/**
 * Switch channel name and event by given channel type
 */
switchChannelType = (channelType = 'public') => {
    let channelName;
    let event;

    if (channelType === 'public') {
        channelName = 'public-channel';
        event = 'PublicEvent';
    }
    else if (channelType === 'private') {
        channelName = `private-channel.1`;
        event = 'PrivateEvent';
    }
    else if (channelType === 'presence') {
        channelName = `presence-channel.1`;
        event = 'PresenceEvent';
    }

    Alpine.store('listenToChannelModal').channelName = channelName;
    Alpine.store('listenToChannelModal').event = event;
}

/**
 * Listen to echo channel
 */
listen = () => {
    const { channelName, channelType, event } = Alpine.store('listenToChannelModal');
    let channel;

    if (!channelName || !channelType || !event) return alert('Please fill required fields');

    if (channelType === 'public') {
        channel = echo.channel(channelName)
    } else if (channelType === 'private') {
        channel = echo.private(channelName)
    } else if (channelType === 'presence') {
        channel = echo.join(channelName)
            .here((users) => {
                log(`${users.length} active users`);
            })
            .joining((user) => {
                if (user && user.name) log(`${user.name} joined`);
            })
            .leaving((user) => {
                if (user && user.name) log(`${user.name} leaved`);
            });
    }

    channel.listen(event, (e) => {
        let text = `New event from channel: ${channelName}, event: ${event}`;

        if (e.payload) {
            text += `, payload: ${JSON.stringify(e.payload)}`;
        }

        if (e.userId) {
            text += `, userId: ${e.userId}`;
        }

        log(text);
    });

    if (! Alpine.store('listeningChannels').includes(channelName)) {
        Alpine.store('listeningChannels').push(channelName);
    }
    Alpine.store('listenToChannelModal').visible = false;

    log(`Listening to ${channelType} channel: ${channelName}`);
}

/**
 * Leave echo channel
 */
leave = () => {
    const { channelName } = Alpine.store('leaveChannelModal');

    if (!channelName) return alert('Please select channel');

    echo.leave(channelName);

    Alpine.store('listeningChannels').splice(Alpine.store('listeningChannels').indexOf(channelName), 1);
    Alpine.store('leaveChannelModal').visible = false;
    log(`Leaving channel ${channelName}`);
}

/**
 * Log events
 */
log = (event) => Alpine.store('app').log(event);

 /**
 * Fires sample event by given type
 */
fire = (type) => {
    const driver = Alpine.store('broadcaster');

    axios.post(`fire/${type}`, { driver }).then(({ data }) => log(`Fired ${data}`));
}

/**
 * Get socket id
 */
getSocketId = () => log(echo.socketId());

/**
 * Get bearer token to use on mobile app
 */
getBearerToken = () => {
    axios.post('/api/token-for-current-user').then((res) => {
        log(`Your bearer token: ${res.data.token}`);
    });
}

/**
 * Echo connect
 */
connect = () => echo.connect();

/**
 * Echo disconnect
 */
disconnect = () => echo.disconnect();
