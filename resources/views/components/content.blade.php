<div class="media">
    <div class="media-body comment-body">
        <div class="row">
            {{-- <span class="comment-body-user">{{$chat->name}}</span> --}}
            <span class="comment-body-time">{{$chat->created_at}}</span>
        </div>
        <span class="comment-body-content">{!! nl2br(e($chat->content)) !!}</span>
    </div>
</div>