# Avance

## Terminado

- Base PHP/PDO, entorno, sesiones, CSRF, roles, rutas cerradas y layout responsive.
- Esquema MySQL inicial con clínica, cuentas, inventario, finanzas y auditoría.
- Flujos base de tutores, pacientes, atenciones, aclaraciones, servicios, cargos, pagos, stock, finanzas, usuarios y configuración.
- Instalación y creación segura del primer superadministrador por CLI.
- Flujo operativo de recepción: búsqueda preventiva, selección de paciente existente y alta conjunta de tutor + mascota.
- Navegación guiada en cuatro pasos: recepción, atención clínica, servicios/insumos y cobro.
- Inicio orientado a tareas y continuidad directa entre llegada, ficha, consulta y cuenta.
- Conjunto demo local idempotente: 10 tutores, pacientes, fotografías sintéticas, atenciones y cuentas de ejemplo.
- Demo ampliada a 25 atenciones, 10 servicios, 9 productos/medicamentos, lotes y movimientos financieros.
- Alertas de inventario activables por producto con mínimo configurable y acceso desde la campana superior.
- Finanzas con filtros por rango, tipo y medio; tarjetas de resumen y detalle verificable de movimientos.
- Seguimientos internos para vacunas, desparasitación, controles, exámenes, medicamentos y procedimientos; incluye estados, filtros, prioridad y trazabilidad de contacto manual.

## Pendiente para completar la definición de terminado

- Carga y descarga privada de fotografías, evidencias y comprobantes.
- Edición controlada de atenciones, anulaciones, devoluciones y aprobación visual de descuentos.
- Entradas, ajustes, mermas, FEFO multi-lote y cierre de caja desde interfaz.
- CSV, informe imprimible, paginación completa y dashboard con todos los indicadores.
- Pruebas funcionales, de concurrencia, seguridad, accesibilidad y restauración.

## Validación local ejecutada el 13-11-2026

- Sintaxis PHP comprobada sin errores.
- Base local nueva creada con 22 tablas y migración `001_inicial`.
- Página de acceso respondió HTTP 200 mediante PHP 8.3 en `127.0.0.1:8080`.
- Las pruebas funcionales completas, concurrencia y restauración siguen pendientes.
