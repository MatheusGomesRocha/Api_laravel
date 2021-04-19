<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

</head>

<body class="antialiased">
<div>

    <form method="post" action="/api/login">
        <input name="user" />
        <input name="password" />
        <input type="submit" />
    </form>

        {{$teste}}
</div>
</body>
</html>
