@extends('layouts.app')

@section('chat.content')
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

@endsection

<form method="POST" action="{{route('chat.store')}}">
    @csrf
    <div class="comment-container row justify-content-center">
        <div class="input-group comment-area">
            <textarea class="form-control" id="content" name="content" placeholder="push massage (shift + Enter)"
                aria-label="With textarea"
                onkeydown="if(event.shiftKey&&event.keyCode==13){document.getElementById('submit').click();return false};"></textarea>
            <input type="submit" id="submit" class="btn btn-outline-primary comment-btn">送信
        </div>
    </div>
</form>

@section('js')
<script src="{{ asset('js/comment.js') }}"></script>
@endsection
