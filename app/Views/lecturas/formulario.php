<?php
/**
 * @var int $contador_id
 * @var float $lectura_anterior
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Lectura</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5" style="max-width: 500px;">
    <h2 class="mb-4">Registrar Lectura del Contador</h2>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?= esc(session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>

    <form action="<?= site_url('lecturas/guardar') ?>" method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="contador_id" value="<?= esc((string) $contador_id) ?>">

        <div class="mb-3">
            <label class="form-label">Contador</label>
            <input type="text" class="form-control" value="#<?= esc((string) $contador_id) ?>" disabled>
        </div>

        <div class="mb-3">
            <label class="form-label">Lectura anterior</label>
            <input type="text" class="form-control" value="<?= esc((string) $lectura_anterior) ?>" disabled>
            <input type="hidden" name="lectura_anterior" value="<?= esc((string) $lectura_anterior) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Lectura actual</label>
            <input type="number" step="0.01" name="lectura_actual" class="form-control" required autofocus>
        </div>

        <button type="submit" class="btn btn-primary w-100">Guardar Lectura</button>
    </form>
</div>
</body>
</html>