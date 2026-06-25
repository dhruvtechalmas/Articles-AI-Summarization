<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>AI Article Summarizer</title>

    @vite(['resources/css/app.css','resources/js/app.js'])

</head>

<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">

    <div class="container">

        <a class="navbar-brand" href="{{ route('articles.index') }}">

            AI Article Summarizer

        </a>

        <div class="ms-auto">

            <a href="{{ route('articles.index') }}" class="btn btn-outline-light">

                Articles

            </a>

        </div>

    </div>

</nav>

<div class="container py-4">

    @yield('content')

</div>

</body>

</html>