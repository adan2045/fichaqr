# FichaQR (PHP puro)

## 1) Base de datos

Tenés el dump listo: `fichaqr.sql`.

### Opción A (phpMyAdmin)
1. Crear la base `fichaqr`.
2. Importar el archivo `fichaqr.sql`.

### Opción B (Consola)
```bash
mysql -u root -p -e "CREATE DATABASE fichaqr CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p fichaqr < fichaqr.sql
```

> Importante: **NO toqué tu archivo de conexión**. Si tu conexión apunta a otra DB (ej `bar_db`), cambiá el `dbname` a `fichaqr` en `app/core/DataBase.php`.

## 2) Crear usuario admin

### Opción rápida por URL
Abrí:
`/tools/crear_admin.php?u=admin&p=123456`

## 2b) Crear usuario empleado

1) Creá el empleado desde `/empleado/formulario`.
2) Luego creá su usuario:

`/tools/crear_usuario.php?u=juan&p=123456&empleado_id=1&rol=empleado`

### Opción SQL
Generá el hash:
`/tools/hash.php?p=123456`

Y luego insert:
```sql
INSERT INTO usuarios (usuario, pass_hash, rol, activo)
VALUES ('admin', 'PEGAR_HASH', 'admin', 1);
```

## 3) Rutas principales

- Login: `/login/login`
- Terminal QR: `/terminal/index`
- Panel admin/jefe: `/admin/gestion`
- CRUD empleados: `/empleado/listado`
- Mis fichadas (empleado): `/fichada/mis`

## 4) Formato de QR

El terminal acepta:
- `EMP:<id_empleado>` (recomendado)
- `<id_empleado>`
