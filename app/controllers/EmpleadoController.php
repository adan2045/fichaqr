<?php
namespace app\controllers;

use \Controller;
use \Response;
use \App;
use app\models\EmpleadoModel;

class EmpleadoController extends Controller
{
    public function actionFormulario()
    {
        SesionController::requireAdmin();
        static::path();

        $head   = SiteController::head();
        $nav    = SiteController::nav();
        $footer = SiteController::footer();

        $errores = [];
        $datos   = ['legajo'=>'','nombre'=>'','apellido'=>'','dni'=>'','email'=>'','activo'=>1];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos['legajo']   = trim($_POST['legajo']   ?? '');
            $datos['nombre']   = trim($_POST['nombre']   ?? '');
            $datos['apellido'] = trim($_POST['apellido'] ?? '');
            $datos['dni']      = trim($_POST['dni']      ?? '');
            $datos['email']    = trim($_POST['email']    ?? '');
            $datos['activo']   = isset($_POST['activo']) ? 1 : 0;

            if ($datos['nombre']   === '') $errores['nombre']   = 'Requerido';
            if ($datos['apellido'] === '') $errores['apellido'] = 'Requerido';

            if (empty($errores)) {
                $em = new EmpleadoModel();
                $em->crear(
                    $datos['legajo']   !== '' ? $datos['legajo']   : null,
                    $datos['nombre'],
                    $datos['apellido'],
                    $datos['dni']      !== '' ? $datos['dni']      : null,
                    $datos['email']    !== '' ? $datos['email']    : null,
                    $datos['activo']
                );
                $_SESSION['flash'] = ['type' => 'ok', 'msg' => 'Empleado creado correctamente'];
                header('Location: ' . App::baseUrl() . '/empleado/listado');
                exit;
            }
        }

