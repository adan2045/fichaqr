<?php
namespace app\controllers;

use \App;

/**
 * FichaQR - helpers de sesión y permisos
 */
class SesionController
{
    public static function iniciar(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function estaAutenticado(): bool
    {
        self::iniciar();
        return isset($_SESSION['user_id']);
    }

    public static function rol(): ?string
    {
        self::iniciar();
        return $_SESSION['user_rol'] ?? null;
    }

    public static function empleadoId(): ?int
    {
        self::iniciar();
        return isset($_SESSION['empleado_id']) ? (int)$_SESSION['empleado_id'] : null;
    }

    public static function requireLogin(): void
    {
        if (!self::estaAutenticado()) {
            header('Location: ' . App::baseUrl() . '/login/login');
            exit;
        }
    }

    public static function esAdmin(): bool
    {
        $rol = strtolower(trim((string)self::rol()));
        return in_array($rol, ['admin', 'jefe'], true);
    }

    public static function requireAdmin(): void
    {
        self::requireLogin();
        if (!self::esAdmin()) {
            header('Location: ' . App::baseUrl() . '/fichada/mis');
            exit;
        }
    }

    public static function logout(): void
    {
        self::iniciar();
        session_unset();
        session_destroy();
        header('Location: ' . App::baseUrl() . '/login/login');
        exit;
    }
}
