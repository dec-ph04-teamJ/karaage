<div class="media">
    <div class="media-body comment-body">
        <div class="row">
            <span class="comment-body-user">{{$chats->name}}</span>
            <span class="comment-body-time">{{$chats->created_at}}</span>
        </div>
        {{-- コメントは複数行表示出来るように、e()でエスケープ→nl2brで改行をbrタグに変換→タグがエスケープされないように{!! !!}で囲んでいる。 --}}
        <span class="comment-body-content">{!! nl2br(e($chats->content)) !!}</span>
    </div>
</div>