        Response::render($this->viewDir(__NAMESPACE__), 'formulario', [
            'title'   => 'Nuevo empleado',
            'head'    => $head,
            'nav'     => $nav,
            'footer'  => $footer,
            'ruta'    => App::baseUrl(),
            'datos'   => $datos,
            'errores' => $errores,
        ]);
    }

    public function actionListado()
    {
        SesionController::requireAdmin();
        static::path();

        $head   = SiteController::head();
        $nav    = SiteController::nav();
        $footer = SiteController::footer();

        $em        = new EmpleadoModel();
        $empleados = $em->obtenerTodos(false);

        $flash = $_SESSION['flash'] ?? null;
        if (isset($_SESSION['flash'])) unset($_SESSION['flash']);

        Response::render($this->viewDir(__NAMESPACE__), 'listado', [
            'title'     => 'Empleados',
            'head'      => $head,
            'nav'       => $nav,
            'footer'    => $footer,
            'ruta'      => App::baseUrl(),
            'empleados' => $empleados,
            'flash'     => $flash,
        ]);
    }

    public function actionModificar()
    {
        SesionController::requireAdmin();
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location: ' . App::baseUrl() . '/empleado/listado');
            exit;
        }

        $em       = new EmpleadoModel();
        $empleado = $em->obtenerPorId($id);
        if (!$empleado) {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Empleado no encontrado'];
            header('Location: ' . App::baseUrl() . '/empleado/listado');
            exit;
        }

        $errores = [];
        $datos   = $empleado;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos['legajo']   = trim($_POST['legajo']   ?? '');
            $datos['nombre']   = trim($_POST['nombre']   ?? '');
            $datos['apellido'] = trim($_POST['apellido'] ?? '');
            $datos['dni']      = trim($_POST['dni']      ?? '');
            $datos['email']    = trim($_POST['email']    ?? '');
            $datos['activo']   = isset($_POST['activo']) ? 1 : 0;

            if ($datos['nombre']   === '') $errores['nombre']   = 'Requerido';
            if ($datos['apellido'] === '') $errores['apellido'] = 'Requerido';

            if (empty($errores)) {
                $em->actualizar(
                    $id,
                    $datos['legajo']   !== '' ? $datos['legajo']   : null,
                    $datos['nombre'],
                    $datos['apellido'],
                    $datos['dni']      !== '' ? $datos['dni']      : null,
                    $datos['email']    !== '' ? $datos['email']    : null,
                    $datos['activo']
                );
                $_SESSION['flash'] = ['type' => 'ok', 'msg' => 'Empleado actualizado'];
                header('Location: ' . App::baseUrl() . '/empleado/listado');
                exit;
            }
        }

        static::path();
        $head   = SiteController::head();
        $nav    = SiteController::nav();
        $footer = SiteController::footer();

        Response::render($this->viewDir(__NAMESPACE__), 'modificar', [
            'title'    => 'Modificar empleado',
            'head'     => $head,
            'nav'      => $nav,
            'footer'   => $footer,
            'ruta'     => App::baseUrl(),
            'id'       => $id,
            'datos'    => $datos,
            'errores'  => $errores,
        ]);
    }

    /** Gestión de documentos del empleado */
    public function actionDocs()
    {
        SesionController::requireAdmin();
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location: ' . App::baseUrl() . '/empleado/listado');
            exit;
        }

        $em       = new EmpleadoModel();
        $empleado = $em->obtenerPorId($id);
        if (!$empleado) {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Empleado no encontrado'];
            header('Location: ' . App::baseUrl() . '/empleado/listado');
            exit;
        }

        // Directorio de documentos
        $docsBase  = realpath(__DIR__ . '/../../public') . '/docs/empleados/' . $id;
        $certDir   = $docsBase . '/certificados';
        $reciboDir = $docsBase . '/recibos';

        foreach ([$certDir, $reciboDir] as $dir) {
            if (!is_dir($dir)) mkdir($dir, 0755, true);
        }

        $flash = $_SESSION['flash'] ?? null;
        if (isset($_SESSION['flash'])) unset($_SESSION['flash']);

        // Subir documento
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['documento'])) {
            $tipo    = $_POST['tipo_doc'] ?? 'certificado';
            $dir     = $tipo === 'recibo' ? $reciboDir : $certDir;
            $archivo = $_FILES['documento'];

            if ($archivo['error'] === UPLOAD_ERR_OK) {
                $ext      = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
                $allowed  = ['pdf','jpg','jpeg','png'];
                if (!in_array($ext, $allowed)) {
                    $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Solo se permiten PDF, JPG, PNG'];
                } else {
                    $nombre = date('Ymd_His') . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $archivo['name']);
                    move_uploaded_file($archivo['tmp_name'], $dir . '/' . $nombre);
                    $_SESSION['flash'] = ['type' => 'ok', 'msg' => 'Documento subido correctamente'];
                }
            } else {
                $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Error al subir el archivo'];
            }
            header('Location: ' . App::baseUrl() . '/empleado/docs?id=' . $id);
            exit;
        }

        // Eliminar documento
        if (isset($_GET['del'])) {
            $tipo = $_GET['tipo'] ?? 'certificado';
            $dir  = $tipo === 'recibo' ? $reciboDir : $certDir;
            $safe = basename($_GET['del']);
            $fp   = $dir . '/' . $safe;
            if (file_exists($fp)) unlink($fp);
            $_SESSION['flash'] = ['type' => 'ok', 'msg' => 'Documento eliminado'];
            header('Location: ' . App::baseUrl() . '/empleado/docs?id=' . $id);
            exit;
        }

        $certificados = file_exists($certDir)   ? array_values(array_filter(scandir($certDir),   fn($f) => !in_array($f,['.','..','.gitkeep']))) : [];
        $recibos      = file_exists($reciboDir)  ? array_values(array_filter(scandir($reciboDir), fn($f) => !in_array($f,['.','..','.gitkeep']))) : [];

        static::path();
        $head   = SiteController::head();
        $nav    = SiteController::nav();
        $footer = SiteController::footer();

        Response::render($this->viewDir(__NAMESPACE__), 'docs', [
            'title'        => 'Documentos · ' . $empleado['apellido'] . ' ' . $empleado['nombre'],
            'head'         => $head,
            'nav'          => $nav,
            'footer'       => $footer,
            'ruta'         => App::baseUrl(),
            'empleado'     => $empleado,
            'certificados' => $certificados,
            'recibos'      => $recibos,
            'flash'        => $flash,
        ]);
    }

    /** Mis documentos — vista del empleado (solo lectura) */
    public function actionMisdocs()
    {
        SesionController::requireLogin();

        $empleadoId = SesionController::empleadoId();
        if (!$empleadoId) {
            header('Location: ' . App::baseUrl() . '/admin/gestion');
            exit;
        }

        $em       = new EmpleadoModel();
        $empleado = $em->obtenerPorId($empleadoId);

        $docsBase  = realpath(__DIR__ . '/../../public') . '/docs/empleados/' . $empleadoId;
        $certDir   = $docsBase . '/certificados';
        $reciboDir = $docsBase . '/recibos';

        $certificados = (is_dir($certDir))   ? array_values(array_filter(scandir($certDir),   fn($f) => !in_array($f,['.','..','.gitkeep']))) : [];
        $recibos      = (is_dir($reciboDir))  ? array_values(array_filter(scandir($reciboDir), fn($f) => !in_array($f,['.','..','.gitkeep']))) : [];

        static::path();
        $head   = SiteController::head();
        $nav    = SiteController::nav();
        $footer = SiteController::footer();

        Response::render($this->viewDir(__NAMESPACE__), 'misdocs', [
            'title'        => 'Mis documentos',
            'head'         => $head,
            'nav'          => $nav,
            'footer'       => $footer,
            'ruta'         => App::baseUrl(),
            'empleado'     => $empleado,
            'certificados' => $certificados,
            'recibos'      => $recibos,
        ]);
    }

}