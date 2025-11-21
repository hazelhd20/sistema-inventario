# Sistema de Inventarios (PHP + MVC + MySQL + Tailwind)

Proyecto convertido desde el prototipo en React a una app PHP con patrón MVC, base de datos MySQL y la misma interfaz pastel con Tailwind vía CDN.

## Requisitos
- PHP 8+ (incluido en XAMPP/WAMP/LAMP)
- MySQL 5.7+
- Servidor web apuntando a la carpeta `public` (o visita `http://localhost/sistema-inventario/public` en XAMPP)

## Instalación rápida
1. Crea la base y datos de demo:
   ```sql
   -- En tu cliente MySQL
   SOURCE database.sql;
   ```
2. Ajusta credenciales y base en `app/config.php` (`db` y `app.base_url` si usas subcarpeta).
3. Arranca Apache/MySQL en XAMPP y abre `http://localhost/sistema-inventario/public`.

Credenciales demo:
- Admin: `admin@demo.com` / `Admin123@`
- Empleado: `empleado@demo.com` / `Empleado123@`

## Estructura
- `public/` front-controller y assets (`.htaccess` ya trae rewrite).
- `app/Core` componentes core (Router, Controller, Database).
- `app/Controllers` acciones por módulo (dashboard, productos, inventario, movimientos, reportes, usuarios, auth).
- `app/Models` acceso a datos (Product, Movement, User).
- `app/Views` layouts y vistas Tailwind que replican la UI original.
- `database.sql` esquema + datos de ejemplo.

## Módulos implementados
- Login y cierre de sesión con roles (admin/empleado).
- Dashboard con métricas, alertas de stock y últimos movimientos.
- Productos: alta/edición/elim., búsqueda y tarjetas.
- Inventario: ajuste de stock y filtro de bajos.
- Movimientos: registro de entradas/salidas con impacto en stock y filtros por fecha/tipo.
- Reportes: inventario general, movimientos, stock bajo, valor del inventario.
- Usuarios: CRUD y activación (solo admin).

## Notas
- Tailwind se carga vía CDN y se aplican colores pastel originales en `public/assets/styles.css`.
- Si sirves en otra ruta, ajusta `app.base_url` en `app/config.php` para que las rutas generen bien los enlaces.
