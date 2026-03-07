<?php
namespace app\controllers;

use \Controller;
use \Response;
use \App;
use app\models\EmpleadoModel;
use app\models\FichadaModel;
use app\models\UsuarioModel;

/**
 * Terminal de fichada
 * - actionIndex    : vista principal con panel PIN + QR próximamente
 * - actionBuscar   : AJAX GET  → busca empleado por legajo, devuelve JSON
 * - actionFicharpin: AJAX POST → valida PIN y registra fichada
 */
class TerminalController extends Controller
{
    public function actionIndex($var = null)
    {
        SesionController::requireLogin();
        static::path();

        $flash = $_SESSION['flash'] ?? null;
        if (isset($_SESSION['flash'])) unset($_SESSION['flash']);

        $head   = SiteController::head();
        $nav    = SiteController::nav();
        $footer = SiteController::footer();

        Response::render($this->viewDir(__NAMESPACE__), 'index', [
            'title'  => 'FichaQR · Fichar',
            'head'   => $head,
            'nav'    => $nav,
            'footer' => $footer,
            'ruta'   => App::baseUrl(),
            'flash'  => $flash,
        ]);
    }

    /** AJAX: busca empleado por legajo o número (sin PIN) */
    public function actionBuscar()
    {
        SesionController::requireLogin();
        header('Content-Type: application/json');

        $legajo = trim($_GET['legajo'] ?? '');
        if ($legajo === '') { echo json_encode(['ok' => false]); exit; }

        $em       = new EmpleadoModel();
        $empleado = $em->obtenerPorLegajo($legajo);

        if (!$empleado) { echo json_encode(['ok' => false, 'msg' => 'No encontrado']); exit; }

        $fm   = new FichadaModel();
        $hoy  = date('Y-m-d');
        $list = $fm->listarPorEmpleado((int)$empleado['id'], $hoy . ' 00:00:00', $hoy . ' 23:59:59');

        $ultima     = !empty($list) ? $list[0] : null;
        $ultimaHora = $ultima ? substr($ultima['fecha_hora'], 11, 8) : null;
        $estado     = $ultima ? ('Última: ' . ($ultima['tipo'] === 'IN' ? '↓ Entrada' : '↑ Salida') . ' ' . substr($ultima['fecha_hora'], 11, 5)) : 'Hoy aún no ha fichado';

        echo json_encode([
            'ok'         => true,
            'empleado'   => [
                'id'       => $empleado['id'],
                'nombre'   => $empleado['nombre'],
                'apellido' => $empleado['apellido'],
                'legajo'   => $empleado['legajo'],
            ],
            'estado'      => $estado,
            'ultima_hora' => $ultimaHora,
        ]);
        exit;
    }

    /** AJAX POST: valida PIN (últimos 4 del DNI) y registra fichada */
    public function actionFicharpin()
    {
        SesionController::requireLogin();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['ok' => false, 'msg' => 'Método inválido']); exit;
        }

        $legajo = trim($_POST['legajo'] ?? '');
        $pin    = trim($_POST['pin']    ?? '');
        $tipo   = strtoupper(trim($_POST['tipo'] ?? 'IN'));

        if (!in_array($tipo, ['IN', 'OUT'])) $tipo = 'IN';
        if (strlen($pin) !== 4 || !ctype_digit($pin)) {
            echo json_encode(['ok' => false, 'msg' => 'PIN inválido']); exit;
        }

        $em       = new EmpleadoModel();
        $empleado = $em->obtenerPorLegajo($legajo);
        if (!$empleado) {
            echo json_encode(['ok' => false, 'msg' => 'Empleado no encontrado']); exit;
        }

        // PIN = últimos 4 dígitos del DNI
        $dni     = preg_replace('/\D/', '', $empleado['dni'] ?? '');
        $pinReal = substr($dni, -4);

        if ($pin !== $pinReal) {
            echo json_encode(['ok' => false, 'msg' => 'PIN incorrecto']); exit;
        }

        // Registrar fichada
        $fm = new FichadaModel();
        $fm->crear((int)$empleado['id'], $tipo, 'PIN', null);

        $tipoLabel = $tipo === 'IN' ? '↓ Entrada' : '↑ Salida';
        echo json_encode([
            'ok'  => true,
            'msg' => "<strong>{$tipoLabel}</strong> registrada · {$empleado['apellido']} {$empleado['nombre']} · " . date('H:i'),
        ]);
        exit;
    }

    /** Registro por QR legacy (mantener compatibilidad) */
    private function parseCodigo(string $codigo): ?int
    {
        $codigo = trim($codigo);
        if ($codigo === '') return null;
        if (stripos($codigo, 'EMP:') === 0) $codigo = substr($codigo, 4);
        $codigo = trim($codigo);
        if (!ctype_digit($codigo)) return null;
        return (int)$codigo;
    }
}