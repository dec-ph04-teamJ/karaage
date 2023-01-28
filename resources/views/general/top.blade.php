<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>chat</title>
</head>
<body>
    <div class=".rounded-sm .border-black">

        <div class="chat-container">
            {{-- チャット内容表示 --}}
            @foreach ($comments as $comment)
            <div class="chat">
                <div class="name">
                    <span>{{ $comment->name }}</span>
                </div>
                <div class="content">
                    <div>{{ $comment->content }}</div>
                </div>
            </div>
            @endforeach
            {{-- 点数表示 --}}
            <div class=".rounded-sm .border-black">
                <h2>{{ $comment->score }}</h2>
            </div>
        </div>

        {{-- 入力フォーム --}}
        <div class=".rounded-sm .border-black">
            <div class=".rounded-sm .border-black">
                <form action="" method="POST">
                    @csrf
                    <textarea name="content" placeholder="入力してください" id="" cols="30" rows="10"></textarea>
                    <button type="submit">送信</button>
                </form>
            </div>
        </div>

    </div>    

</body>
</html>