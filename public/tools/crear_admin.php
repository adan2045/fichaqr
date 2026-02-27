<?php
/**
 * Crea un usuario admin si no existe.
 * Uso: /tools/crear_admin.php?u=admin&p=123456
 * (Borrar después si el profe no lo quiere)
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

$u = trim($_GET['u'] ?? 'admin');
$p = trim($_GET['p'] ?? '123456');

if ($u === '' || $p === '') {
    echo "Faltan parámetros. Ej: ?u=admin&p=123456";
    exit;
}

$existe = DataBase::query("SELECT id FROM usuarios WHERE usuario = ? LIMIT 1", [$u], true);
if (!empty($existe)) {
    echo "Ya existe usuario '$u' (id {$existe[0]['id']}).";
    exit;
}

$hash = password_hash($p, PASSWORD_DEFAULT);
DataBase::execute("INSERT INTO usuarios (usuario, pass_hash, rol, activo) VALUES (?, ?, 'admin', 1)", [$u, $hash]);
echo "OK. Usuario admin creado: $u";
