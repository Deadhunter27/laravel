<style>
    .message-box {
        max-width: 70%; /* Atur lebar maksimum sesuai kebutuhan */
        padding: 10px;
        margin-bottom: 10px;
        border-radius: 8px;
    }

    .float-left {
        float: left;
        background-color: white; /* Warna latar belakang untuk pesan yang diterima */
    }

    .float-right {
        float: right;
        background-color: #4CAF50; /* Warna latar belakang untuk pesan yang dikirim */
        color: white;
    }

    .clearfix {
        clear: both;
    }
</style>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Messages
        </h2>
    </x-slot>

    <div class="w-full h-32" style="background-color: #449388"></div>

    <div class="container mx-auto mt-6 flex">
        <!-- Left Sidebar -->
        <div class="w-1/4 pr-4">
            <div class="bg-grey-lightest p-4 rounded">
                <!-- Header -->
                <div class="flex items-center mb-4">
                    <img class="w-10 h-10 rounded-full" src="{{ auth()->user()->avatar }}" alt="User Avatar">
                    <div class="ml-4">
                        <p class="text-grey-darkest">{{ auth()->user()->name }}</p>
                    </div>
                </div>

                <!-- Search -->
                <div class="mb-4">
                    <input type="text" class="w-full px-2 py-2 text-sm border rounded" placeholder="Search or start new chat"/>
                </div>

                <!-- Contacts -->
        <div class="bg-grey-lighter flex-1 overflow-auto">
            <h2 class="text-lg font-semibold mb-2">Contacts</h2>
            @foreach ($users as $user)
                <div class="flex items-center mb-2">
                    <img class="w-8 h-8 rounded-full" src="{{ $user->avatar }}" alt="{{ $user->name }}">
                    <p class="ml-2">{{ $user->name }}</p>
                    <!-- Add a link or button to select the user and initiate chat -->
                    <a href="{{ route('messages.show', $user->id) }}" class="ml-auto text-blue-500">Chat</a>
                </div>
            @endforeach
        </div>
            </div>
        </div>

        <!-- Right Content -->
        <div class="w-3/4">
            <div class="bg-grey-lightest p-4 rounded">
                <!-- Chat Header -->
                <div class="flex items-center justify-between mb-4">
                    @if ($chatUser)
                        <div class="flex items-center">
                            <img class="w-10 h-10 rounded-full" src="{{ $chatUser->avatar }}" alt="Chat User Avatar">
                            <div class="ml-4">
                                <p class="text-grey-darkest">{{ $chatUser->name }}</p>
                            </div>
                        </div>
                    @else
                        <p class="text-grey-darkest">No user selected</p>
                    @endif
                    <div class="flex">
                        <!-- Additional options or icons if needed -->
                    </div>
                </div>

                <!-- Messages -->
<!-- Messages -->
<div class="flex-1 overflow-auto" style="background-color: #DAD3CC">
    @if ($chatUser)
        @php
            $currentUserId = auth()->id();
            $lastSenderId = null;
            $lastMessageContent = null;
        @endphp

        @foreach ($messages as $message)
            @php
                $isCurrentUser = $message->user_id === $currentUserId;
                $isNewConversation = $lastSenderId !== $message->user_id || $lastMessageContent === null || $lastMessageContent !== $message->content;
            @endphp

            @if ($isNewConversation)
                <div class="clearfix"></div> <!-- Baris baru sebelum percakapan baru -->
            @endif

            <div class="mb-2">
                <!-- Display each message, adjust styling as needed -->
                <p class="message-box {{ $isCurrentUser ? 'float-right' : 'float-left' }}">
                    {{ $message->content }}
                </p>
            </div>

            @php
                $lastSenderId = $message->user_id;
                $lastMessageContent = $message->content;
            @endphp
        @endforeach
    @else
        <p class="text-grey-darkest">No user selected</p>
    @endif
</div>


                <!-- Input Form -->
                @if ($chatUser)
                    <div class="bg-grey-lighter px-4 py-4 flex items-center mt-4">
                        <div>
                            <!-- Additional icons if needed -->
                        </div>
                        <div class="flex-1 mx-4">
                            <form id="messageForm" action="{{ route('messages.store') }}" method="post">
                                @csrf
                                <input type="hidden" name="recipient_id" value="{{ $chatUser->id }}">
                                <input class="w-full border rounded px-2 py-2" type="text" name="content" placeholder="Type your message"/>
                            </form>

                            @error('content')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <!-- Additional icons if needed -->
                            <button type="button" onclick="sendMessage()" class="bg-blue-500 text-white px-4 py-2 rounded">Send</button>
                        </div>
                    </div>
                @endif

                <script>
                    function sendMessage() {
                        // Submit the form using JavaScript
                        document.getElementById('messageForm').submit();
                    }
                </script>
            </div>
        </div>
    </div>
</x-app-layout>
