# Instrucciones de Despliegue en DirectAdmin

## Problema Identificado
Error: `DNS_PROBE_FINISHED_NXDOMAIN` al acceder a `cardgen.skylabs.cl`

## Solución Paso a Paso

### 1. Verificar Propagación DNS
Ya configuraste el registro DNS correctamente:
- **Subdominio:** `cardgen`
- **Tipo:** `A`
- **IP:** `107.167.86.210`
- **TTL:** `3600`

**La propagación DNS puede tardar:**
- Normalmente: 30 minutos a 4 horas
- Máximo: 24-48 horas (raro)

**Para verificar propagación:**
```cmd
nslookup cardgen.skylabs.cl
```
o
```cmd
ping cardgen.skylabs.cl
```

### 2. Verificar Estructura de Archivos en DirectAdmin

Dijiste que subiste los archivos a `public_html/` directamente. Esto es **INCORRECTO** para la aplicación CardGen.

**Estructura CORRECTA necesaria:**
```
public_html/cardgen/           ← DEBE CREAR ESTA CARPETA
├── index.php
├── .htaccess
├── config/
├── controllers/
├── models/
├── views/
├── public/
└── core/
```

**Pasos para corregir:**
1. Accede a **File Manager** en DirectAdmin
2. Ve a `public_html/`
3. Crea una carpeta llamada `cardgen`
4. Mueve TODOS los archivos de CardGen a `public_html/cardgen/`
5. Asegúrate de que `index.php` esté en `public_html/cardgen/index.php`

### 3. Verificar Configuración del Servidor

**Crear archivo de prueba:**
1. En File Manager, ve a `public_html/cardgen/`
2. Crea un archivo llamado `test.php` con este contenido:
```php
<?php
phpinfo();
?>
```
3. Accede a: `http://cardgen.skylabs.cl/test.php`

**Si ves información de PHP → El servidor funciona**
**Si ves error 404 → La carpeta no está en el lugar correcto**
**Si ves error 500 → Problema de configuración PHP**

### 4. Configurar Base de Datos

**En DirectAdmin:**
1. Ve a **MySQL Management**
2. Crea una base de datos llamada `cardgen_pro`
3. Crea un usuario para la base de datos
4. Otorga todos los privilegios al usuario sobre `cardgen_pro`

**Importar estructura:**
1. Ve a **phpMyAdmin** (desde MySQL Management)
2. Selecciona la base de datos `cardgen_pro`
3. Ve a la pestaña **Importar**
4. Selecciona el archivo `database.sql` desde tu computadora
5. Haz clic en **Ejecutar**

### 5. Configurar Archivos de Configuración

**Editar `config/database.php`:**
```php
<?php
// Conexión a la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'cardgen_pro');
define('DB_USER', 'tu_usuario_mysql');     // ← CAMBIAR
define('DB_PASSWORD', 'tu_password_mysql'); // ← CAMBIAR

function startSecureSession() {
    // ... código existente ...
}
?>
```

**Editar `config/smtp.php`** (si usas SMTP para emails):
```php
<?php
define('SMTP_HOST', 'smtp.tuservidor.com');
define('SMTP_USERNAME', 'tu_email@skylabs.cl');
define('SMTP_PASSWORD', 'tu_password_smtp');
// ... resto del código ...
?>
```

### 6. Configurar Permisos

**En File Manager, establecer permisos:**
```
public_html/cardgen/public/uploads/ → 755 (o 777 si hay problemas)
public_html/cardgen/public/uploads/temp/ → 755 (o 777)
```

### 7. Probar la Aplicación

Una vez configurado todo, prueba:
1. `http://cardgen.skylabs.cl/` → Debe mostrar la página de inicio
2. `http://cardgen.skylabs.cl/register` → Formulario de registro
3. `http://cardgen.skylabs.cl/login` → Formulario de login

### 8. Problemas Comunes y Soluciones

#### A) Error 404 - Página no encontrada
- **Causa:** Archivos en `public_html/` en lugar de `public_html/cardgen/`
- **Solución:** Mover archivos a la carpeta `cardgen`

#### B) Error 500 - Error interno del servidor
- **Causa:** Problema con `.htaccess` o PHP
- **Solución:** Temporalmente renombrar `.htaccess` a `.htaccess.bak` para probar

#### C) Error de conexión a base de datos
- **Causa:** Credenciales incorrectas en `config/database.php`
- **Solución:** Verificar usuario/contraseña de MySQL

#### D) Error DNS_PROBE_FINISHED_NXDOMAIN
- **Causa:** DNS no propagado o configuración incorrecta
- **Solución:** Esperar propagación o verificar registro DNS

### 9. Acceso Directo para Pruebas

**Para verificar sin DNS:**
1. Edita el archivo `hosts` en tu computadora
2. Agrega esta línea:
```
107.167.86.210 cardgen.skylabs.cl
```
3. Guarda el archivo
4. Ahora puedes acceder directamente por IP

**Ubicación del archivo hosts:**
- Windows: `C:\Windows\System32\drivers\etc\hosts`
- Mac/Linux: `/etc/hosts`

### 10. Contactar Soporte

Si después de seguir estos pasos aún tienes problemas:

1. **Contacta a tu proveedor de hosting** para:
   - Verificar que el servidor web esté activo
   - Verificar que PHP esté instalado
   - Verificar configuración de dominios

2. **Proporciona esta información:**
   - URL del error
   - Captura de pantalla
   - Pasos que seguiste
   - Archivo `test.php` creado

### Resumen de Acciones Críticas

1. ✅ **DNS Configurado** - cardgen → 107.167.86.210
2. ⚠️ **Mover archivos** a `public_html/cardgen/`
3. ⚠️ **Configurar base de datos** MySQL
4. ⚠️ **Actualizar archivos de configuración**
5. ⚠️ **Probar con archivo `test.php`**

**Tiempo estimado para resolución:** 1-2 horas después de corregir la estructura de archivos.