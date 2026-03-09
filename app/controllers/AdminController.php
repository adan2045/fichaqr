<?php
namespace app\controllers;

use \Controller;
use \Response;
use \App;
use app\models\EmpleadoModel;
use app\models\FichadaModel;

class AdminController extends Controller{

	public function __construct() {}

	public function actionIndex($var=null)
	{
		// Redirigir directo al panel
		header('Location: ' . App::baseUrl() . '/admin/gestion');
		exit;
	}

	public function actionGestion()
	{
		SesionController::requireAdmin();
		static::path();

		$footer = SiteController::footer();
		$head   = SiteController::head();
		$nav    = SiteController::nav();

		// Si no hay parámetros GET, siempre arrancar en HOY (sin búsqueda previa)
		$hayFiltro  = isset($_GET['desde']) || isset($_GET['hasta']) || isset($_GET['empleado_id']);
		$desde      = $hayFiltro ? ($_GET['desde'] ?? date('Y-m-d')) : date('Y-m-d');
		$hasta      = $hayFiltro ? ($_GET['hasta'] ?? date('Y-m-d')) : date('Y-m-d');
		$empleadoId = ($hayFiltro && isset($_GET['empleado_id']) && $_GET['empleado_id'] !== '')
		              ? (int)$_GET['empleado_id'] : null;

		$desdeDT = $desde . ' 00:00:00';
		$hastaDT = $hasta . ' 23:59:59';

		$em        = new EmpleadoModel();
		$empleados = $em->obtenerTodos(true);

		$fm       = new FichadaModel();
		$fichadas = $fm->listar($desdeDT, $hastaDT, $empleadoId);

		$flash = $_SESSION['flash'] ?? null;
		if (isset($_SESSION['flash'])) unset($_SESSION['flash']);

		Response::render($this->viewDir(__NAMESPACE__), "gestion", [
			"title"      => 'FichaQR · Panel',
			"head"       => $head,
			"nav"        => $nav,
			"footer"     => $footer,
			"ruta"       => App::baseUrl(),
			"desde"      => $desde,
			"hasta"      => $hasta,
			"empleadoId" => $empleadoId,
			"empleados"  => $empleados,
			"fichadas"   => $fichadas,
			"flash"      => $flash,
		]);
	}
}