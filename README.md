# ClinicVet

Sistema privado para la operación de una clínica veterinaria, desarrollado con PHP 8.3, MySQL 8, PDO, JavaScript nativo y CSS local. No requiere Node ni un framework.

## Requisitos de producción

- PHP 8.3 o superior con `pdo_mysql`, `fileinfo` y `mbstring`.
- MySQL 8.
- Apache 2.4 o Nginx con HTTPS.
- Acceso de escritura del usuario PHP exclusivamente al directorio configurado en `STORAGE_PATH`.
- El dominio debe apuntar únicamente a la carpeta `public/`, nunca a la raíz del repositorio.

## Instalación nueva

1. Crear una base de datos MySQL vacía con `utf8mb4_0900_ai_ci` y un usuario exclusivo con permisos sobre esa base.
2. Importar `database/estructura.sql`.
3. Opcionalmente importar `database/datos_iniciales.sql` para cargar catálogos y servicios iniciales. No crea usuarios ni datos clínicos.
4. Copiar `.env.example` como `.env` y completar el dominio HTTPS, la base de datos y una ruta privada para `STORAGE_PATH`.
5. Crear las carpetas `pacientes`, `atenciones`, `comprobantes`, `configuracion` y `logs` dentro de `STORAGE_PATH`, con permisos de escritura para PHP.
6. Configurar el servidor web con `public/` como raíz pública.
7. Crear el primer superadministrador:

   ```bash
   php scripts/crear_superadmin.php administrador@clinica.cl "Administrador principal"
   ```

   El comando solicitará una contraseña de al menos 12 caracteres sin guardarla en el repositorio.

8. Abrir `https://tu-dominio.cl/index.php?r=login` e ingresar con el correo y la contraseña recién definidos.

La credencial local `admin@clinicavet.local` no se incluye en una instalación nueva ni debe utilizarse en producción.

## Actualizar una instalación existente

Antes de actualizar, respaldar en la misma ventana operativa tanto la base MySQL como `STORAGE_PATH`. Revisar y aplicar, en orden, únicamente los archivos pendientes de `database/cambios/`; no volver a importar `estructura.sql` sobre una base con datos.

## Validación

Para revisar sintaxis antes de publicar:

```bash
find app public scripts -name '*.php' -print0 | xargs -0 -n1 php -l
```

La guía completa está en `docs/INSTALACION.md`.
