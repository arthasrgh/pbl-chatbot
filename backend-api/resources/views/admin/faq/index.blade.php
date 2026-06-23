<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Dashboard FAQ Bangkesbangpol</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
        }

        h1 {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background: #f2f2f2;
        }

        .btn {
            padding: 8px 12px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
        }

        .btn-add {
            background: #28a745;
            color: white;
        }

        .btn-edit {
            background: #007bff;
            color: white;
        }

        .btn-delete {
            background: #dc3545;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
        }

        .success {
            color: green;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

    <h1>Dashboard FAQ Bangkesbangpol</h1>

    @if(session('success'))
        <div class="success">
            {{ session('success') }}
        </div>
    @endif

    <p>
        <a href="/admin/faqs/create" class="btn btn-add">
            + Tambah FAQ
        </a>
    </p>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Keyword</th>
                <th>Pertanyaan</th>
                <th>Jawaban</th>
                <th width="180">Aksi</th>
            </tr>
        </thead>

        <tbody>

            @forelse($faqs as $faq)

                <tr>
                    <td>{{ $faq->id }}</td>
                    <td>{{ $faq->keyword }}</td>
                    <td>{{ $faq->question }}</td>
                    <td>{{ $faq->answer }}</td>

                    <td>

                        <a
                            href="/admin/faqs/{{ $faq->id }}/edit"
                            class="btn btn-edit">
                            Edit
                        </a>

                        <form
                            action="/admin/faqs/{{ $faq->id }}"
                            method="POST"
                            style="display:inline">

                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="btn-delete"
                                onclick="return confirm('Hapus FAQ ini?')">
                                Hapus
                            </button>

                        </form>

                    </td>
                </tr>

            @empty

                <tr>
                    <td colspan="5">
                        Belum ada data FAQ.
                    </td>
                </tr>

            @endforelse

        </tbody>
    </table>

</body>
</html>