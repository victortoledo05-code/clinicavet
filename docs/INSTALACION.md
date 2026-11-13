# Instalación y operación

La raíz pública debe ser exclusivamente `public/`. Configure `APP_ENV=production`, `APP_URL` con HTTPS, zona `America/Santiago` y un usuario MySQL de privilegios mínimos. `STORAGE_PATH` puede ser absoluto y debe apuntar fuera de `public`.

Antes de aplicar cualquier cambio a una base con datos, respáldela y revise el SQL. `estructura.sql` es únicamente para instalaciones nuevas; no debe reimportarse sobre una base existente.

Para respaldos consistentes deben copiarse base y `storage` en una misma ventana operativa. La frecuencia propuesta es diaria con 30 días de retención y copia externa, pendiente de confirmación y prueba de restauración.
