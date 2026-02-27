<?php
namespace app\models;

use \DataBase;

/**
 * FichaQR - EmpleadoModel
 * Tabla: empleados(legajo,nombre,apellido,dni,email,activo)
 */
class EmpleadoModel
{
    public function obtenerTodos(bool $soloActivos = true): array
    {
        $where = $soloActivos ? "WHERE activo = 1" : "";
        $sql = "SELECT * FROM empleados $where ORDER BY apellido, nombre";
        return DataBase::query($sql, [], true);
    }

    public function obtenerPorId(int $id): ?array
    {
        $sql = "SELECT * FROM empleados WHERE id = ? LIMIT 1";
        $res = DataBase::query($sql, [$id], true);
        return $res[0] ?? null;
    }

    public function crear(?string $legajo, string $nombre, string $apellido, ?string $dni, ?string $email, int $activo = 1): int
    {
        $sql = "INSERT INTO empleados (legajo, nombre, apellido, dni, email, activo) VALUES (?, ?, ?, ?, ?, ?)";
        DataBase::execute($sql, [$legajo, $nombre, $apellido, $dni, $email, $activo]);
        $row = DataBase::query("SELECT LAST_INSERT_ID() AS id", [], true);
        return (int)($row[0]['id'] ?? 0);
    }

    public function actualizar(int $id, ?string $legajo, string $nombre, string $apellido, ?string $dni, ?string $email, int $activo): int
    {
        $sql = "UPDATE empleados SET legajo = ?, nombre = ?, apellido = ?, dni = ?, email = ?, activo = ? WHERE id = ?";
        return DataBase::execute($sql, [$legajo, $nombre, $apellido, $dni, $email, $activo, $id]);
    }

    public function desactivar(int $id): int
    {
        $sql = "UPDATE empleados SET activo = 0 WHERE id = ?";
        return DataBase::execute($sql, [$id]);
    }
}
