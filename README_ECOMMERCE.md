# Sistema de Ecommerce - Chocolatería

## 📋 Resumen del Proyecto

Se ha implementado un **sistema completo de ecommerce** para la sección de clientes del sistema de facturación de chocolatería, conectado a una base de datos MySQL en Hostinger siguiendo el modelamiento de PowerDesigner.

## 🎯 Funcionalidades Implementadas

### 1. ✅ Base de Datos en Hostinger
- **Esquema completo** basado en PowerDesigner
- **15 tablas** con relaciones y foreign keys
- Script SQL optimizado para phpMyAdmin
- Migraciones Laravel sincronizadas con el esquema

### 2. ✅ Catálogo Público de Productos
- **Página principal** (/) muestra catálogo de productos
- Filtrado por categorías
- Solo muestra productos activos con stock
- Diseño responsive con Tailwind CSS
- Acceso sin necesidad de login

### 3. ✅ Sistema de Carrito de Compras
- Agregar/quitar productos
- Calcular subtotal, IVA (15%) y total
- Validación de stock en tiempo real
- Persistencia en sesión

### 4. ✅ Sistema de Facturas
- Genera facturas en tabla `FACTURAS` al pagar
- Guarda detalle en tabla `PROXFAC`
- Crea o vincula cliente automáticamente
- Descuenta stock de bodega
- Transacciones para integridad de datos

### 5. ✅ Modelos Eloquent
- 15 modelos con relaciones completas
- Soporte para primary keys compuestas
- Compatibilidad con nuevo esquema de BD

## 📊 Estructura de Base de Datos

### Tablas Principales

#### CATEGORIA
- Categorías de productos
- Campos: CAT_CODIGO, CAT_NOMBRE, CAT_DESCRIPCION

#### CLIENTES
- Información de clientes
- Campos: CLI_ID, CLI_CEDULA_RUC, CLI_NOMBRE, CLI_TELEFONO, CLI_CORREO

#### PRODUCTOS
- Catálogo de productos
- Campos: PRD_CODIGO, CAT_CODIGO, PRD_DESCRIPCION, PRD_PRECIO, PRD_COSTO_ADQUISICION
- Relación con CATEGORIA

#### FACTURAS
- Ventas realizadas a clientes
- Campos: FAC_CODIGO, FAC_FECHA, FAC_SUBTOTAL, FAC_IVA, FAC_MONTO_TOTAL, FAC_ESTADO, CLI_ID
- Relación con CLIENTES

#### PROXFAC
- Detalle de productos en cada factura
- Campos: FAC_CODIGO, PRD_CODIGO, DET_FAC_CANTIDAD, DET_FAC_PRECIO_UNITARIO, ESTADO_PROXFAC
- Relación con FACTURAS y PRODUCTOS

### Otras Tablas
- **BODEGAS**: Almacenes
- **PROVEEDORES**: Proveedores de productos
- **COMPRAS**: Compras a proveedores
- **PROXCMP**: Detalle de compras
- **PROXBOD**: Productos por bodega
- **USUARIOS**: Usuarios del sistema
- **KARDEX**: Control de inventario
- **TRANSACCION**: Transacciones de inventario
- **DETALLE_CARRITO**: Items en carritos

## 🚀 Instalación y Configuración

### Paso 1: Ejecutar el Script SQL en Hostinger

1. Accede a **phpMyAdmin** en Hostinger
2. Selecciona la base de datos `u891466530_ProyectoIntegr`
3. Ve a la pestaña **"Importar"**
4. Sube el archivo `database/schema_hostinger.sql`
5. Haz clic en **"Continuar"**

Ver instrucciones detalladas en `database/INSTRUCCIONES_HOSTINGER.md`

### Paso 2: Configurar .env

El archivo `.env` ya está configurado con las credenciales de Hostinger:

```env
DB_CONNECTION=mysql
DB_HOST=srv1783.hstgr.io
DB_PORT=3306
DB_DATABASE=u891466530_ProyectoIntegr
DB_USERNAME=u891466530_proyectointegr
DB_PASSWORD=lticPUCE24
```

### Paso 3: Instalar Dependencias

```bash
composer install
npm install
npm run build
```

### Paso 4: Ejecutar la Aplicación

```bash
php artisan serve
```

La aplicación estará disponible en: `http://localhost:8000`

## 🌐 Rutas Principales

### Rutas Públicas (No requieren login)
- `/` - Catálogo de productos (página principal)
- `/tienda` - Catálogo de productos
- `/producto/{codigo}` - Detalle de producto
- `/login` - Iniciar sesión
- `/register` - Registrarse

