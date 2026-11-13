# ClinicVet

MVP privado para operación clínica veterinaria, construido con el stack INAUDIS definido en la especificación: PHP 8.3, MySQL 8, PDO, JavaScript nativo y CSS local. No requiere Node ni un framework.

## Requisitos

- PHP 8.3 con `pdo_mysql`, `fileinfo`, `mbstring` y `zip`.
- MySQL 8 y Apache 2.4 con `mod_rewrite`.
- HTTPS para producción.

## Instalación en Laragon

1. Crear una base MySQL vacía con cotejamiento `utf8mb4_0900_ai_ci`.
2. Importar, solo en una base nueva, `database/estructura.sql` y después `database/datos_iniciales.sql`.
3. Copiar `.env.example` a `.env` y completar URL y credenciales. El archivo real está ignorado por Git.
4. Configurar el virtual host con raíz en `C:/laragon/www/clinicalvet/public`; nunca exponer la raíz del repositorio.
5. Crear el primer usuario desde una terminal: `php scripts/crear_superadmin.php correo@dominio.cl "Nombre"`.
6. Verificar que el usuario del servidor web pueda escribir en `storage/`, pero que esa carpeta no sea pública.

Para una revisión local limitada puede usarse `php -S 127.0.0.1:8080 -t public`, pero el servidor integrado no reproduce las reglas y cabeceras de Apache y no debe usarse en producción.

No se ejecutaron migraciones ni pruebas en esta entrega: requieren autorización explícita según la especificación.
