<?php
namespace app\models;

use \DataBase;

class FichadaModel
{
    /** Fichada en tiempo real (NOW()) */
    public function crear(int $empleadoId, string $tipo, string $origen = 'web', ?int $creadoPor = null, ?string $comentario = null): int
    {
        $sql = "INSERT INTO fichadas (empleado_id, fecha_hora, tipo, origen, creado_por, comentario)
                VALUES (?, NOW(), ?, ?, ?, ?)";
        DataBase::execute($sql, [$empleadoId, $tipo, $origen, $creadoPor, $comentario]);
        $row = DataBase::query("SELECT LAST_INSERT_ID() AS id", [], true);
        return (int)($row[0]['id'] ?? 0);
    }

    /** Fichada con fecha/hora manual (admin) */
    public function crearManual(int $empleadoId, string $fechaHora, string $tipo, string $origen = 'admin', ?int $creadoPor = null, ?string $comentario = null): int
    {
        $sql = "INSERT INTO fichadas (empleado_id, fecha_hora, tipo, origen, creado_por, comentario)
                VALUES (?, ?, ?, ?, ?, ?)";
        DataBase::execute($sql, [$empleadoId, $fechaHora, $tipo, $origen, $creadoPor, $comentario]);
        $row = DataBase::query("SELECT LAST_INSERT_ID() AS id", [], true);
        return (int)($row[0]['id'] ?? 0);
    }

    public function listar(string $desde, string $hasta, ?int $empleadoId = null): array
    {
        $params = [$desde, $hasta];
        $whereEmpleado = "";
        if ($empleadoId) {
            $whereEmpleado = " AND f.empleado_id = ?";
            $params[] = $empleadoId;
        }

        $sql = "SELECT f.*, e.nombre, e.apellido, e.legajo,
                       u.usuario AS creado_usuario,
                       u2.usuario AS editado_usuario
                FROM fichadas f
                INNER JOIN empleados e ON e.id = f.empleado_id
                LEFT JOIN usuarios u ON u.id = f.creado_por
                LEFT JOIN usuarios u2 ON u2.id = f.editado_por
                WHERE f.eliminado = 0
                  AND f.fecha_hora BETWEEN ? AND ?
                  $whereEmpleado
                ORDER BY f.empleado_id ASC, f.fecha_hora DESC";

        return DataBase::query($sql, $params, true);
    }

    public function listarPorEmpleado(int $empleadoId, string $desde, string $hasta): array
    {
        return $this->listar($desde, $hasta, $empleadoId);
    }

    public function obtenerPorId(int $id): ?array
    {
        $sql = "SELECT f.*, e.nombre, e.apellido, e.legajo
                FROM fichadas f
                INNER JOIN empleados e ON e.id = f.empleado_id
                WHERE f.id = ? LIMIT 1";
        $res = DataBase::query($sql, [$id], true);
        return $res[0] ?? null;
    }

    public function actualizar(int $id, string $fechaHora, string $tipo, ?string $comentario, ?int $editadoPor): int
    {
        $sql = "UPDATE fichadas
                SET fecha_hora = ?, tipo = ?, comentario = ?, editado_por = ?, editado_en = NOW()
                WHERE id = ?";
        return DataBase::execute($sql, [$fechaHora, $tipo, $comentario, $editadoPor, $id]);
    }

    public function eliminar(int $id, ?int $editadoPor): int
    {
        $sql = "UPDATE fichadas
                SET eliminado = 1, editado_por = ?, editado_en = NOW()
                WHERE id = ?";
        return DataBase::execute($sql, [$editadoPor, $id]);
    }
}
