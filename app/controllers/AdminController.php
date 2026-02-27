<?php
namespace app\controllers;

use \Controller;
use \Response;
use \App;
use app\models\EmpleadoModel;
use app\models\FichadaModel;

class AdminController extends Controller{
    // Constructor
	public function __construct()
	{

	}

	public function actionIndex($var=null)
	{	
		$footer = SiteController::footer();
		$head = SiteController::head();
		$nav = SiteController::nav();
		$path = static::path();
		Response::render($this->viewDir(__NAMESPACE__), "inicio", [
			"title" => $this->title . "Inicio",
			"head" => $head,
			"nav" => $nav,
			"footer" => $footer,
		]);
	}
	public function actionGestion()
	{	
		SesionController::requireAdmin();
		static::path();

		$footer = SiteController::footer();
		$head = SiteController::head();
		$nav = SiteController::nav();

		$desde = $_GET['desde'] ?? date('Y-m-d', strtotime('-7 days'));
		$hasta = $_GET['hasta'] ?? date('Y-m-d');
		$empleadoId = isset($_GET['empleado_id']) && $_GET['empleado_id'] !== '' ? (int)$_GET['empleado_id'] : null;

		$desdeDT = $desde . ' 00:00:00';
		$hastaDT = $hasta . ' 23:59:59';

		$em = new EmpleadoModel();
		$empleados = $em->obtenerTodos(true);

		$fm = new FichadaModel();
		$fichadas = $fm->listar($desdeDT, $hastaDT, $empleadoId);

		$flash = $_SESSION['flash'] ?? null;
		if (isset($_SESSION['flash'])) unset($_SESSION['flash']);

		Response::render($this->viewDir(__NAMESPACE__), "gestion", [
			"title" => 'FichaQR · Panel',
			"head" => $head,
			"nav" => $nav,
			"footer" => $footer,
			"ruta" => App::baseUrl(),
			"desde" => $desde,
			"hasta" => $hasta,
			"empleadoId" => $empleadoId,
			"empleados" => $empleados,
			"fichadas" => $fichadas,
			"flash" => $flash,
		]);
	}
}