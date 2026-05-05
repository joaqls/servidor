<?php

require_once 'DaoEquipos.php';

header('Content-Type: application/json; charset=utf-8');

$dao = new DaoEquipos();

function ok($data = [])
{
    echo json_encode(['ok' => true, 'data' => $data], JSON_UNESCAPED_UNICODE);
    exit;
}

function fail($message, $code = 400)
{
    http_response_code($code);
    echo json_encode(['ok' => false, 'error' => $message], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'GET') {
        $accion = $_GET['accion'] ?? 'listar';

        if ($accion !== 'listar') {
            fail('Accion GET no valida.');
        }

        $equipos = $dao->listar();
        error_log('Equipos cargados: ' . count($equipos));
        $out = [];
        foreach ($equipos as $eq) {
            $out[] = [
                'id' => $eq->id,
                'nombre' => $eq->nombre,
                'fechafund' => $eq->fechafund,
                'presupuesto' => $eq->presupuesto,
                'puesto' => $eq->puesto,
                'logo_base64' => $eq->logo
            ];
        }

        ok([
            'equipos' => $out,
            'total' => count($out)
        ]);
    }

    if ($method === 'POST') {
        $accion = $_POST['accion'] ?? '';

        if ($accion === 'anadir') {
            $nombre = trim($_POST['nombre'] ?? '');
            if ($nombre === '') {
                fail('El nombre es obligatorio.');
            }

            $fechafund = isset($_POST['fechafund']) ? (int)$_POST['fechafund'] : 0;
            if ($fechafund < 1800 || $fechafund > 2026) {
                fail('El año de fundación es obligatorio y debe estar entre 1800 y 2026.');
            }

            $presupuesto = isset($_POST['presupuesto']) ? (int)$_POST['presupuesto'] : -1;
            if ($presupuesto < 0) {
                fail('El presupuesto es obligatorio y debe ser un número positivo.');
            }

            if (!isset($_FILES['logo']) || $_FILES['logo']['error'] !== UPLOAD_ERR_OK) {
                fail('El logo es obligatorio.');
            }

            $tmp = $_FILES['logo']['tmp_name'];
            if (!is_uploaded_file($tmp)) {
                fail('Archivo no valido.');
            }
            $logoBin = base64_encode(file_get_contents($tmp));

            $dao->insertar($nombre, $fechafund, $presupuesto, $logoBin);
            ok(['message' => 'Equipo anadido correctamente.']);
        }

        if ($accion === 'actualizar_puesto') {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            $puesto = isset($_POST['puesto']) ? (int)$_POST['puesto'] : 0;

            if ($id <= 0 || $puesto <= 0) {
                fail('Datos invalidos para actualizar puesto.');
            }

            $done = $dao->actualizarPuesto($id, $puesto);
            if (!$done) {
                fail('No se pudo actualizar el puesto (equipo no encontrado).', 404);
            }

            ok(['message' => 'Puesto actualizado.']);
        }

        if ($accion === 'eliminar') {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            if ($id <= 0) {
                fail('ID invalido para eliminar.');
            }

            $done = $dao->eliminar($id);
            if (!$done) {
                fail('No se pudo eliminar (equipo no encontrado).', 404);
            }

            ok(['message' => 'Equipo eliminado.']);
        }

        fail('Accion POST no valida.');
    }

    fail('Metodo no permitido.', 405);
} catch (Throwable $e) {
    error_log('Error API: ' . $e->getMessage());
    error_log('Stack: ' . $e->getTraceAsString());
    fail('Error interno: ' . $e->getMessage(), 500);
}
