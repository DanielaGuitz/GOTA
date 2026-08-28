<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h3>Bienvenido, <?= esc(session()->get('usuario_nombre')) ?></h3>
        <p>Rol ID: <?= esc(session()->get('rol_id')) ?></p>
        <a href="/logout" class="btn btn-outline-danger">Cerrar sesión</a>
    </div>
</body>
</html>