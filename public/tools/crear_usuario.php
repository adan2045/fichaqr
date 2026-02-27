<?php
/**
 * Crea un usuario para un empleado.
 * Uso:
 *  /tools/crear_usuario.php?u=juan&p=123456&empleado_id=1&rol=empleado
 */

date_default_timezone_set('America/Argentina/Buenos_Aires');
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

chdir(dirname(__DIR__));
define("CORE_PATH", "app/core/");
define("APP_PATH", "app/");
define("ROOT_PATH", "public/");
require CORE_PATH . 'Autoloader.php';

$u = trim($_GET['u'] ?? '');
$p = trim($_GET['p'] ?? '');
$empleadoId = (int)($_GET['empleado_id'] ?? 0);
$rol = strtolower(trim($_GET['rol'] ?? 'empleado'));

if ($u === '' || $p === '' || $empleadoId <= 0) {
    echo "Faltan parámetros. Ej: ?u=juan&p=123456&empleado_id=1&rol=empleado";
    exit;
}

if (!in_array($rol, ['admin','jefe','empleado'], true)) {
    $rol = 'empleado';
}

$existe = DataBase::query("SELECT id FROM usuarios WHERE usuario = ? LIMIT 1", [$u], true);
if (!empty($existe)) {
    echo "Ya existe usuario '$u' (id {$existe[0]['id']}).";
    exit;
}

$emp = DataBase::query("SELECT id FROM empleados WHERE id = ? LIMIT 1", [$empleadoId], true);
if (empty($emp)) {
    echo "No existe empleado_id=$empleadoId";
    exit;
}

$hash = password_hash($p, PASSWORD_DEFAULT);
DataBase::execute("INSERT INTO usuarios (usuario, pass_hash, rol, empleado_id, activo) VALUES (?, ?, ?, ?, 1)", [$u, $hash, $rol, $empleadoId]);
echo "OK. Usuario creado: $u (rol=$rol, empleado_id=$empleadoId)";
