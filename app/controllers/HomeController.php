<?php
namespace app\controllers;

use \Controller;
use \Response;
use \App;

class HomeController extends Controller
{
    public function actionIndex($var = null)
    {
        // Si está logueado, redirigir directo a Fichar
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (isset($_SESSION['user_id'])) {
            header('Location: ' . App::baseUrl() . '/terminal/index');
            exit;
        }
        // Si no está logueado, ir al login
        header('Location: ' . App::baseUrl() . '/login/login');
        exit;
    }

    public function action404()
    {
        $head = SiteController::head();
        Response::render($this->viewDir(__NAMESPACE__), '404', [
            'title' => 'FichaQR · 404',
            'head'  => $head,
        ]);
    }
}