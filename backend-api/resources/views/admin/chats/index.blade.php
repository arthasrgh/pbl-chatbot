<!DOCTYPE html>

<html>
<head>
    <title>Riwayat Chat</title>

```
<style>
    body{
        font-family: Arial, sans-serif;
        margin:0;
        padding:0;
    }

    .container{
        display:flex;
        height:100vh;
    }

    .sidebar{
        width:300px;
        border-right:1px solid #ddd;
        overflow-y:auto;
    }

    .sidebar h2{
        padding:15px;
        margin:0;
        background:#f5f5f5;
    }

    .user{
        display:block;
        padding:12px 15px;
        border-bottom:1px solid #eee;
        text-decoration:none;
        color:#333;
    }

    .user:hover{
        background:#f5f5f5;
    }

    .chat{
        flex:1;
        padding:20px;
        overflow-y:auto;
    }

    .msg{
        margin-bottom:15px;
        padding:10px;
        border-radius:8px;
    }

    .user-msg{
        background:#dcf8c6;
    }

    .bot-msg{
        background:#f1f1f1;
    }

    .admin-msg{
        background:#ffe0b2;
    }

    .sender{
        font-weight:bold;
        margin-bottom:5px;
    }
</style>
```

</head>
<body>

<div class="container">

```
<div class="sidebar">

    <h2>Daftar User</h2>

    @foreach($users as $user)
        <a
            class="user"
            href="/admin/chats/{{ urlencode($user->nomor) }}"
        >
            {{ $user->nomor }}
        </a>
    @endforeach

</div>

<div class="chat">

    @if(isset($nomor))

        <h2>Riwayat Chat {{ $nomor }}</h2>

        @foreach($messages as $msg)

            <div class="msg {{ $msg->sender }}-msg">

                <div class="sender">
                    {{ strtoupper($msg->sender) }}
                </div>

                <div>
                    {{ $msg->pesan }}
                </div>

            </div>

        @endforeach

    @else

        <h2>Pilih user di sebelah kiri</h2>

    @endif

</div>
```

</div>

</body>
</html>
