# FichaQR v2 — Resumen de cambios

## Cambios realizados

### Login mejorado
- Validación en tiempo real con JavaScript (igual que el POS)
- Indicador visual de fuerza de contraseña
- Mensajes de error por campo (usuario / contraseña)
- Botón desactivado durante el envío para evitar doble submit
- Auto-focus al cargar

### Panel Admin (gestion.php)
- **Stats rápidas**: contadores de total, entradas, salidas y empleados únicos
- **Separadores por empleado** en la tabla con botón rápido a documentos
- **Modal CRUD inline**: Editar y crear fichadas sin salir de la página
- Botón "+ Nueva fichada" para ingresar fichadas manuales con selector de empleado
- Eliminar con modal de confirmación (sin `confirm()` del browser)
- Badge especial para fichadas de origen `admin`

### Módulo de Documentos (`/empleado/docs?id=X`)
- Subir certificados médicos y recibos de sueldo (PDF, JPG, PNG)
- Vista separada por tipo de documento
- Eliminar documentos individuales
- Accesible desde la tabla de fichadas (botón 📁) y desde el listado de empleados
- Archivos guardados en `public/docs/empleados/{id}/certificados/` y `.../recibos/`

### FichadaController
- Nuevo método `actionNueva()` para fichadas manuales desde panel admin
- `actionEliminar()` acepta tanto GET como POST (más seguro con modal)

### FichadaModel
- Nuevo método `crearManual()` que acepta fecha/hora explícita
- `listar()` ahora ordena por `empleado_id` primero para la separación visual

### Base de datos (fichaqr.sql)
- Nueva tabla `documentos_empleado` (referencia — los archivos se guardan en disco)
- Índice adicional en `fichadas(tipo, fecha_hora)`

## Instalación
1. Reemplazar los archivos modificados
2. Ejecutar el bloque "ACTUALIZACIÓN" del `fichaqr.sql` en tu base de datos
3. Crear la carpeta `public/docs/` con permisos de escritura (755)
