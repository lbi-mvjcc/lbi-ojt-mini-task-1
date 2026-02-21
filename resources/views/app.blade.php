<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Task Management System</title>
    @viteReactRefresh
    @vite(['resources/js/main.jsx'])
    <script>
        // Force reload if cached
        if (performance.navigation.type === 2) {
            location.reload(true);
        }
    </script>
</head>
<body>
    <div id="app"></div>
</body>
</html>
