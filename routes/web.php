<?php

use App\Events\PresenceEvent;
use App\Events\PrivateEvent;
use App\Events\PublicEvent;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::view('/', 'broadcasting')->middleware(['auth']);
Route::view('/home', 'dashboard')->middleware(['auth'])->name('home');
Route::post('fire/{type}', function (String $type) {
    if (request('driver', 'pusher') === 'pusher') {
        $broadcaster = config('broadcasting.connections.pusher.key') === 'local' ? 'websockets' : 'pusher';
    } else {
        $broadcaster = 'redis';
    }

    // Sets broadcasting driver on the fly
    config(['broadcasting.default' => $broadcaster]);

    // Fires sample event by given channel type
    switch ($type) {
        case 'public':
            event(new PublicEvent('payload'));
            return response('PublicEvent');

        case 'private':
            event(new PrivateEvent(1, 'payload'));
            return response('PrivateEvent');

        case 'presence':
            event(new PresenceEvent(1, 'payload'));
            return response('PresenceEvent');
    }
});

require __DIR__ . '/auth.php';
