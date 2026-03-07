<?php
namespace app\models;

use \DataBase;

/**
 * FichaQR - DocumentoModel
 * Tabla: documentos_empleado
 */
class DocumentoModel
{
    /** Registra un documento en la tabla */
    public function crear(int $empleadoId, string $tipo, string $nombreArchivo, string $rutaArchivo, ?int $subidoPor): int
    {
        $sql = "INSERT INTO documentos_empleado (empleado_id, tipo, nombre_archivo, ruta_archivo, subido_por)
                VALUES (?, ?, ?, ?, ?)";
        DataBase::execute($sql, [$empleadoId, $tipo, $nombreArchivo, $rutaArchivo, $subidoPor]);
        $row = DataBase::query("SELECT LAST_INSERT_ID() AS id", [], true);
        return (int)($row[0]['id'] ?? 0);
    }

    /** Elimina el registro por nombre de archivo y empleado */
    public function eliminarPorArchivo(int $empleadoId, string $nombreArchivo): int
    {
        $sql = "DELETE FROM documentos_empleado WHERE empleado_id = ? AND nombre_archivo = ?";
        return DataBase::execute($sql, [$empleadoId, $nombreArchivo]);
    }

    /** Trae todos los documentos de un empleado por tipo, con datos del usuario que subió */
    public function listarPorEmpleado(int $empleadoId, string $tipo): array
    {
        $sql = "SELECT d.*, u.usuario AS subido_por_usuario
                FROM documentos_empleado d
                LEFT JOIN usuarios u ON u.id = d.subido_por
                WHERE d.empleado_id = ? AND d.tipo = ?
                ORDER BY d.subido_en DESC";
        return DataBase::query($sql, [$empleadoId, $tipo], true);
    }

    /** Trae todos los documentos agrupables por empleado_id */
    public function listarTodos(string $tipo): array
    {
        $sql = "SELECT d.*, u.usuario AS subido_por_usuario
                FROM documentos_empleado d
                LEFT JOIN usuarios u ON u.id = d.subido_por
                WHERE d.tipo = ?
                ORDER BY d.empleado_id, d.subido_en DESC";
        return DataBase::query($sql, [$tipo], true);
    }
}