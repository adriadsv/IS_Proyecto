# Guía de Deployment en Hostinger

## 📋 Requisitos Previos

- Cuenta de Hostinger con hosting web
- Dominio configurado (indeminibaez.ec)
- Acceso a File Manager o FTP
- Base de datos MySQL ya creada
- Composer instalado localmente

## 🚀 Pasos para Subir el Proyecto

### Paso 1: Preparar el Proyecto Localmente

#### 1.1 Optimizar para Producción

```bash
# Limpiar caché
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Generar archivos optimizados
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Compilar assets
npm run build
```

#### 1.2 Actualizar .env para Producción

Crea un archivo `.env.production` con:

```env
APP_NAME="Sistema Chocolatería"
APP_ENV=production
APP_KEY=base64:45iplql+zrUsQiZ482H1Sh0AibTsJYX3EpsKt2hi6XE=
APP_DEBUG=false
APP_URL=https://indeminibaez.ec

DB_CONNECTION=mysql
DB_HOST=srv1783.hstgr.io
DB_PORT=3306
DB_DATABASE=u891466530_ProyectoIntegr
DB_USERNAME=u891466530_proyectointegr
DB_PASSWORD=lticPUCE24

SESSION_DRIVER=database
SESSION_LIFETIME=120

CACHE_STORE=database
QUEUE_CONNECTION=database
```

**IMPORTANTE**:
- `APP_DEBUG=false` en producción
- `APP_URL` debe ser tu dominio completo

#### 1.3 Crear archivo .gitignore Correcto

Asegúrate de NO subir estos archivos:

```
/node_modules
/public/hot
/public/storage
/storage/*.key
/vendor
.env
.env.backup
.env.production
.phpunit.result.cache
Homestead.json
Homestead.yaml
auth.json
npm-debug.log
yarn-error.log
```

### Paso 2: Ejecutar el Script SQL en phpMyAdmin

Si aún no lo has hecho:

1. Ve a phpMyAdmin en Hostinger
2. Selecciona la base de datos `u891466530_ProyectoIntegr`
3. Importa el archivo `database/schema_hostinger.sql`
4. Verifica que se crearon todas las tablas

### Paso 3: Subir Archivos a Hostinger

#### Opción A: File Manager (Recomendado para principiantes)

1. **Comprimir el proyecto**:
   ```bash
   # En tu computadora, comprimir todo el proyecto
   zip -r proyecto.zip . -x "node_modules/*" "vendor/*" ".git/*"
   ```

2. **Subir a Hostinger**:
   - Ingresa al panel de Hostinger
   - Ve a **Files** → **File Manager**
   - Navega a la carpeta `public_html`
   - **IMPORTANTE**: Elimina todo el contenido existente de `public_html`
   - Sube el archivo `proyecto.zip`
   - Haz clic derecho en `proyecto.zip` → **Extract**
   - Elimina el archivo `proyecto.zip` después de extraer

3. **Instalar dependencias**:
   - En File Manager, haz clic en **Terminal** (esquina superior derecha)
   - Ejecuta:
     ```bash
     cd public_html
     composer install --optimize-autoloader --no-dev
     ```

#### Opción B: FTP/SFTP (Más rápido)

1. **Configurar cliente FTP** (FileZilla, WinSCP, etc.)
   - Host: ftp.indeminibaez.ec (o el que te proporcione Hostinger)
   - Usuario: Tu usuario de Hostinger
   - Contraseña: Tu contraseña de Hostinger
   - Puerto: 21 (FTP) o 22 (SFTP)

2. **Subir archivos**:
   - Conecta via FTP
   - Navega a `public_html`
   - **BORRA** todo el contenido existente
   - Sube TODO el proyecto (excepto node_modules y vendor)

3. **Instalar dependencias via SSH**:
   ```bash
   cd public_html
   composer install --optimize-autoloader --no-dev
   ```

### Paso 4: Configurar el Dominio para Laravel

#### 4.1 Problema: Laravel está en subdirectorio `public`

Laravel espera que el documento root sea la carpeta `public`, pero Hostinger usa `public_html`.

**Solución 1: Mover contenido de public a public_html** (RECOMENDADO)

1. Conecta via File Manager o FTP
2. Estructura debe quedar así:

   ```
   /home/u891466530/
   ├── domains/
   │   └── indeminibaez.ec/
   │       └── public_html/         ← Document Root
   │           ├── index.php        ← Archivo de Laravel (desde /public)
   │           ├── .htaccess        ← Archivo de Laravel (desde /public)
   │           ├── css/             ← Assets compilados
   │           └── js/
   ├── proyecto_laravel/            ← Crear esta carpeta
   │   ├── app/
   │   ├── bootstrap/
   │   ├── config/
   │   ├── database/
   │   ├── resources/
   │   ├── routes/
   │   ├── storage/
   │   ├── vendor/
   │   ├── .env
   │   ├── artisan
   │   └── composer.json
   ```

