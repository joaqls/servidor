<?php

require_once 'DaoEquipos.php';

$dao = new DaoEquipos();

// ── Gestión de acciones POST ────────────────────────────────────────────────

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $accion    = $_POST['accion']    ?? '';
    $idEquipo  = $_POST['id_equipo'] ?? null;

    switch ($accion)
    {
        case 'subir':
            if ($idEquipo) $dao->subirPuesto($idEquipo);
            break;

        case 'bajar':
            if ($idEquipo) $dao->bajarPuesto($idEquipo);
            break;

        case 'borrar':
            if ($idEquipo) $dao->borrar($idEquipo);
            break;

        case 'anadir':
            $nombre = trim($_POST['nombre'] ?? '');
            if ($nombre !== '')
            {
                $eq = new Equipo();
                $eq->__set('nombre', $nombre);

                $escudo = null;
                if (isset($_FILES['escudo']) && $_FILES['escudo']['error'] === UPLOAD_ERR_OK)
                {
                    // Codificar la imagen en base64 para guardarla en el campo BLOB
                    $escudo = base64_encode(file_get_contents($_FILES['escudo']['tmp_name']));
                }
                $eq->__set('escudo', $escudo);

                $dao->insertar($eq);
            }
            break;
    }

    // Patrón Post-Redirect-Get: evita reenvío del formulario al recargar
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// ── Obtener listado actualizado ─────────────────────────────────────────────

$dao->listar();
$equipos = $dao->equipos;
$total   = count($equipos);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>CRUD Equipos</title>
    <style>
        body        { font-family: Arial, sans-serif; margin: 20px; }
        h2          { color: #333; }
        table       { border-collapse: collapse; width: 100%; }
        th, td      { border: 1px solid #aaa; padding: 8px 12px; text-align: center; }
        thead th    { background-color: #4a7fcb; color: #fff; }
        tbody tr:nth-child(even) { background-color: #f2f2f2; }
        tbody tr:hover           { background-color: #dde8fb; }
        img.escudo  { width: 50px; height: 50px; object-fit: contain; }
        .btn        { padding: 5px 12px; cursor: pointer; }
        .btn-borrar { background-color: #e74c3c; color: #fff; border: none; border-radius: 3px; }
        .btn-move   { background-color: #27ae60; color: #fff; border: none; border-radius: 3px; padding: 6px 16px; }
        .btn-add    { background-color: #2980b9; color: #fff; border: none; border-radius: 3px; padding: 6px 16px; }
        .acciones   { margin-top: 10px; }
        input[type="text"]  { width: 140px; padding: 4px; }
        input[type="file"]  { font-size: 0.85em; }
        td.vacia    { background-color: #fffde7; }
    </style>
</head>
<body>

<h2>CRUD Equipos</h2>

<!-- ── Formulario principal (listado + añadir + subir/bajar/borrar) ───────── -->
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">

    <!-- Campo oculto que identifica la acción; los botones lo sobreescriben -->
    <input type="hidden" name="accion" value="">

    <table>
        <thead>
            <tr>
                <th>Sel</th>
                <th>Escudo</th>
                <th>Nombre</th>
                <th>Puesto</th>
                <th>Borrar</th>
            </tr>
        </thead>
        <tbody>

            <!-- Filas de datos -->
            <?php foreach ($equipos as $eq): ?>
            <tr>
                <td>
                    <input type="radio"
                           id="radio_<?php echo $eq->__get('id'); ?>"
                           name="id_equipo"
                           value="<?php echo $eq->__get('id'); ?>">
                </td>
                <td>
                    <?php if ($eq->__get('escudo') !== null && $eq->__get('escudo') !== ''): ?>
                        <img class="escudo"
                             src="data:image/jpeg;base64,<?php echo $eq->__get('escudo'); ?>"
                             alt="escudo">
                    <?php else: ?>
                        &mdash;
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($eq->__get('nombre')); ?></td>
                <td><?php echo $eq->__get('puesto'); ?></td>
                <td>
                    <!-- Botón Borrar: selecciona el radio de la fila antes de enviar -->
                    <button class="btn btn-borrar"
                            type="submit"
                            name="accion"
                            value="borrar"
                            onclick="document.getElementById('radio_<?php echo $eq->__get('id'); ?>').checked=true;">
                        Borrar
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>

            <!-- Fila vacía para dar de alta un nuevo equipo -->
            <tr>
                <td class="vacia"></td>
                <td class="vacia">
                    <input type="file" name="escudo" accept="image/*">
                </td>
                <td class="vacia">
                    <input type="text" name="nombre" placeholder="Nombre del equipo">
                </td>
                <td class="vacia"></td>
                <td class="vacia">
                    <button class="btn btn-add" type="submit" name="accion" value="anadir">
                        Añadir
                    </button>
                </td>
            </tr>

        </tbody>
    </table>

    <!-- Botones de movimiento de puesto -->
    <div class="acciones">
        <button class="btn btn-move" type="submit" name="accion" value="subir">&#9650; Subir</button>
        <button class="btn btn-move" type="submit" name="accion" value="bajar">&#9660; Bajar</button>
    </div>

</form>

</body>
</html>
