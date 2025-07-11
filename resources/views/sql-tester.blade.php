<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SQL Tester</title>
    <style>
        table { border-collapse: collapse; margin-top: 1em; }
        th, td { border: 1px solid #ccc; padding: 6px 10px; text-align: left; }
        textarea { width: 100%; height: 150px; }
        body { font-family: sans-serif; padding: 20px; }
    </style>
</head>
<body>

<h2>SQL Tester</h2>

<form method="POST">
    @csrf
    <textarea name="sql" placeholder="Введите SQL">{{ old('sql', $query ?? '') }}</textarea>
    <br>
    <button type="submit">Выполнить</button>
</form>

@if ($error)
    <div style="color:red; margin-top:1em;"><strong>Ошибка:</strong> {{ $error }}</div>
@endif

@if ($result)
    <h3>Результаты:</h3>
    <table>
        <thead>
        <tr>
            @foreach ((array) $result[0] as $key => $val)
                <th>{{ $key }}</th>
            @endforeach
        </tr>
        </thead>
        <tbody>
        @foreach ($result as $row)
            <tr>
                @foreach ((array) $row as $val)
                    <td>{{ $val }}</td>
                @endforeach
            </tr>
        @endforeach
        </tbody>
    </table>
@endif

</body>
</html>
