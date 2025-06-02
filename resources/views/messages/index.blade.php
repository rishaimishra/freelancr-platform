@extends('layouts.master')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Job Details</h5>
                    </div>
                    <div class="card-body">
                        <h6>{{ $job->title }}</h6>
                        <p class="text-muted mb-2">
                            <i class="fas fa-user me-2"></i>{{ $job->client->name }}
                        </p>

                        <p class="text-muted mb-0">
                            <i class="fas fa-dollar-sign me-2"></i>{{ number_format($job->budget, 2) }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Chat</h5>
                    </div>
                    <div class="card-body">
                        <div class="chat-messages" id="chatMessages" style="height: 400px; overflow-y: auto;">
                            @foreach ($messages as $message)
                                <div class="message {{ $message->sender_id === auth()->id() ? 'text-end' : '' }} mb-3">
                                    <div
                                        class="message-content d-inline-block p-2 rounded {{ $message->sender_id === auth()->id() ? 'bg-primary text-white' : 'bg-light' }}">
                                        <small
                                            class="d-block {{ $message->sender_id === auth()->id() ? 'text-white-50' : 'text-muted' }}">
                                            {{ $message->sender->name }} -
                                            {{ $message->sent_at->format('M d, Y H:i') }}
                                        </small>
                                        {{ $message->message }}
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="chat-input mt-3">
                            <form id="chatForm" class="d-flex">
                                <input type="text" class="form-control me-2" placeholder="Type your message..."
                                    id="messageInput">
                                <button type="submit" class="btn btn-primary">Send</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize Pusher
                const pusher = new Pusher('{{ config('broadcasting.connections.pusher.key') }}', {
                    cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}'
                });

                // Subscribe to private channel
                const channel = pusher.subscribe('private-job.{{ $job->id }}');

                channel.bind('NewMessage', function(data) {
                    appendMessage(data.message);
                });

                // Handle message submission
                $('#chatForm').on('submit', function(e) {
                    e.preventDefault();
                    const input = $('#messageInput');
                    const message = input.val().trim();

                    if (message) {
                        sendMessage(message);
                        input.val('');
                    }
                });

                // Scroll to bottom of messages
                const messagesContainer = $('#chatMessages');
                messagesContainer.scrollTop(messagesContainer[0].scrollHeight);
            });

            function appendMessage(message) {
                const isCurrentUser = message.sender_id === {{ auth()->id() }};
                const messageHtml = `
        <div class="message ${isCurrentUser ? 'text-end' : ''} mb-3">
            <div class="message-content d-inline-block p-2 rounded ${isCurrentUser ? 'bg-primary text-white' : 'bg-light'}">
                <small class="d-block ${isCurrentUser ? 'text-white-50' : 'text-muted'}">
                    ${message.sender.name} - ${new Date(message.created_at).toLocaleString()}
                </small>
                ${message.message}
            </div>
        </div>
    `;

                $('#chatMessages').append(messageHtml);
                $('#chatMessages').scrollTop($('#chatMessages')[0].scrollHeight);
            }

            function sendMessage(message) {
                $.ajax({
                    url: '{{ route('messages.broadcast', $job) }}',
                    method: 'POST',
                    data: {
                        message: message,
                        receiver_id: {{ auth()->user()->user_type === 'user' ? $job->contractor_id : $job->client_id }},
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        appendMessage(response.message);
                    }
                });
            }
        </script>
    @endpush
@endsection
