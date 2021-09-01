<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel Echo</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">
        <style>
            body {
                font-family: 'Nunito';
            }
        </style>
    </head>
    <body class="h-screen">
        <div x-data class="flex flex-col  h-full">
            <div class="flex items-center justify-between bg-white border-b border-gray-300 h-14">
                <div class="w-10"></div>
                <div class="flex border rounded-md border-blue-500">
                    <div
                        class="px-4 py-1 rounded-l cursor-pointer"
                        :class="$store.broadcaster === 'pusher' ? 'bg-blue-500 text-white hover:bg-blue-600' : 'text-blue-500 hover:bg-gray-200'"
                        @click="switchBroadcaster('pusher')"
                    >
                        pusher
                    </div>
                    <div
                        class="px-4 py-1 rounded-r cursor-pointer"
                        :class="$store.broadcaster === 'socket.io' ? 'bg-blue-500 text-white hover:bg-blue-600' : 'text-blue-500 hover:bg-gray-200'"
                        @click="switchBroadcaster('socket.io')"
                    >
                        socket.io
                    </div>
                </div>
                <div class="flex items-center h-full w-10 cursor-pointer hover:opacity-60" @click="$store.app.clear()">
                    <span class="material-icons">block</span>
                </div>
            </div>

            <div class="flex-1 flex flex-col-reverse bg-gray-100 overflow-y-scroll py-6">
                <template x-for="log in $store.app.logs">
                    <div class="px-8 py-2 text-lg">
                        <span class="text-blue-500" x-text="log.date"></span>&nbsp;
                        <span x-text="log.text"></span>
                    </div>
                </template>
            </div>

            <div class="flex-1 border-t border-gray-300  py-6">
                <div class="px-8 pb-1 border-b uppercase text-gray-500">Client events</div>
                <div class="px-8 py-2 text-lg font-bold text-blue-500 hover:bg-gray-100 cursor-pointer" @click="$store.connected ? disconnect : connect">
                    <div class="flex justify-between">
                        <div x-text="$store.connected ? 'disconnect' : 'connect'"></div>
                        <div class="h-6 w-6 rounded-full" :class="$store.connected ? 'bg-green-500' : 'bg-red-500'"></div>
                    </div>
                </div>
                <div class="px-8 py-2 text-lg font-bold text-blue-500 hover:bg-gray-100 cursor-pointer" @click="$store.listenToChannelModal.toggle()">
                    <div class="flex justify-between">
                        <div>listen to channel</div>
                        <div class="h-6 w-6 bg-gray-300 rounded-full text-gray-600 text-sm flex items-center justify-center" x-text="$store.listeningChannels.length"></div>
                    </div>
                </div>
                <div class="px-8 py-2 text-lg font-bold text-blue-500 hover:bg-gray-100 cursor-pointer" @click="$store.leaveChannelModal.toggle()">leave channel</div>
                <div class="px-8 py-2 text-lg font-bold text-blue-500 hover:bg-gray-100 cursor-pointer" @click="getSocketId">get socket-id</div>

                <div class="px-8 pb-1 mt-5 border-b uppercase text-gray-500">Server events</div>
                <div class="px-8 py-2 text-lg font-bold text-blue-500 hover:bg-gray-100 cursor-pointer" @click="fire('public')">fire PublicEvent on public channel</div>
                <div class="px-8 py-2 text-lg font-bold text-blue-500 hover:bg-gray-100 cursor-pointer" @click="fire('private')">fire PrivateEvent on private channel</div>
                <div class="px-8 py-2 text-lg font-bold text-blue-500 hover:bg-gray-100 cursor-pointer" @click="fire('presence')">fire PresenceEvent on presence channel</div>
                <div class="px-8 py-2 text-lg font-bold text-blue-500 hover:bg-gray-100 cursor-pointer" @click="getBearerToken">get bearer token for current user</div>
            </div>

            {{-- Listen to channel modal --}}
            <div class="absolute bottom-0 h-screen w-screen bg-black bg-opacity-40" :class="$store.listenToChannelModal.visible ? '' : 'hidden'">
                <div class="flex flex-col h-full">
                    <div class="flex-1" @click="$store.listenToChannelModal.toggle()"></div>
                    <div class="flex-1 bg-white p-6">
                        <div class="space-y-3">
                            <select class="w-full rounded" x-model="$store.listenToChannelModal.channelType" x-init="$watch('$store.listenToChannelModal.channelType', (v) => switchChannelType(v))">
                                <option value="public">Public</option>
                                <option value="private">Private</option>
                                <option value="presence">Presence</option>
                            </select>
                            <input type="text" class="w-full rounded" placeholder="Channel name" x-model="$store.listenToChannelModal.channelName">
                            <input type="text" class="w-full rounded" placeholder="Event" x-model="$store.listenToChannelModal.event">
                            <input type="button" value="Listen" class="w-full rounded p-2" @click="listen">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Leave channel modal --}}
            <div class="absolute bottom-0 h-screen w-screen bg-black bg-opacity-40" :class="$store.leaveChannelModal.visible ? '' : 'hidden'">
                <div class="flex flex-col h-full">
                    <div class="flex-1" @click="$store.leaveChannelModal.toggle()"></div>
                    <div class="flex-1 bg-white p-6">
                        <div x-show="$store.listeningChannels.length > 0" class="space-y-3">
                            <select x-model="$store.leaveChannelModal.channelName" class="w-full rounded">
                                <option value="" selected disabled>Select channel</option>
                                <template x-for="(channel, index) in $store.listeningChannels">
                                    <option :key="channel" :value="channel" x-text="channel"></option>
                                </template>
                            </select>
                            <input type="button" value="Leave" class="w-full rounded p-2" @click="leave">
                        </div>
                        <div x-show="$store.listeningChannels.length == 0" class="flex flex-col items-center ">
                            <div>No listening channels</div>
                            <input type="button" value="Close" class="rounded py-2 px-8 mt-2" @click="$store.leaveChannelModal.toggle()">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="{{ mix('js/app.js') }}"></script>
    </body>
</html>