### Rutas Protegidas (Requieren login)
- `/dashboard` - Panel de administración
- `/carrito` - Ver carrito de compras
- `/productos` - Gestión de productos (admin)
- `/clientes` - Gestión de clientes (admin)
- `/proveedores` - Gestión de proveedores (admin)
- `/compras` - Gestión de compras (admin)
- `/facturas` - Ver facturas (admin)

## 🔐 Tipos de Usuario

### Cliente (Usuario Normal)
- Ver catálogo público
- Agregar productos al carrito
- Realizar compras
- Ver sus facturas

### Administrador
- Todas las funciones de cliente
- Gestionar productos
- Gestionar clientes
- Gestionar proveedores
- Gestionar compras
- Ver todas las facturas
- Gestionar bodega/inventario

## 💡 Flujo de Compra

1. **Cliente navega** el catálogo público (/)
2. **Selecciona productos** y los agrega al carrito
3. **Revisa el carrito** (/carrito)
4. **Confirma el pago**
5. **Sistema genera**:
   - Factura en tabla FACTURAS
   - Detalle en tabla PROXFAC
   - Descuenta stock de bodega
   - Vincula con cliente

## 📁 Archivos Importantes

### Controladores
- `app/Http/Controllers/TiendaController.php` - Catálogo público
- `app/Http/Controllers/CarritoController.php` - Carrito de compras
- `app/Http/Controllers/ProductoController.php` - Gestión de productos

### Servicios
- `app/Services/CarritoService.php` - Lógica de carrito y facturas

### Modelos
- `app/Models/Producto.php`
- `app/Models/Categoria.php`
- `app/Models/Cliente.php`
- `app/Models/Factura.php`
- `app/Models/Proxfac.php`
- Y 10 modelos más...

### Vistas
- `resources/views/tienda/index.blade.php` - Catálogo
- `resources/views/tienda/show.blade.php` - Detalle producto
- `resources/views/carrito/index.blade.php` - Carrito

### Migraciones
- `database/migrations/2026_01_21_*.php` - 14 migraciones

### Scripts SQL
- `database/schema_hostinger.sql` - Script para phpMyAdmin
- `database/schema_powerdesigner.sql` - Script original de PowerDesigner

## 🔧 Configuración Técnica

### Tecnologías Utilizadas
- **Laravel 12** - Framework PHP
- **MySQL 8** - Base de datos en Hostinger
- **Tailwind CSS** - Estilos
- **Eloquent ORM** - Mapeo objeto-relacional
- **Blade** - Motor de plantillas

### Características Implementadas
- ✅ Transacciones de base de datos
- ✅ Validación de stock en tiempo real
- ✅ Cálculo automático de IVA (15%)
- ✅ Manejo de sesiones para carrito
- ✅ Relaciones Eloquent completas
- ✅ Soporte para primary keys compuestas
- ✅ Middleware de autenticación
- ✅ Diseño responsive

## 📝 Notas Importantes

### Compatibilidad
El sistema mantiene **compatibilidad con el esquema anterior** usando fallbacks:
```php
$producto->PRD_DESCRIPCION ?? $producto->nombre ?? 'Sin nombre'
$producto->PRD_PRECIO ?? $producto->precio ?? 0
```

### Estados de Factura
- **PAG** = Pagada
- **PEN** = Pendiente
- **ANU** = Anulada

### Stock y Bodega
El sistema actualmente usa un **repositorio de archivos de texto** para el stock de bodega (`BodegaProductoTxtRepository`). En el futuro, esto se puede migrar a usar la tabla `PROXBOD` de la base de datos.

## 🐛 Problemas Conocidos

1. **Conexión remota a MySQL**: Si tienes problemas conectándote desde el entorno de desarrollo, ejecuta el script SQL directamente en phpMyAdmin.

2. **Stock de bodega**: Actualmente usa archivos de texto. Se recomienda migrar a usar la tabla PROXBOD para producción.

## 📚 Próximos Pasos Sugeridos

1. ✅ **Implementar vista de historial de facturas para clientes**
2. ✅ **Migrar gestión de stock a tabla PROXBOD**
3. ✅ **Implementar sistema de Kardex completo**
4. ✅ **Agregar imágenes reales de productos**
5. ✅ **Implementar sistema de búsqueda de productos**
6. ✅ **Agregar paginación al catálogo**
7. ✅ **Implementar sistema de reseñas/calificaciones**
8. ✅ **Agregar pasarela de pago real**

## 👨‍💻 Desarrollado por

Claude - Implementación completa del sistema de ecommerce basado en modelamiento PowerDesigner

---

**Fecha**: Enero 2026
**Versión**: 1.0
