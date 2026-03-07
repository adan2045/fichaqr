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

    public function obtenerPorLegajo(string $legajo): ?array
    {
        // Acepta "001", "EMP-001" o el número directo del id
        $legajo = trim($legajo);
        // Intentar por legajo exacto
        $sql = "SELECT * FROM empleados WHERE legajo = ? AND activo = 1 LIMIT 1";
        $res = DataBase::query($sql, [$legajo], true);
        if (!empty($res[0])) return $res[0];
        // Intentar con prefijo EMP-
        $conPrefijo = 'EMP-' . ltrim($legajo, '0');
        $conPrefijoPad = 'EMP-' . str_pad(ltrim($legajo, '0'), 3, '0', STR_PAD_LEFT);
        $sql2 = "SELECT * FROM empleados WHERE (legajo = ? OR legajo = ?) AND activo = 1 LIMIT 1";
        $res2 = DataBase::query($sql2, [$conPrefijo, $conPrefijoPad], true);
        return $res2[0] ?? null;
    }

    public function generarLegajo(): string
    {
        // Busca el mayor número de legajo con formato EMP-NNN
        $sql = "SELECT legajo FROM empleados WHERE legajo REGEXP '^EMP-[0-9]+$' ORDER BY CAST(SUBSTRING(legajo,5) AS UNSIGNED) DESC LIMIT 1";
        $res = DataBase::query($sql, [], true);
        if (!empty($res[0]['legajo'])) {
            $num = (int)substr($res[0]['legajo'], 4) + 1;
        } else {
            $num = 1;
        }
        return 'EMP-' . str_pad($num, 3, '0', STR_PAD_LEFT);
    }

    public function desactivar(int $id): int
    {
        $sql = "UPDATE empleados SET activo = 0 WHERE id = ?";
        return DataBase::execute($sql, [$id]);
    }
}