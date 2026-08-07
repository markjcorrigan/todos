<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Todo App' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @livewireStyles
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ route('todos.index') }}">Todo App</a>
            <div>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-light btn-sm me-2">Dashboard</a>
                <a href="{{ route('todos.index') }}" class="btn btn-outline-light btn-sm me-2">Manage</a>
                <a href="{{ route('todos.calendar') }}" class="btn btn-outline-light btn-sm me-2">Calendar</a>
                <a href="{{ route('todos.backup') }}" class="btn btn-outline-light btn-sm">Backup</a>
            </div>
        </div>
    </nav>

    <div class="container pb-5">
        {{ $slot }}
    </div>

    @livewireScripts
</body>
</html>
