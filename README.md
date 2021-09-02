# Laravel Echo Backend

Sample Laravel backend for [laravel_echo](https://github.com/kakajansh/echo) package. Before proceeding make sure you read detailed information from official [Laravel Broadcasting](https://laravel.com/docs/8.x/broadcasting) documentation

Available integrations:

- [pusher](#pusher)
- [laravel-websockets](#laravel-websockets)
- [laravel-echo-server](#laravel-echo-server)

### Authorization

For authorizing private channels [Laravel Sanctum](https://laravel.com/docs/sanctum) is used.

### How to run

1. Clone this repo
2. `composer install`
3. `php artisan migrate`


## [Pusher](https://pusher.com/)

Pusher related connection options available at `/config/broadcasting.php`. Most of time default options are enough.
```php
'pusher' => [
    'driver' => 'pusher',
    'key' => env('PUSHER_APP_KEY'),
    'secret' => env('PUSHER_APP_SECRET'),
    'app_id' => env('PUSHER_APP_ID'),
    'options' => [
        'cluster' => env('PUSHER_APP_CLUSTER'),
        'useTLS' => false,
        'encrypted' => false,
    ],
],
```

Set driver in your `.env` file
```bash
BROADCAST_DRIVER=pusher
```

And pusher related keys
```bash
PUSHER_APP_ID=your-pusher-app-id
PUSHER_APP_KEY=your-pusher-key
PUSHER_APP_SECRET=your-pusher-secret
PUSHER_APP_CLUSTER=your-pusher-cluster
```


## [laravel-websockets](https://github.com/beyondcode/laravel-websockets)

> Laravel WebSockets is a package for Laravel 5.7 and up that will get your application started with WebSockets in no-time! It has a drop-in Pusher API replacement, has a debug dashboard, realtime statistics and even allows you to create custom WebSocket controllers.

Defined separate `websockets` connection that uses `pusher` driver in `/config/broadcasting.php`
```php
'websockets' => [
    'driver' => 'pusher',
    'key' => env('PUSHER_APP_KEY'),
    'secret' => env('PUSHER_APP_SECRET'),
    'app_id' => env('PUSHER_APP_ID'),
    'options' => [
        'useTLS' => false,
        'encrypted' => false,
        'host' => '127.0.0.1',
        'port' => 6001,
    ],
],
```

Set driver in your `.env` file
```bash
BROADCAST_DRIVER=websockets
```

And your websocket server keys
```bash
PUSHER_APP_ID=local
PUSHER_APP_KEY=local
PUSHER_APP_SECRET=local
PUSHER_APP_CLUSTER=local
```

Additionally take a look at `/config/websockets.php` file

Start websockets server
```bash
php artisan websockets:serve
```

## [laravel-echo-server](https://github.com/tlaverdure/laravel-echo-server)

> NodeJs server for Laravel Echo broadcasting with Socket.io.

Notes
- As laravel-echo-server has issues with newer socket.io clients, in the fron-end `"socket.io-client": "^2.3.0"` is used.
- Echo server is configured to listen on `6002` port

Set driver in your `.env` file
```bash
BROADCAST_DRIVER=redis
```

Removed redis prefix
```bash
REDIS_PREFIX=
```

Additionally take a look at `laravel-echo-server.json` file.

Start Echo server
```bash
laravel-echo-server start
```
