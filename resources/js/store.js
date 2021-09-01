Alpine.store('connected', false);
Alpine.store('broadcaster', process.env.MIX_BROADCAST_DRIVER === 'redis' ? 'socket.io' : 'pusher');
Alpine.store('listeningChannels', []);

Alpine.store('leaveChannelModal', {
    visible: false,
    channelName: undefined,
    toggle() {
        this.visible = !this.visible
    }
});

Alpine.store('listenToChannelModal', {
    visible: false,
    channelName: 'public-channel',
    channelType: 'public',
    event: 'PublicEvent',
    toggle() {
        this.visible = !this.visible
    },
});

Alpine.store('app', {
    logs: [],

    log(text) {
        const now = new Date()
        let date = `${now.getHours().toString().padStart(2,'0')}`;
        date += `:${now.getMinutes().toString().padStart(2,'0')}`;
        date += `:${now.getSeconds().toString().padStart(2,'0')}`;
        this.logs.unshift({ date, text })
    },

    clear() {
        this.logs = [];
    }
});

Alpine.start();
