if (typeof window.pusherConfig !== 'undefined') {
    import('laravel-echo').then(({ default: Echo }) => {
        import('pusher-js').then(({ default: Pusher }) => {
            window.Pusher = Pusher;
            let pusherConfig = window.pusherConfig;
            window.Echo = new Echo({
                broadcaster: 'pusher',
                key: pusherConfig.key,
                cluster: pusherConfig.cluster,
                wsHost: pusherConfig.wsHost ?? `ws-${pusherConfig.cluster}.pusher.com`,
                wsPort: pusherConfig.port ?? 80,
                wssPort: pusherConfig.port ?? 443,
                forceTLS: (pusherConfig.scheme ?? 'https') === 'https',
                enabledTransports: ['ws', 'wss'],
            });
        }).catch(error => console.log("Failed to load Pusher:", error));
    }).catch(error => console.log("Failed to load Echo:", error));
} else {
    console.log("window.pusherConfig is not defined.");
}
