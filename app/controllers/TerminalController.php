<?php
namespace app\controllers;

use \Controller;
use \Response;
use \App;
use app\models\EmpleadoModel;
use app\models\FichadaModel;

/**
 * Terminal QR (modo PC)
 * - Se escanea/pega un código (ej: "EMP:12" o "12")
 * - Muestra datos del empleado y botones ENTRADA / SALIDA
 */
class TerminalController extends Controller
{
    public function actionIndex()
    {
        static::path();
        $head = SiteController::head();
        $nav = SiteController::nav();
        $footer = SiteController::footer();

        $codigo = trim($_GET['codigo'] ?? '');
        $empleado = null;
        $hoy = date('Y-m-d');
        $ultimas = [];

        if ($codigo !== '') {
            $id = $this->parseCodigo($codigo);
            if ($id) {
                $em = new EmpleadoModel();
                $empleado = $em->obtenerPorId($id);
                if ($empleado) {
                    $fm = new FichadaModel();
                    $ultimas = $fm->listarPorEmpleado((int)$empleado['id'], $hoy . ' 00:00:00', $hoy . ' 23:59:59');
                }
            }
        }

        $flash = $_SESSION['flash'] ?? null;
        if (isset($_SESSION['flash'])) unset($_SESSION['flash']);

        Response::render($this->viewDir(__NAMESPACE__), 'index', [
            'title' => 'FichaQR · Terminal',
            'head' => $head,
            'nav' => $nav,
            'footer' => $footer,
            'ruta' => App::baseUrl(),
            'codigo' => $codigo,
            'empleado' => $empleado,
            'ultimas' => $ultimas,
            'flash' => $flash,
        ]);
    }

    private function parseCodigo(string $codigo): ?int
    {
        $codigo = trim($codigo);
        if ($codigo === '') return null;

        // Acepta "EMP:123" o "123"
        if (stripos($codigo, 'EMP:') === 0) {
            $codigo = substr($codigo, 4);
        }
        $codigo = trim($codigo);
        if (!ctype_digit($codigo)) return null;
        return (int)$codigo;
    }
}
