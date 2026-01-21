# Carga de Datos de Prueba - Sistema Chocolatería

## 📦 ¿Qué datos se incluyen?

El script `seed_data.sql` carga datos completos para todas las tablas:

### Catálogo de Productos
- ✅ **5 categorías** de chocolates
- ✅ **15 productos** variados (chocolates, trufas, bombones)
- ✅ Precios desde $1.50 hasta $28.00

### Inventario
- ✅ **3 bodegas** con ubicaciones
- ✅ **Stock inicial** en todas las bodegas
- ✅ Más de 500 unidades en total

### Proveedores y Compras
- ✅ **3 proveedores** de cacao y chocolates
- ✅ **3 compras** registradas
- ✅ Detalles de compra con costos

### Clientes y Ventas
- ✅ **5 clientes** registrados
- ✅ **5 facturas** de ventas completas
- ✅ Detalles de cada venta

### Control
- ✅ **Transacciones** de inventario
- ✅ Listo para sistema KARDEX

## 🚀 Opción 1: Ejecutar SQL en phpMyAdmin (MÁS RÁPIDO)

### Paso 1: Acceder a phpMyAdmin
1. Ve a tu panel de Hostinger
2. **Bases de datos** → **phpMyAdmin**
3. Selecciona `u891466530_ProyectoIntegr`

### Paso 2: Ejecutar el Script
1. Ve a GitHub → tu repositorio → rama `claude/ecommerce-client-section-IUPSr`
2. Abre el archivo `database/seed_data.sql`
3. Copia TODO el contenido
4. En phpMyAdmin, ve a la pestaña **"SQL"**
5. Pega el contenido
6. Haz clic en **"Continuar"**

### Paso 3: Verificar
Al final del script verás una tabla con el conteo:

```
Tabla                      | Total
---------------------------|-------
CATEGORIAS                 | 5
BODEGAS                    | 3
PROVEEDORES                | 3
CLIENTES                   | 5
PRODUCTOS                  | 15
PROXBOD (Stock)            | 17
COMPRAS                    | 3
PROXCMP (Detalle Compras)  | 8
FACTURAS                   | 5
PROXFAC (Detalle Facturas) | 11
TRANSACCIONES              | 5
```

## 🔄 Opción 2: Ejecutar con Laravel (Local)

Si tienes el proyecto localmente:

```bash
# Navegar al proyecto
cd IS_Proyecto
git checkout claude/ecommerce-client-section-IUPSr

# Configurar .env con BD de Hostinger
# DB_HOST=srv1783.hstgr.io
# DB_DATABASE=u891466530_ProyectoIntegr
# etc...

# Ejecutar seeders (cuando estén implementados)
php artisan db:seed
```

## 📝 Detalles de los Datos

### Productos Disponibles

| Código   | Descripción                          | Precio | Stock |
|----------|--------------------------------------|--------|-------|
| PROD001  | Chocolate Oscuro 70% - 100g          | $3.50  | 80    |
| PROD002  | Chocolate Oscuro 85% - 100g          | $4.00  | 30    |
| PROD003  | Chocolate con Leche Classic - 100g   | $2.50  | 130   |
| PROD004  | Chocolate con Leche y Almendras      | $3.00  | 45    |
| PROD006  | Trufa de Chocolate Oscuro            | $1.50  | 100   |
| PROD009  | Caja de Bombones Surtidos 12 und     | $15.00 | 25    |
| PROD010  | Caja de Bombones Premium 24 und      | $28.00 | 15    |

### Usuarios de Prueba

**Clientes:**
- carlos.mendoza@email.com
- laura.fernandez@email.com
- roberto.silva@email.com
- patricia.rojas@email.com
- diego.castro@email.com

**Administradores** (crear con `create_admin_users.sql`):
- carloindemini@gmail.com (contraseña: 12345)
- adriansanchez@gmail.com (contraseña: 12345)
- michaellopez@gmail.com (contraseña: 12345)

## ⚠️ Notas Importantes

### Si quieres borrar datos existentes

Descomenta estas líneas en `seed_data.sql` (líneas 10-20):

```sql
TRUNCATE TABLE KARDEX;
TRUNCATE TABLE PROXFAC;
-- etc...
```

### Productos en Catálogo Público

Los productos con stock > 0 en PROXBOD aparecerán automáticamente en el catálogo público (`/` o `/tienda`).

### Stock y Bodega

El sistema usa actualmente archivos de texto para stock. Para que los productos aparezcan en el catálogo, necesitas:

1. **Opción A**: Actualizar el repositorio `BodegaProductoTxtRepository` para leer de PROXBOD
2. **Opción B**: Crear archivos de texto con los códigos de producto (método actual)

### Facturas de Ejemplo

Las 5 facturas incluidas muestran:
- Cálculo de subtotal, IVA (15%) y total
- Vinculación con clientes
- Detalle de productos vendidos
- Estados: PAG (Pagada)

## 🧪 Probar el Sistema

Después de cargar los datos:

1. **Ver catálogo**: Abre `https://indeminibaez.ec` (o `localhost:8000`)
2. **Registrarse**: Crea una cuenta nueva
3. **Comprar**: Agrega productos al carrito
4. **Ver productos**: Login como admin y ve `/productos`
5. **Ver facturas**: Ve a `/facturas` (admin)

## 🔧 Personalizar los Datos

Puedes editar `seed_data.sql` para:

- Cambiar precios de productos
- Agregar más productos
- Modificar stock inicial
- Agregar más clientes
- Cambiar nombres de bodegas

## 📞 Troubleshooting

### Error: Duplicate entry

Si ya tienes datos y quieres actualizarlos, el script usa `ON DUPLICATE KEY UPDATE` para no fallar.

### Error: Foreign key constraint

Asegúrate de ejecutar el script completo de una vez. No ejecutes secciones por separado.

### No aparecen productos en el catálogo

Verifica que:
1. Los productos existen en tabla PRODUCTOS
2. Tienen stock en PROXBOD
3. El sistema está leyendo de PROXBOD (o actualiza los archivos txt)

## ✅ Checklist Post-Carga

- [ ] Script SQL ejecutado sin errores
- [ ] Verificación muestra conteos correctos
- [ ] Usuarios admin creados
- [ ] Catálogo público muestra productos
- [ ] Puedes hacer login
- [ ] Puedes agregar productos al carrito
- [ ] Sistema de pago funciona

---

**¡Listo para empezar!** 🎉
