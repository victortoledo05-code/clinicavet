# Despliegue en producción

## 1. Código y servidor web

Clona el repositorio en una carpeta privada del servidor. La raíz pública del dominio debe ser exclusivamente `public/`. Activa HTTPS antes de habilitar el acceso de usuarios.

Ejemplo conceptual para Apache:

```apache
<VirtualHost *:443>
    ServerName clinica.ejemplo.cl
    DocumentRoot /var/www/clinicavet/public
    <Directory /var/www/clinicavet/public>
        Require all granted
        AllowOverride None
        Options -Indexes
    </Directory>
</VirtualHost>
```

## 2. Base de datos

Para una instalación nueva importa, en este orden:

```bash
mysql -u usuario -p clinicavet < database/estructura.sql
mysql -u usuario -p clinicavet < database/datos_iniciales.sql
```

`datos_iniciales.sql` es opcional y no crea credenciales. Para una base existente, realiza un respaldo y aplica solamente los cambios pendientes de `database/cambios/` en orden numérico.

## 3. Configuración privada

Copia `.env.example` a `.env`. Usa un usuario MySQL exclusivo, una contraseña fuerte, la URL HTTPS real y una ruta de almacenamiento fuera de `public/`. `.env` está excluido de Git.

```bash
cp .env.example .env
mkdir -p /var/lib/clinicavet/storage/{pacientes,atenciones,comprobantes,configuracion,logs}
chown -R www-data:www-data /var/lib/clinicavet/storage
chmod -R 750 /var/lib/clinicavet/storage
```

Adapta el usuario del servidor web y las rutas a tu proveedor. No otorgues escritura sobre el código de la aplicación.

## 4. Superadministrador

Crea la credencial real directamente en el servidor:

```bash
php scripts/crear_superadmin.php administrador@clinica.cl "Administrador principal"
```

La contraseña se solicita interactivamente, debe tener al menos 12 caracteres y se almacena como hash. No existe una contraseña universal ni una credencial de producción incluida en Git.

## 5. Verificación posterior

- Inicia sesión y cambia el nombre y logo desde Configuración.
- Confirma que se puedan cargar y visualizar fotos.
- Registra una atención de prueba, agrega un servicio y verifica el cobro.
- Registra una entrada y un consumo de stock.
- Comprueba los filtros de Atenciones, Seguimientos y Finanzas.
- Programa respaldos diarios conjuntos de la base y `STORAGE_PATH`, y prueba periódicamente una restauración.

No uses `php -S` en producción. El servidor integrado solo es apropiado para desarrollo local.
