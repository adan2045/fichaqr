<?php
namespace app\models;

use \DataBase;

/**
 * FichaQR - UsuarioModel
 * Tabla: usuarios(usuario, pass_hash, rol, empleado_id, activo)
 */
class UsuarioModel
{
    public function buscarPorUsuario(string $usuario): ?array
    {
        $sql = "SELECT * FROM usuarios WHERE usuario = ? LIMIT 1";
        $res = DataBase::query($sql, [$usuario], true);
        return $res[0] ?? null;
    }

    public function obtenerPorId(int $id): ?array
    {
        $sql = "SELECT * FROM usuarios WHERE id = ? LIMIT 1";
        $res = DataBase::query($sql, [$id], true);
        return $res[0] ?? null;
    }

    public function listar(bool $soloActivos = true): array
    {
        $where = $soloActivos ? "WHERE u.activo = 1" : "";
        $sql = "SELECT u.*, e.nombre, e.apellido, e.legajo
                FROM usuarios u
                LEFT JOIN empleados e ON e.id = u.empleado_id
                $where
                ORDER BY u.id DESC";
        return DataBase::query($sql, [], true);
    }

    public function crear(string $usuario, string $password, string $rol = 'empleado', ?int $empleadoId = null, int $activo = 1): int
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO usuarios (usuario, pass_hash, rol, empleado_id, activo) VALUES (?, ?, ?, ?, ?)";
        DataBase::execute($sql, [$usuario, $hash, $rol, $empleadoId, $activo]);
        $row = DataBase::query("SELECT LAST_INSERT_ID() AS id", [], true);
        return (int)($row[0]['id'] ?? 0);
    }

    public function actualizar(int $id, string $usuario, string $rol, ?int $empleadoId, int $activo): int
    {
        $sql = "UPDATE usuarios SET usuario = ?, rol = ?, empleado_id = ?, activo = ? WHERE id = ?";
        return DataBase::execute($sql, [$usuario, $rol, $empleadoId, $activo, $id]);
    }

    public function actualizarPassword(int $id, string $password): int
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE usuarios SET pass_hash = ? WHERE id = ?";
        return DataBase::execute($sql, [$hash, $id]);
    }
}
