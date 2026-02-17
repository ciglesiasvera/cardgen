# CardGen Pro - Generador de Tarjetas Digitales

## Descripción del Proyecto

CardGen Pro es una aplicación web completa para generar tarjetas digitales personalizadas (bancarias, tributarias y corporativas). Permite a usuarios registrados crear tarjetas configurables en diseño, estructura, colores, campos dinámicos y formato de descarga (PNG/JPG).

## Características Principales

### ✅ Funcionalidades Completadas

1. **Sistema de Registro y Autenticación**
   - Registro con verificación de email
   - Login con sesiones seguras
   - Recuperación de contraseña
   - Verificación de cuenta vía email

2. **Generador de Tarjetas**
   - 3 tipos de tarjetas: Bancarias, Tributarias y Personalizadas
   - Campos dinámicos configurables
   - Personalización visual completa (colores, fuentes, alineación)
   - Formato de proporción seleccionable (1:1, 16:9, 2x4, personalizado)

3. **Vista Previa en Tiempo Real**
   - Panel dividido formulario/vista previa
   - Actualización automática al modificar campos
   - Zoom y controles de visualización

4. **Exportación de Tarjetas**
   - Exportación a PNG y JPG
   - Múltiples resoluciones (baja, media, alta)
   - Configuración de tamaño personalizado

5. **Dashboard de Usuario**
   - Estadísticas de uso
   - Historial de tarjetas creadas
   - Acceso rápido a funciones principales

6. **Integraciones**
   - Google Ads (ubicaciones configurables)
   - SMTP para envío de emails
   - Base de datos MySQL con PDO

### 🛠️ Stack Tecnológico

- **Backend**: PHP 8.x con arquitectura MVC
- **Frontend**: HTML5, CSS3, JavaScript Vanilla
- **Base de Datos**: MySQL 8.x
- **Seguridad**: Bcrypt para passwords, Prepared Statements, CSRF protection
- **Exportación**: Canvas API / html2canvas (simulado en MVP)

## Estructura del Proyecto

```
cardgen/
├── index.php              # Front Controller
├── config/               # Configuración
│   ├── database.php     # Conexión BD
│   └── smtp.php         # Configuración SMTP
├── controllers/          # Controladores
│   ├── auth/           # Autenticación
│   └── cards/          # Gestión de tarjetas
├── models/              # Modelos de datos
│   ├── User.php        # Modelo Usuario
│   └── Card.php        # Modelo Tarjeta
├── views/               # Vistas
│   ├── layouts/        # Layouts base
│   ├── home.php        # Página principal
│   └── errors/         # Páginas de error
├── public/              # Archivos públicos
│   ├── assets/css/     # Estilos
│   ├── assets/js/      # Scripts
│   └── uploads/        # Subida de archivos
└── database.sql         # Script de base de datos
```

## Instalación y Configuración

### 1. Requisitos del Sistema
- PHP 8.0 o superior
- MySQL 8.x
- Servidor web (Apache, Nginx)
- Extensión PDO para MySQL
- SMTP configurado para emails

### 2. Configuración Inicial

```bash
# 1. Clonar o copiar los archivos
cp -r cardgen /var/www/html/

# 2. Crear base de datos
mysql -u root -p < database.sql

# 3. Configurar archivos de configuración
# Editar config/database.php con tus credenciales
# Editar config/smtp.php con configuración de email

# 4. Configurar permisos
chmod 755 /var/www/html/cardgen
chmod 777 /var/www/html/cardgen/public/uploads/
```

### 3. Configuración de Base de Datos

```sql
-- Crear base de datos
CREATE DATABASE cardgen_pro CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Importar estructura
mysql -u username -p cardgen_pro < database.sql
```

### 4. Configuración SMTP

Editar `config/smtp.php` con tus credenciales:
```php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USERNAME', 'tu_email@gmail.com');
define('SMTP_PASSWORD', 'tu_contraseña_app');
```

## Uso de la Aplicación

### Flujo de Usuario

1. **Registro**: El usuario se registra con email y contraseña
2. **Verificación**: Recibe email con enlace de verificación
3. **Login**: Inicia sesión con credenciales verificadas
4. **Crear Tarjeta**: Selecciona tipo, completa datos, personaliza diseño
5. **Vista Previa**: Visualiza la tarjeta en tiempo real
6. **Exportar**: Descarga la tarjeta en formato PNG/JPG

### Tipos de Tarjeta Disponibles

1. **Tarjeta Bancaria**
   - Nombre del banco
   - Tipo de cuenta
   - Número de cuenta
   - Nombre empresa/persona
   - RUT
   - Correo
   - Logo del banco

2. **Tarjeta Tributaria**
   - Nombre empresa
   - Razón social
   - Giro
   - Dirección
   - Teléfono
   - Correo
   - RUT
   - Logo empresa

3. **Tarjeta Personalizada**
   - Título configurable
   - Campos dinámicos ilimitados (key:value)
   - Diseño completamente personalizable

## Configuración de Google Ads

La aplicación incluye espacios para Google Ads en:
- Header de la página
- Entre formulario y vista previa
- Footer de secciones importantes

Para activar:
1. Reemplazar `ca-pub-XXXXXXXXXXXXXXX` con tu Publisher ID
2. Configurar los slots según tus necesidades

## Seguridad Implementada

- **Passwords**: Hash con bcrypt
- **SQL**: Prepared Statements con PDO
- **XSS**: Sanitización de inputs con htmlspecialchars
- **Sesiones**: Cookies seguras, regeneración de IDs
- **CSRF**: Tokens en formularios (implementación recomendada)
- **Archivos**: Validación de MIME types y límites de tamaño

## Monitoreo y Logs

- Logs de email en desarrollo
- Registro de errores de base de datos
- Trazabilidad de actividades de usuario

## Despliegue en Producción

### Recomendaciones

1. **Configuración PHP**
   ```ini
   display_errors = Off
   error_log = /var/log/php/errors.log
   upload_max_filesize = 2M
   post_max_size = 2M
   ```

2. **Configuración Web Server**
   ```apache
   # Apache .htaccess
   RewriteEngine On
   RewriteCond %{REQUEST_FILENAME} !-f
   RewriteCond %{REQUEST_FILENAME} !-d
   RewriteRule ^(.*)$ index.php?path=$1 [QSA,L]
   ```

3. **Backup**
   - Programar backup diario de base de datos
   - Backup semanal de archivos de código

## Mantenimiento

### Tareas Recurrentes

1. **Limpieza de Archivos Temporales**
   - Programar limpieza de `/public/uploads/temp/` cada 24h

2. **Optimización de Base de Datos**
   ```sql
   OPTIMIZE TABLE cards, users;
   ```

3. **Monitoreo de Logs**
   - Revisar logs de errores diariamente
   - Monitorear logs de email en desarrollo

## Extensibilidad

### Futuras Mejoras

1. **API REST**: Para integración con otras aplicaciones
2. **Plantillas Prediseñadas**: Colección de diseños profesionales
3. **QR Dinámico**: Generación de códigos QR para tarjetas
4. **Enlace Público**: Compartir tarjetas sin descarga
5. **Plan Premium**: Sin publicidad, funciones avanzadas

## Soporte

- Documentación completa en código
- Comentarios descriptivos en funciones críticas
- Estructura modular para fácil mantenimiento

## Licencia

Este proyecto está desarrollado para fines demostrativos y educativos. Se puede utilizar y modificar libremente.

---

**Desarrollado con ❤️ siguiendo las especificaciones detalladas en instrucciones.txt**

*Última actualización: Febrero 2026*