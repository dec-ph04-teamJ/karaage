@extends('layouts.app')

        @section('content')
        <div class="chat-container row justify-content-center">
            <div class="chat-area">
                <div class="card">
                    <div class="card-header">Comment</div>
                        <div class="card-body chat-card">
                            @foreach ($chats as $chat)
                            @include('components.comment', ['item' => $item])
                            @endforeach
                        </div>
                </div>
            </div>
        </div>

        <form method="POST" action="{{route('chat.store')}}">
            @csrf
            <div class="chat-container row justify-content-center">
                <div class="chat-area">
                    <div class="card">
                        <div class="card-header">Comment</div>
                        <div class="card-body chat-card">
                            <div id="comment-data"></div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        @endsection

        @section('js')
        <script src="{{ asset('js/comment.js') }}"></script>
        @endsection

            
       