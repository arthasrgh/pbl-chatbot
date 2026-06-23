<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tambah FAQ</title>

    <style>
        body{
            font-family: Arial, sans-serif;
            margin:40px;
        }

        input,
        textarea{
            width:100%;
            padding:10px;
            margin-top:5px;
            margin-bottom:15px;
        }

        button{
            padding:10px 15px;
        }

        .back{
            text-decoration:none;
        }
    </style>
</head>
<body>

<h1>Tambah FAQ</h1>

<a href="/admin/faqs" class="back">
    ← Kembali ke Dashboard
</a>

<form action="/admin/faqs" method="POST">

    @csrf

    <label>Keyword</label>
    <input
        type="text"
        name="keyword"
        required>

    <label>Pertanyaan</label>
    <input
        type="text"
        name="question"
        required>

    <label>Jawaban</label>
    <textarea
        name="answer"
        rows="5"
        required></textarea>

    <button type="submit">
        Simpan FAQ
    </button>

</form>

</body>
</html>