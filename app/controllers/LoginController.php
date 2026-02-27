<?php
namespace app\controllers;

use \Controller;
use \Response;
use \App;
use app\models\UsuarioModel;

class LoginController extends Controller
{
    public function actionLogin()
    {
        static::path();

        $usuario = '';
        $password = '';
        $error_usuario = '';
        $error_pass = '';
        $general_error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = trim($_POST['usuario'] ?? '');
            $password = trim($_POST['password'] ?? '');

            // Validaciones simples (profe-friendly)
            if ($usuario === '') {
                $error_usuario = 'Usuario requerido';
            } elseif (strlen($usuario) < 3 || strlen($usuario) > 60) {
                $error_usuario = 'Debe tener entre 3 y 60 caracteres';
            }

            if ($password === '') {
                $error_pass = 'Contraseña requerida';
            } elseif (strlen($password) < 3 || strlen($password) > 50) {
                $error_pass = 'Debe tener entre 3 y 50 caracteres';
            }

            if ($error_usuario === '' && $error_pass === '') {
                try {
                    $model = new UsuarioModel();
                    $row = $model->buscarPorUsuario($usuario);

                    if (!$row || (int)($row['activo'] ?? 0) !== 1) {
                        $error_usuario = 'Usuario no encontrado o inactivo';
                    } else {
                        $hash = (string)($row['pass_hash'] ?? '');
                        if (!password_verify($password, $hash)) {
                            $error_pass = 'Contraseña incorrecta';
                        } else {
                            if (session_status() === PHP_SESSION_NONE) session_start();
                            $_SESSION['user_id'] = (int)$row['id'];
                            $_SESSION['user_usuario'] = (string)$row['usuario'];
                            $_SESSION['user_rol'] = (string)$row['rol'];
                            $_SESSION['empleado_id'] = $row['empleado_id'] !== null ? (int)$row['empleado_id'] : null;

                            $rol = strtolower(trim((string)$row['rol']));
                            if (in_array($rol, ['admin', 'jefe'], true)) {
                                header('Location: ' . App::baseUrl() . '/admin/gestion');
                            } else {
                                header('Location: ' . App::baseUrl() . '/fichada/mis');
                            }
                            exit;
                        }
                    }
                } catch (\Throwable $e) {
                    $general_error = 'Error: ' . $e->getMessage();
                }
            }
        }

        $head = SiteController::head();
        $nav = SiteController::nav();
        $footer = SiteController::footer();

        Response::render($this->viewDir(__NAMESPACE__), 'login', [
            'title' => 'FichaQR · Login',
            'head' => $head,
            'nav' => $nav,
            'footer' => $footer,
            'ruta' => App::baseUrl(),
            'usuario' => $usuario,
            'password' => $password,
            'error_usuario' => $error_usuario,
            'error_pass' => $error_pass,
            'general_error' => $general_error,
        ]);
    }

    public function actionLogout()
    {
        SesionController::logout();
    }
}