3. **Pasos para reorganizar**:

   a. Crea una carpeta fuera de `public_html`:
   ```bash
   cd /home/u891466530
   mkdir proyecto_laravel
   ```

   b. Mueve TODO excepto la carpeta `public`:
   ```bash
   cd public_html
   mv app bootstrap config database resources routes storage tests vendor artisan composer.* .env* /home/u891466530/proyecto_laravel/
   ```

   c. Mueve el contenido de `public` a `public_html`:
   ```bash
   cd public_html/public
   mv * /home/u891466530/domains/indeminibaez.ec/public_html/
   mv .htaccess /home/u891466530/domains/indeminibaez.ec/public_html/
   cd ..
   rm -rf public
   ```

   d. Edita `public_html/index.php`:
   ```php
   // Busca esta línea:
   require __DIR__.'/../vendor/autoload.php';
   // Cámbiala por:
   require __DIR__.'/../../proyecto_laravel/vendor/autoload.php';

   // Busca esta línea:
   $app = require_once __DIR__.'/../bootstrap/app.php';
   // Cámbiala por:
   $app = require_once __DIR__.'/../../proyecto_laravel/bootstrap/app.php';
   ```

**Solución 2: Configurar subdomain o alias** (Alternativa)

Si prefieres mantener la estructura normal de Laravel:

1. En panel de Hostinger → **Domains**
2. Apunta el dominio a `public_html/public` en lugar de solo `public_html`

### Paso 5: Configurar Permisos

```bash
# Conectar via SSH o Terminal
cd /home/u891466530/proyecto_laravel  # O donde esté tu proyecto

# Dar permisos a storage y bootstrap/cache
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Si Hostinger lo requiere
chown -R $USER:$USER storage
chown -R $USER:$USER bootstrap/cache
```

### Paso 6: Crear/Copiar archivo .env

```bash
cd /home/u891466530/proyecto_laravel
cp .env.production .env

# O crear manualmente el archivo .env con el contenido de producción
```

### Paso 7: Generar Key (si es necesario)

```bash
php artisan key:generate
```

### Paso 8: Crear Symbolic Link para Storage

```bash
php artisan storage:link
```

### Paso 9: Configurar .htaccess

Verifica que en `public_html/.htaccess` tengas:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On

    # Redirect to HTTPS
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

### Paso 10: Probar el Sitio

1. Abre tu navegador
2. Ve a `https://indeminibaez.ec`
3. Deberías ver el catálogo de productos

#### Solución de Problemas Comunes:

**Error 500**:
```bash
# Ver logs
tail -f storage/logs/laravel.log

# Verificar permisos
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

**Error 404 en todas las rutas**:
- Verifica que `.htaccess` esté en `public_html`
- Verifica que mod_rewrite esté habilitado
- Verifica las rutas en `index.php`

**Error de base de datos**:
- Verifica credenciales en `.env`
- Verifica que el script SQL se haya ejecutado
- Prueba conexión desde terminal: `php artisan migrate:status`

**Assets no cargan (CSS/JS)**:
```bash
# Recompilar assets
npm run build

# Verificar que los archivos estén en public_html/build/
```

### Paso 11: Configurar SSL (HTTPS)

1. En panel de Hostinger → **SSL**
2. Activar **Let's Encrypt SSL** para tu dominio
3. Forzar HTTPS (ya está en .htaccess)

### Paso 12: Optimizar para Producción

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer dump-autoload --optimize
```

## 📝 Checklist Final

- [ ] Base de datos creada y script SQL ejecutado
- [ ] Archivos subidos a Hostinger
- [ ] Dependencias instaladas (composer install)
- [ ] .env configurado correctamente
- [ ] Permisos de storage y bootstrap/cache
- [ ] index.php con rutas correctas
- [ ] .htaccess configurado
- [ ] SSL/HTTPS activado
- [ ] Cache optimizado
- [ ] Sitio accesible desde el dominio

## 🔐 Seguridad Post-Deployment

1. **Cambiar APP_KEY** si no lo hiciste:
   ```bash
   php artisan key:generate
   ```

2. **Desactivar debug**:
   ```env
   APP_DEBUG=false
   ```

3. **Proteger archivos sensibles**:
   - `.env` no debe ser accesible públicamente
   - Verifica que `vendor` y `storage` no sean públicos

4. **Actualizar credenciales** si las compartiste públicamente

## 📞 Soporte

Si tienes problemas:

1. **Logs de Laravel**: `storage/logs/laravel.log`
2. **Logs de Apache**: Panel de Hostinger → Error Logs
3. **Soporte de Hostinger**: https://www.hostinger.com/contact

## 🎉 ¡Listo!

Tu tienda de chocolatería debería estar funcionando en:
**https://indeminibaez.ec**

Los clientes pueden:
- Ver el catálogo
- Registrarse
- Comprar productos
- Ver sus facturas

Los administradores pueden:
- Gestionar productos
- Ver ventas
- Gestionar inventario
