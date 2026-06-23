<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Edit FAQ</title>

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

<h1>Edit FAQ</h1>

<a href="/admin/faqs" class="back">
    ← Kembali ke Dashboard
</a>

<form action="/admin/faqs/{{ $faq->id }}" method="POST">

    @csrf
    @method('PUT')

    <label>Keyword</label>
    <input
        type="text"
        name="keyword"
        value="{{ $faq->keyword }}"
        required>

    <label>Pertanyaan</label>
    <input
        type="text"
        name="question"
        value="{{ $faq->question }}"
        required>

    <label>Jawaban</label>
    <textarea
        name="answer"
        rows="5"
        required>{{ $faq->answer }}</textarea>

    <button type="submit">
        Update FAQ
    </button>

</form>

</body>
</html>