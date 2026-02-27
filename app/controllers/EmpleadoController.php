<?php
namespace app\controllers;

use \Controller;
use \Response;
use \App;
use app\models\EmpleadoModel;

class EmpleadoController extends Controller
{
    public function actionListado()
    {
        SesionController::requireAdmin();
        static::path();

        $head = SiteController::head();
        $nav = SiteController::nav();
        $footer = SiteController::footer();

        $model = new EmpleadoModel();
        $empleados = $model->obtenerTodos(false);

        $flash = $_SESSION['flash'] ?? null;
        if (isset($_SESSION['flash'])) unset($_SESSION['flash']);

        Response::render($this->viewDir(__NAMESPACE__), 'listado', [
            'title' => 'Empleados',
            'head' => $head,
            'nav' => $nav,
            'footer' => $footer,
            'ruta' => App::baseUrl(),
            'empleados' => $empleados,
            'flash' => $flash,
        ]);
    }

    public function actionFormulario()
    {
        SesionController::requireAdmin();
        static::path();

        $head = SiteController::head();
        $nav = SiteController::nav();
        $footer = SiteController::footer();

        Response::render($this->viewDir(__NAMESPACE__), 'formulario', [
            'title' => 'Nuevo empleado',
            'head' => $head,
            'nav' => $nav,
            'footer' => $footer,
            'ruta' => App::baseUrl(),
        ]);
    }

    public function actionGuardar()
    {
        SesionController::requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . App::baseUrl() . '/empleado/listado');
            exit;
        }

        $legajo = trim($_POST['legajo'] ?? '');
        $nombre = trim($_POST['nombre'] ?? '');
        $apellido = trim($_POST['apellido'] ?? '');
        $dni = trim($_POST['dni'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $activo = isset($_POST['activo']) ? 1 : 0;

        // Validación mínima
        if ($nombre === '' || $apellido === '') {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Nombre y apellido son obligatorios'];
            header('Location: ' . App::baseUrl() . '/empleado/formulario');
            exit;
        }

        $model = new EmpleadoModel();
        try {
            $id = $model->crear($legajo !== '' ? $legajo : null, $nombre, $apellido, $dni !== '' ? $dni : null, $email !== '' ? $email : null, $activo);
            $_SESSION['flash'] = ['type' => 'ok', 'msg' => "Empleado creado (#$id)"];
        } catch (\Throwable $e) {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Error: ' . $e->getMessage()];
        }

        header('Location: ' . App::baseUrl() . '/empleado/listado');
        exit;
    }

    public function actionModificar()
    {
        SesionController::requireAdmin();
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location: ' . App::baseUrl() . '/empleado/listado');
            exit;
        }

        $model = new EmpleadoModel();
        $empleado = $model->obtenerPorId($id);
        if (!$empleado) {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Empleado no encontrado'];
            header('Location: ' . App::baseUrl() . '/empleado/listado');
            exit;
        }

        static::path();
        $head = SiteController::head();
        $nav = SiteController::nav();
        $footer = SiteController::footer();

        Response::render($this->viewDir(__NAMESPACE__), 'modificar', [
            'title' => 'Modificar empleado',
            'head' => $head,
            'nav' => $nav,
            'footer' => $footer,
            'ruta' => App::baseUrl(),
            'empleado' => $empleado,
        ]);
    }

    public function actionActualizar()
    {
        SesionController::requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . App::baseUrl() . '/empleado/listado');
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);
        $legajo = trim($_POST['legajo'] ?? '');
        $nombre = trim($_POST['nombre'] ?? '');
        $apellido = trim($_POST['apellido'] ?? '');
        $dni = trim($_POST['dni'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $activo = isset($_POST['activo']) ? 1 : 0;

        if ($id <= 0 || $nombre === '' || $apellido === '') {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Datos inválidos'];
            header('Location: ' . App::baseUrl() . '/empleado/listado');
            exit;
        }

        $model = new EmpleadoModel();
        try {
            $model->actualizar($id, $legajo !== '' ? $legajo : null, $nombre, $apellido, $dni !== '' ? $dni : null, $email !== '' ? $email : null, $activo);
            $_SESSION['flash'] = ['type' => 'ok', 'msg' => 'Empleado actualizado'];
        } catch (\Throwable $e) {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Error: ' . $e->getMessage()];
        }

        header('Location: ' . App::baseUrl() . '/empleado/listado');
        exit;
    }

    public function actionEliminar()
    {
        SesionController::requireAdmin();
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            $model = new EmpleadoModel();
            $model->desactivar($id);
            $_SESSION['flash'] = ['type' => 'ok', 'msg' => 'Empleado desactivado'];
        }
        header('Location: ' . App::baseUrl() . '/empleado/listado');
        exit;
    }
}
