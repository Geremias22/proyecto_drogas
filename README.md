# El Punto Ciego — Tienda Online (PHP + MySQL + AJAX)

Proyecto de tienda online desarrollado con **PHP** y **MySQL**, siguiendo un patrón **MVC** (Modelo–Vista–Controlador) y un **router** propio mediante `index.php?c=...&a=...`.  
Incluye autenticación con roles (usuario y administrador), catálogo con búsqueda dinámica (AJAX), carrito, pedidos y panel de administración (CRUD y gestión interna).

---

## Tecnologías
- Backend: **PHP**
- Base de datos: **MySQL** (PDO)
- Frontend: HTML + **Bootstrap 5**
- Interactividad: **AJAX** (catálogo dinámico + packs/preview)  
- Arquitectura: **MVC** + Router por controlador/acción

---

## Estructura del proyecto (MVC)

- `index.php`  
  Router principal. Resuelve:
  - `c` → controlador
  - `a` → acción del controlador

- `controller/`  
  Controladores (lógica de aplicación y navegación).

- `model/`  
  Modelos (acceso a datos con PDO y consultas seguras).

- `views/`  
  Vistas (HTML/PHP).  
  - `views/layouts/main.php` layout principal.
  - `views/partials/` componentes (navbar, cards, grids, etc.)
  - `views/admin/` vistas del panel de administración.

- `public/js/`  
  JavaScript (AJAX del catálogo, packs, etc.)

---

## Roles y permisos

### Usuario (customer)
- Ver catálogo y productos
- Añadir productos al carrito
- Finalizar compra (checkout / payment)
- Ver perfil y pedidos

### Administrador (admin)
- Todo lo anterior, más:
- Gestión de **usuarios** (activar/desactivar, cambiar rol)
- CRUD de **productos**
- CRUD de **categorías**
- CRUD de **packs** y gestión de contenido del pack
- Gestión de **stock** y movimientos
- Sección de análisis (en progreso / ampliable)

El control de acceso se realiza desde:
- `AuthController::checkLogin()`
- `AuthController::checkAdmin()`

---

## Funcionalidades principales

### Autenticación
- Login + Registro
- Password con `password_hash()` y verificación con `password_verify()`
- (Compatibilidad) Si hay contraseñas antiguas en texto plano, se puede migrar a hash en el primer login.

### Catálogo
- Listado completo de productos activos
- Búsqueda en vivo (AJAX) por nombre/descripcion
- Filtro por categoría

### Producto (detalle)
- Página con información completa del producto + stock
- Añadir al carrito con validación en backend

### Carrito
- Añadir, quitar, vaciar
- Validación de stock en backend

### Pedidos
- Crear pedido desde el carrito
- Guardar líneas del pedido (`order_products`)
- Descontar stock + registrar movimiento OUT

### Administración
- Panel admin con accesos a:
  - Usuarios
  - Productos
  - Categorías
  - Packs
  - Stock
  - Análisis

---

## Seguridad (medidas implementadas)
- Consultas seguras con **PDO + prepared statements** (protección SQL Injection)
- Escapado de salida en vistas con `htmlspecialchars()` (mitigación XSS básica)
- Validación server-side de entradas (IDs, cantidades, campos obligatorios, etc.)

> Mejoras recomendadas: CSRF tokens en formularios POST.

---

## Instalación y ejecución (XAMPP)

1. Copia el proyecto a:
   - `C:\xampp\htdocs\el_punto_ciego` (o tu carpeta)
2. Inicia Apache + MySQL desde XAMPP.
3. Importa la base de datos en phpMyAdmin.
4. Ajusta credenciales en:
   - `model/conectaDB.php`
5. Abre:
   - `http://localhost/el_punto_ciego/index.php`

---

## Usuarios de prueba
- Admin: (creado en BDD)
  - role: `admin`
- Usuario normal: se puede crear desde el registro (role: `customer`)

---

## Requisitos del enunciado (estado)
- Login/Registro + roles: ✅
- Panel admin CRUD productos: ✅
- AJAX (catálogo): ✅
- AJAX verificar email en registro: ⏳ (pendiente)
- Restablecimiento de contraseña (método creativo): ⏳ (pendiente)
- Validación cliente+servidor: ⚠️ (servidor OK, cliente a reforzar)
- Diagrama BD + documentación: ✅ (incluido)

---

## Autor
- Geremias y David
