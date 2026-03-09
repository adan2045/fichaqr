<?php
namespace app\controllers;

use \Controller;
use \Response;
use \App;
use app\models\EmpleadoModel;
use app\models\FichadaModel;

class TerminalController extends Controller
{
    public function actionIndex($var = null)
    {
        SesionController::requireLogin();
        static::path();

        if (session_status() === PHP_SESSION_NONE) session_start();

        $rol       = strtolower(trim($_SESSION['user_rol'] ?? ''));
        $esAdmin   = in_array($rol, ['admin', 'jefe'], true);
        $empleadoId = (int)($_SESSION['empleado_id'] ?? 0);

        $flash = $_SESSION['flash'] ?? null;
        if (isset($_SESSION['flash'])) unset($_SESSION['flash']);

        $head   = SiteController::head();
        $nav    = SiteController::nav();
        $footer = SiteController::footer();

        $em = new EmpleadoModel();
        $fm = new FichadaModel();
        $hoy = date('Y-m-d');

        // ── EMPLEADO: carga sus propios datos automáticamente ──
        $empleadoPropio = null;
        $fichadasHoy    = [];
        if (!$esAdmin && $empleadoId) {
            $empleadoPropio = $em->obtenerPorId($empleadoId);
            if ($empleadoPropio) {
                $fichadasHoy = $fm->listarPorEmpleado(
                    $empleadoId,
                    $hoy . ' 00:00:00',
                    $hoy . ' 23:59:59'
                );
            }
        }

        // ── ADMIN/JEFE: buscador por legajo ──
        $codigo   = '';
        $empleado = null;
        $ultimas  = [];
        if ($esAdmin) {
            $codigo = trim($_GET['codigo'] ?? '');
            if ($codigo !== '') {
                $id = $this->parseCodigo($codigo);
                if ($id) {
                    $empleado = $em->obtenerPorId($id);
                    if ($empleado) {
                        $ultimas = $fm->listarPorEmpleado(
                            (int)$empleado['id'],
                            $hoy . ' 00:00:00',
                            $hoy . ' 23:59:59'
                        );
                    }
                }
            }
        }

        Response::render($this->viewDir(__NAMESPACE__), 'index', [
            'title'          => 'FichaQR · Fichar',
            'head'           => $head,
            'nav'            => $nav,
            'footer'         => $footer,
            'ruta'           => App::baseUrl(),
            'flash'          => $flash,
            'esAdmin'        => $esAdmin,
            // empleado
            'empleadoPropio' => $empleadoPropio,
            'fichadasHoy'    => $fichadasHoy,
            // admin
            'codigo'         => $codigo,
            'empleado'       => $empleado,
            'ultimas'        => $ultimas,
        ]);
    }

    /** AJAX GET: busca empleado por legajo → JSON (solo admin/jefe) */
    public function actionBuscar()
    {
        SesionController::requireLogin();
        header('Content-Type: application/json');
        $legajo   = trim($_GET['legajo'] ?? '');
        if ($legajo === '') { echo json_encode(['ok'=>false]); exit; }
        $em       = new EmpleadoModel();
        $empleado = $em->obtenerPorLegajo($legajo);
        if (!$empleado) { echo json_encode(['ok'=>false,'msg'=>'No encontrado']); exit; }
        $fm  = new FichadaModel();
        $hoy = date('Y-m-d');
        $list = $fm->listarPorEmpleado((int)$empleado['id'], $hoy.' 00:00:00', $hoy.' 23:59:59');
        $ultima  = !empty($list) ? $list[0] : null;
        $estado  = $ultima
            ? ('Última: '.($ultima['tipo']==='IN'?'↓ Entrada':'↑ Salida').' '.substr($ultima['fecha_hora'],11,5))
            : 'Hoy aún no ha fichado';
        echo json_encode([
            'ok'       => true,
            'empleado' => ['id'=>$empleado['id'],'nombre'=>$empleado['nombre'],
                           'apellido'=>$empleado['apellido'],'legajo'=>$empleado['legajo']],
            'estado'   => $estado,
            'ultima_hora' => $ultima ? substr($ultima['fecha_hora'],11,8) : null,
        ]);
        exit;
    }

    /** AJAX POST: valida PIN y registra fichada (solo admin/jefe, para buscar por legajo) */
    public function actionFicharpin()
    {
        SesionController::requireLogin();
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['ok'=>false,'msg'=>'Método inválido']); exit;
        }
        $legajo = trim($_POST['legajo'] ?? '');
        $pin    = trim($_POST['pin']    ?? '');
        $tipo   = strtoupper(trim($_POST['tipo'] ?? 'IN'));
        if (!in_array($tipo,['IN','OUT'])) $tipo = 'IN';
        if (strlen($pin)!==4 || !ctype_digit($pin)) {
            echo json_encode(['ok'=>false,'msg'=>'PIN inválido']); exit;
        }
        $em = new EmpleadoModel();
        $empleado = $em->obtenerPorLegajo($legajo);
        if (!$empleado) { echo json_encode(['ok'=>false,'msg'=>'Empleado no encontrado']); exit; }
        $dni     = preg_replace('/\D/','',$empleado['dni']??'');
        $pinReal = substr($dni,-4);
        if ($pin !== $pinReal) { echo json_encode(['ok'=>false,'msg'=>'PIN incorrecto']); exit; }
        $fm = new FichadaModel();
        $fm->crear((int)$empleado['id'], $tipo, 'PIN', null);
        $label = $tipo==='IN' ? '↓ Entrada' : '↑ Salida';
        echo json_encode([
            'ok'  => true,
            'msg' => "<strong>{$label}</strong> registrada · {$empleado['apellido']} {$empleado['nombre']} · ".date('H:i'),
        ]);
        exit;
    }

    /** POST directo desde botón del empleado (sin PIN, ya está logueado) */
    public function actionFicharpropio()
    {
        SesionController::requireLogin();
        if (session_status() === PHP_SESSION_NONE) session_start();

        $rol = strtolower(trim($_SESSION['user_rol'] ?? ''));
        if (in_array($rol, ['admin','jefe'], true)) {
            header('Location: ' . App::baseUrl() . '/terminal/index'); exit;
        }

        $empleadoId = (int)($_SESSION['empleado_id'] ?? 0);
        if (!$empleadoId) {
            header('Location: ' . App::baseUrl() . '/terminal/index'); exit;
        }

        $tipo = strtoupper(trim($_POST['tipo'] ?? 'IN'));
        if (!in_array($tipo, ['IN','OUT'])) $tipo = 'IN';

        $fm = new FichadaModel();
        $fm->crear($empleadoId, $tipo, 'web', null);

        $label = $tipo === 'IN' ? 'Entrada' : 'Salida';
        $_SESSION['flash'] = ['type'=>'ok', 'msg'=> "✅ {$label} registrada · " . date('H:i:s')];
        header('Location: ' . App::baseUrl() . '/terminal/index');
        exit;
    }

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