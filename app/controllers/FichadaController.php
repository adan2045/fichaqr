<?php
namespace app\controllers;

use \Controller;
use \Response;
use \App;
use app\models\EmpleadoModel;
use app\models\FichadaModel;

class FichadaController extends Controller
{
    public function actionMis()
    {
        SesionController::requireLogin();
        static::path();

        $empleadoId = SesionController::empleadoId();
        if (!$empleadoId) {
            header('Location: ' . App::baseUrl() . '/admin/gestion');
            exit;
        }

        $desde = $_GET['desde'] ?? date('Y-m-01');
        $hasta = $_GET['hasta'] ?? date('Y-m-d');
        $desdeDT = $desde . ' 00:00:00';
        $hastaDT = $hasta . ' 23:59:59';

        $fm = new FichadaModel();
        $lista = $fm->listarPorEmpleado($empleadoId, $desdeDT, $hastaDT);

        $em = new EmpleadoModel();
        $empleado = $em->obtenerPorId($empleadoId);

        $flash = $_SESSION['flash'] ?? null;
        if (isset($_SESSION['flash'])) unset($_SESSION['flash']);

        $head = SiteController::head();
        $nav = SiteController::nav();
        $footer = SiteController::footer();

        Response::render($this->viewDir(__NAMESPACE__), 'mis', [
            'title'    => 'Mis fichadas',
            'head'     => $head,
            'nav'      => $nav,
            'footer'   => $footer,
            'ruta'     => App::baseUrl(),
            'desde'    => $desde,
            'hasta'    => $hasta,
            'lista'    => $lista,
            'empleado' => $empleado,
            'flash'    => $flash,
        ]);
    }

    public function actionRegistrarqr()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . App::baseUrl() . '/terminal/index');
            exit;
        }

        $empleadoId = (int)($_POST['empleado_id'] ?? 0);
        $tipo       = strtoupper(trim($_POST['tipo'] ?? ''));
        $comentario = trim($_POST['comentario'] ?? '');

        if ($empleadoId <= 0 || !in_array($tipo, ['IN','OUT'], true)) {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Datos inválidos para fichar'];
            header('Location: ' . App::baseUrl() . '/terminal/index');
            exit;
        }

        $creadoPor = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
        $fm = new FichadaModel();
        try {
            $fm->crear($empleadoId, $tipo, 'qr', $creadoPor, $comentario !== '' ? $comentario : null);
            $_SESSION['flash'] = ['type' => 'ok', 'msg' => 'Fichada registrada'];
        } catch (\Throwable $e) {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Error: ' . $e->getMessage()];
        }

        header('Location: ' . App::baseUrl() . '/terminal/index?codigo=EMP:' . $empleadoId);
        exit;
    }

    /** Nueva fichada manual desde panel admin */
    public function actionNueva()
    {
        SesionController::requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . App::baseUrl() . '/admin/gestion');
            exit;
        }

        $empleadoId = (int)($_POST['empleado_id'] ?? 0);
        $fechaHora  = trim($_POST['fecha_hora'] ?? '');
        $tipo       = strtoupper(trim($_POST['tipo'] ?? ''));
        $comentario = trim($_POST['comentario'] ?? '');

        if ($empleadoId <= 0 || $fechaHora === '' || !in_array($tipo, ['IN','OUT'], true)) {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Datos inválidos para crear fichada'];
            header('Location: ' . App::baseUrl() . '/admin/gestion');
            exit;
        }

        // datetime-local = "2026-03-04T18:49" → "2026-03-04 18:49:00"
        $fechaHora = str_replace('T', ' ', $fechaHora);
        if (strlen($fechaHora) === 16) $fechaHora .= ':00';

        $fm = new FichadaModel();
        try {
            $fm->crearManual($empleadoId, $fechaHora, $tipo, 'admin', (int)($_SESSION['user_id'] ?? 0), $comentario !== '' ? $comentario : null);
            $_SESSION['flash'] = ['type' => 'ok', 'msg' => 'Fichada creada correctamente'];
        } catch (\Throwable $e) {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Error: ' . $e->getMessage()];
        }

        header('Location: ' . App::baseUrl() . '/admin/gestion');
        exit;
    }

    public function actionEditar()
    {
        SesionController::requireAdmin();
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location: ' . App::baseUrl() . '/admin/gestion');
            exit;
        }

        $fm      = new FichadaModel();
        $fichada = $fm->obtenerPorId($id);
        if (!$fichada) {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Fichada no encontrada'];
            header('Location: ' . App::baseUrl() . '/admin/gestion');
            exit;
        }

        static::path();
        $head   = SiteController::head();
        $nav    = SiteController::nav();
        $footer = SiteController::footer();

        Response::render($this->viewDir(__NAMESPACE__), 'editar', [
            'title'   => 'Editar fichada',
            'head'    => $head,
            'nav'     => $nav,
            'footer'  => $footer,
            'ruta'    => App::baseUrl(),
            'fichada' => $fichada,
        ]);
    }

    public function actionActualizar()
    {
        SesionController::requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . App::baseUrl() . '/admin/gestion');
            exit;
        }

        $id         = (int)($_POST['id'] ?? 0);
        $fechaHora  = str_replace('T', ' ', trim($_POST['fecha_hora'] ?? ''));
        if (strlen($fechaHora) === 16) $fechaHora .= ':00';
        $tipo       = strtoupper(trim($_POST['tipo'] ?? ''));
        $comentario = trim($_POST['comentario'] ?? '');

        if ($id <= 0 || $fechaHora === '' || !in_array($tipo, ['IN','OUT'], true)) {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Datos inválidos para actualizar'];
            header('Location: ' . App::baseUrl() . '/admin/gestion');
            exit;
        }

        $fm = new FichadaModel();
        $fm->actualizar($id, $fechaHora, $tipo, $comentario !== '' ? $comentario : null, (int)($_SESSION['user_id'] ?? 0));
        $_SESSION['flash'] = ['type' => 'ok', 'msg' => 'Fichada #' . $id . ' actualizada'];
        header('Location: ' . App::baseUrl() . '/admin/gestion');
        exit;
    }

    public function actionEliminar()
    {
        SesionController::requireAdmin();
        if (session_status() === PHP_SESSION_NONE) session_start();

        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($id > 0) {
            $fm = new FichadaModel();
            $fm->eliminar($id, (int)($_SESSION['user_id'] ?? 0));
            $_SESSION['flash'] = ['type' => 'ok', 'msg' => 'Fichada #' . $id . ' eliminada'];
        }
        header('Location: ' . App::baseUrl() . '/admin/gestion');
        exit;
    }
}
