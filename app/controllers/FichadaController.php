<?php
namespace app\controllers;

use \Controller;
use \Response;
use \App;
use app\models\EmpleadoModel;
use app\models\FichadaModel;

class FichadaController extends Controller
{
    /**
     * Panel empleado: Mis fichadas (con filtro)
     */
    public function actionMis()
    {
        SesionController::requireLogin();
        static::path();

        $empleadoId = SesionController::empleadoId();
        if (!$empleadoId) {
            // Si el usuario no está vinculado a empleado, lo tratamos como admin
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
            'title' => 'Mis fichadas',
            'head' => $head,
            'nav' => $nav,
            'footer' => $footer,
            'ruta' => App::baseUrl(),
            'desde' => $desde,
            'hasta' => $hasta,
            'lista' => $lista,
            'empleado' => $empleado,
            'flash' => $flash,
        ]);
    }

    /**
     * Registrar fichada desde terminal QR (o web)
     */
    public function actionRegistrarqr()
    {
        // Terminal puede funcionar sin login
        if (session_status() === PHP_SESSION_NONE) session_start();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . App::baseUrl() . '/terminal/index');
            exit;
        }

        $empleadoId = (int)($_POST['empleado_id'] ?? 0);
        $tipo = strtoupper(trim($_POST['tipo'] ?? ''));
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

    public function actionEditar()
    {
        SesionController::requireAdmin();
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location: ' . App::baseUrl() . '/admin/gestion');
            exit;
        }

        $fm = new FichadaModel();
        $fichada = $fm->obtenerPorId($id);
        if (!$fichada) {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Fichada no encontrada'];
            header('Location: ' . App::baseUrl() . '/admin/gestion');
            exit;
        }

        static::path();
        $head = SiteController::head();
        $nav = SiteController::nav();
        $footer = SiteController::footer();

        Response::render($this->viewDir(__NAMESPACE__), 'editar', [
            'title' => 'Editar fichada',
            'head' => $head,
            'nav' => $nav,
            'footer' => $footer,
            'ruta' => App::baseUrl(),
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

        $id = (int)($_POST['id'] ?? 0);
        $fecha_hora = trim($_POST['fecha_hora'] ?? '');
        $tipo = strtoupper(trim($_POST['tipo'] ?? ''));
        $comentario = trim($_POST['comentario'] ?? '');

        if ($id <= 0 || $fecha_hora === '' || !in_array($tipo, ['IN','OUT'], true)) {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Datos inválidos para actualizar'];
            header('Location: ' . App::baseUrl() . '/fichada/editar?id=' . $id);
            exit;
        }

        $fm = new FichadaModel();
        $fm->actualizar($id, $fecha_hora, $tipo, $comentario !== '' ? $comentario : null, (int)($_SESSION['user_id'] ?? 0));
        $_SESSION['flash'] = ['type' => 'ok', 'msg' => 'Fichada actualizada'];
        header('Location: ' . App::baseUrl() . '/admin/gestion');
        exit;
    }

    public function actionEliminar()
    {
        SesionController::requireAdmin();
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            $fm = new FichadaModel();
            $fm->eliminar($id, (int)($_SESSION['user_id'] ?? 0));
            $_SESSION['flash'] = ['type' => 'ok', 'msg' => 'Fichada eliminada'];
        }
        header('Location: ' . App::baseUrl() . '/admin/gestion');
        exit;
    }
}
