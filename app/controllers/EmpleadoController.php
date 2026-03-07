<?php
namespace app\controllers;

use \Controller;
use \Response;
use \App;
use app\models\EmpleadoModel;
use app\models\DocumentoModel;
use app\models\UsuarioModel;

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
            if ($datos['dni']      === '') $errores['dni']      = 'Requerido (se usa como contraseña inicial)';

            if (empty($errores)) {
                try {
                    $em = new EmpleadoModel();

                    if ($datos['legajo'] === '') {
                        $datos['legajo'] = $em->generarLegajo();
                    }

                    $nuevoId = $em->crear(
                        $datos['legajo'],
                        $datos['nombre'],
                        $datos['apellido'],
                        $datos['dni'],
                        $datos['email'] !== '' ? $datos['email'] : null,
                        $datos['activo']
                    );

                    // Crear usuario automático con rol empleado
                    $um           = new UsuarioModel();
                    $loginUsuario = $datos['email'] !== '' ? $datos['email'] : $datos['legajo'];
                    $passDefault  = $datos['dni'];
                    $um->crear($loginUsuario, $passDefault, 'empleado', $nuevoId);

                    $_SESSION['flash'] = [
                        'type' => 'ok',
                        'msg'  => "Empleado creado. Usuario: <strong>{$loginUsuario}</strong> · Contraseña: <strong>{$passDefault}</strong>"
                    ];
                    header('Location: ' . App::baseUrl() . '/empleado/listado');
                    exit;
                } catch (\Exception $e) {
                    $errores['db'] = 'Error al guardar: ' . $e->getMessage();
                }
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
                try {
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
                } catch (\Exception $e) {
                    $errores['db'] = 'Error al guardar: ' . $e->getMessage();
                }
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

    /** Vista global de documentos — admin/jefe ve todos los empleados */
    public function actionDocs()
    {
        SesionController::requireAdmin();

        $em         = new EmpleadoModel();
        $dm         = new DocumentoModel();
        $empleados  = $em->obtenerTodos(true);
        $publicPath = realpath(__DIR__ . '/../../public');
        $usuarioId  = (int)($_SESSION['user_id'] ?? 0);

        $flash = $_SESSION['flash'] ?? null;
        if (isset($_SESSION['flash'])) unset($_SESSION['flash']);

        // ── Subir documento ──
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['documento'])) {
            $eid     = (int)($_POST['empleado_id'] ?? 0);
            $tipo    = in_array($_POST['tipo_doc'] ?? '', ['certificado','recibo'])
                       ? $_POST['tipo_doc'] : 'certificado';
            $archivo = $_FILES['documento'];
            $dir     = $publicPath . '/docs/empleados/' . $eid . '/' . $tipo . 's';

            if (!is_dir($dir)) mkdir($dir, 0755, true);

            if ($eid > 0 && $archivo['error'] === UPLOAD_ERR_OK) {
                $ext     = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
                $allowed = ['pdf','jpg','jpeg','png'];
                if (in_array($ext, $allowed)) {
                    $nombre = date('Ymd_His') . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $archivo['name']);
                    $ruta   = '/docs/empleados/' . $eid . '/' . $tipo . 's/' . $nombre;
                    move_uploaded_file($archivo['tmp_name'], $dir . '/' . $nombre);
                    $dm->crear($eid, $tipo, $nombre, $ruta, $usuarioId);
                    $_SESSION['flash'] = ['type' => 'ok', 'msg' => 'Documento subido correctamente'];
                } else {
                    $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Solo PDF, JPG, PNG'];
                }
            }
            header('Location: ' . App::baseUrl() . '/empleado/docs');
            exit;
        }

        // ── Eliminar documento ──
        if (isset($_GET['del'])) {
            $eid  = (int)($_GET['eid'] ?? 0);
            $tipo = in_array($_GET['tipo'] ?? '', ['certificado','recibo']) ? $_GET['tipo'] : 'certificado';
            $safe = basename($_GET['del']);
            $fp   = $publicPath . '/docs/empleados/' . $eid . '/' . $tipo . 's/' . $safe;
            if (file_exists($fp)) unlink($fp);
            $dm->eliminarPorArchivo($eid, $safe);
            $_SESSION['flash'] = ['type' => 'ok', 'msg' => 'Documento eliminado'];
            header('Location: ' . App::baseUrl() . '/empleado/docs');
            exit;
        }

        // ── Cargar docs indexados por empleado_id ──
        $certificados = [];
        $recibos      = [];

        foreach ($dm->listarTodos('certificado') as $doc) {
            $doc['nombre_display'] = preg_replace('/^\d{8}_\d{6}_/', '', $doc['nombre_archivo']);
            $certificados[$doc['empleado_id']][] = $doc;
        }
        foreach ($dm->listarTodos('recibo') as $doc) {
            $doc['nombre_display'] = preg_replace('/^\d{8}_\d{6}_/', '', $doc['nombre_archivo']);
            $recibos[$doc['empleado_id']][] = $doc;
        }

        static::path();
        $head   = SiteController::head();
        $nav    = SiteController::nav();
        $footer = SiteController::footer();

        Response::render($this->viewDir(__NAMESPACE__), 'docs', [
            'title'        => 'Gestión de documentos',
            'head'         => $head,
            'nav'          => $nav,
            'footer'       => $footer,
            'ruta'         => App::baseUrl(),
            'empleados'    => $empleados,
            'certificados' => $certificados,
            'recibos'      => $recibos,
            'flash'        => $flash,
        ]);
    }

    /** Mis documentos — el empleado sube/elimina sus certificados; recibos solo lectura */
    public function actionMisdocs()
    {
        SesionController::requireLogin();

        $empleadoId = SesionController::empleadoId();
        if (!$empleadoId) {
            header('Location: ' . App::baseUrl() . '/admin/gestion');
            exit;
        }

        $em         = new EmpleadoModel();
        $dm         = new DocumentoModel();
        $empleado   = $em->obtenerPorId($empleadoId);
        $usuarioId  = (int)($_SESSION['user_id'] ?? 0);
        $publicPath = realpath(__DIR__ . '/../../public');
        $certDir    = $publicPath . '/docs/empleados/' . $empleadoId . '/certificados';
        $reciboDir  = $publicPath . '/docs/empleados/' . $empleadoId . '/recibos';

        foreach ([$certDir, $reciboDir] as $dir) {
            if (!is_dir($dir)) mkdir($dir, 0755, true);
        }

        $flash = $_SESSION['flash'] ?? null;
        if (isset($_SESSION['flash'])) unset($_SESSION['flash']);

        // Subir certificado propio
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['documento'])) {
            $archivo = $_FILES['documento'];
            if ($archivo['error'] === UPLOAD_ERR_OK) {
                $ext     = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
                $allowed = ['pdf','jpg','jpeg','png'];
                if (in_array($ext, $allowed)) {
                    $nombre = date('Ymd_His') . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $archivo['name']);
                    $ruta   = '/docs/empleados/' . $empleadoId . '/certificados/' . $nombre;
                    move_uploaded_file($archivo['tmp_name'], $certDir . '/' . $nombre);
                    $dm->crear($empleadoId, 'certificado', $nombre, $ruta, $usuarioId);
                    $_SESSION['flash'] = ['type' => 'ok', 'msg' => 'Certificado subido correctamente'];
                } else {
                    $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Solo se permiten PDF, JPG, PNG'];
                }
            } else {
                $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Error al subir el archivo'];
            }
            header('Location: ' . App::baseUrl() . '/empleado/misdocs');
            exit;
        }

        // Eliminar certificado propio
        if (isset($_GET['del'])) {
            $safe = basename($_GET['del']);
            $fp   = $certDir . '/' . $safe;
            if (file_exists($fp)) unlink($fp);
            $dm->eliminarPorArchivo($empleadoId, $safe);
            $_SESSION['flash'] = ['type' => 'ok', 'msg' => 'Certificado eliminado'];
            header('Location: ' . App::baseUrl() . '/empleado/misdocs');
            exit;
        }

        // Traer desde DB con fecha y quién subió
        $rawCerts   = $dm->listarPorEmpleado($empleadoId, 'certificado');
        $rawRecibos = $dm->listarPorEmpleado($empleadoId, 'recibo');

        foreach ($rawCerts   as &$d) $d['nombre_display'] = preg_replace('/^\d{8}_\d{6}_/', '', $d['nombre_archivo']);
        foreach ($rawRecibos as &$d) $d['nombre_display'] = preg_replace('/^\d{8}_\d{6}_/', '', $d['nombre_archivo']);
        unset($d);

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
            'certificados' => $rawCerts,
            'recibos'      => $rawRecibos,
            'flash'        => $flash,
        ]);
    }
}