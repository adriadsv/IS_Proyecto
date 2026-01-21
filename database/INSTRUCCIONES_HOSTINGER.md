# Instrucciones para Crear Base de Datos en Hostinger

## Paso 1: Acceder a phpMyAdmin

1. Inicia sesión en tu panel de Hostinger
2. Ve a **Bases de datos** → **phpMyAdmin**
3. Selecciona la base de datos `u891466530_ProyectoIntegr`

## Paso 2: Ejecutar el Script SQL

### Opción A: Importar archivo SQL (RECOMENDADO)

1. En phpMyAdmin, selecciona la pestaña **"Importar"**
2. Haz clic en **"Elegir archivo"**
3. Selecciona el archivo `database/schema_hostinger.sql` de este proyecto
4. Asegúrate de que el formato esté en **"SQL"**
5. Haz clic en **"Continuar"** para ejecutar
6. Espera a que se complete la importación (verás un mensaje de éxito)

### Opción B: Copiar y pegar el SQL

1. Abre el archivo `database/schema_hostinger.sql` en un editor de texto
2. Copia TODO el contenido del archivo
3. En phpMyAdmin, ve a la pestaña **"SQL"**
4. Pega el contenido completo en el área de texto
5. Haz clic en **"Continuar"** para ejecutar
6. Espera a que se complete (puede tomar unos segundos)

## Paso 3: Verificar la Creación de Tablas

1. En el panel izquierdo de phpMyAdmin, actualiza la lista de tablas
2. Deberías ver las siguientes tablas creadas:
   - ✅ BODEGAS
   - ✅ CATEGORIA
   - ✅ CLIENTES
   - ✅ COMPRAS
   - ✅ DETALLE_CARRITO
   - ✅ FACTURAS
   - ✅ KARDEX
   - ✅ PRODUCTOS
   - ✅ PROVEEDORES
   - ✅ PROXBOD
   - ✅ PROXCMP
   - ✅ PROXFAC
   - ✅ TRANSACCION
   - ✅ USUARIOS

## Paso 4: Verificar las Relaciones (Foreign Keys)

1. Haz clic en cualquier tabla (ej: PRODUCTOS)
2. Ve a la pestaña **"Estructura"**
3. Deberías ver las foreign keys definidas en la sección inferior

## Estructura de la Base de Datos

### Tablas Base (sin dependencias)
- **CATEGORIA**: Categorías de productos
- **CLIENTES**: Información de clientes
- **PROVEEDORES**: Proveedores del sistema
- **BODEGAS**: Bodegas/almacenes
- **TRANSACCION**: Registro de transacciones

### Tablas Principales
- **PRODUCTOS**: Catálogo de productos (depende de CATEGORIA)
- **COMPRAS**: Compras a proveedores (depende de PROVEEDORES)
- **FACTURAS**: Ventas a clientes (depende de CLIENTES)
- **USUARIOS**: Usuarios del sistema (depende de CLIENTES)

### Tablas de Detalle/Intermedias
- **PROXBOD**: Productos por bodega (relación BODEGAS-PRODUCTOS)
- **PROXCMP**: Detalle de compras (relación COMPRAS-PRODUCTOS)
- **PROXFAC**: Detalle de facturas (relación FACTURAS-PRODUCTOS)
- **DETALLE_CARRITO**: Items del carrito de compras
- **KARDEX**: Control de inventario (registro de movimientos)

## Problemas Comunes

### Error: "Tabla ya existe"
**Solución**: Elimina las tablas existentes antes de ejecutar el script:
```sql
SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS KARDEX, DETALLE_CARRITO, PROXFAC, PROXCMP, PROXBOD;
DROP TABLE IF EXISTS USUARIOS, FACTURAS, COMPRAS, PRODUCTOS;
DROP TABLE IF EXISTS TRANSACCION, BODEGAS, PROVEEDORES, CLIENTES, CATEGORIA;
SET FOREIGN_KEY_CHECKS=1;
```

### Error: "Foreign key constraint fails"
**Solución**: Asegúrate de ejecutar TODO el script de una vez. Las foreign keys se crean al final.

### Error: "Access denied"
**Solución**: Verifica que el usuario `u891466530_proyectointegr` tenga permisos completos sobre la base de datos.

## Siguiente Paso: Conectar Laravel

Una vez creadas las tablas, tu aplicación Laravel ya puede conectarse a la base de datos de Hostinger con la configuración del archivo `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=srv1783.hstgr.io
DB_PORT=3306
DB_DATABASE=u891466530_ProyectoIntegr
DB_USERNAME=u891466530_proyectointegr
DB_PASSWORD=lticPUCE24
```

## Datos de Prueba (Opcional)

Si deseas insertar datos de prueba, puedes ejecutar los seeders de Laravel:

```bash
php artisan db:seed
```

O ejecutar SQL manual en phpMyAdmin para insertar registros iniciales.

## Soporte

Si tienes algún problema:
1. Verifica que todas las tablas se hayan creado correctamente
2. Revisa los logs de phpMyAdmin para errores específicos
3. Asegúrate de que las credenciales de .env sean correctas
