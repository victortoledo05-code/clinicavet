# ClinicVet — Especificación e instrucciones para construir el MVP

Versión: 1.0 · Fecha: 13 de noviembre de 2026
Responsable del producto: Víctor Toledo / INAUDIS
Estado: especificación para implementación; no representa software construido ni probado.

## 1. Instrucción inicial para Codex o Cursor

Actúa como desarrollador senior de PHP y MySQL. Construye este MVP completo siguiendo este documento, con programación simple, interfaz profesional y reglas consistentes. El resultado debe poder instalarse en Laragon y luego en un servidor web.

Antes de editar, inspecciona el proyecto y lee su AGENTS.md. Si existe código, identifica lo reutilizable y limita los cambios al alcance solicitado. Si está vacío, crea la estructura descrita. No reemplaces ni borres trabajo existente sin autorización.

No entregues pantallas vacías, botones sin acción, datos simulados presentados como reales ni funciones críticas pendientes. No afirmes que algo funciona si no lo verificaste. No prometas ausencia absoluta de errores.

Trabaja por las etapas de la sección 17. Mantén un registro breve de lo terminado, pendiente y bloqueado. Si falta una decisión realmente bloqueante, realiza una sola pregunta concreta; no preguntes otra vez por decisiones ya resueltas aquí.

Antes de ejecutar pruebas, pide autorización a Víctor. Puedes escribir el plan de validación sin ejecutarlo. Antes de ejecutar migraciones o acciones sobre una base existente, presenta el cambio y pide autorización. Generar los archivos SQL forma parte de la implementación; ejecutarlos en producción no está autorizado por este documento. No publiques ni conectes datos reales sin instrucción explícita.

## 2. Objetivo y contexto

Aplicación web privada para una clínica veterinaria. Debe reunir pacientes, tutores, atenciones, fotos y documentos, servicios, stock, cobros, gastos e indicadores. Su propósito es ordenar la operación diaria y entregar información verificable al dueño.

Flujo principal: buscar tutor o paciente → registrar atención → registrar servicios e insumos → determinar cobro → registrar pago o saldo → revisar operación y finanzas.

El sistema actual es CPVet de escritorio. Las pantallas compartidas muestran ficha de mascota, tutor, fotografía, consultas fechadas, historia libre, diagnóstico, tratamiento y próxima cita. Conservar esa sencillez; no copiar su interfaz antigua ni su código.

Hay un contador visible de 632 mascotas en las capturas: es una referencia visual, no un volumen confirmado ni datos para precargar.

## 3. Decisiones confirmadas y valores propuestos

### Confirmado por el cliente

- Aplicación web en línea, accesible desde computador, tablet y celular.
- Programación simple con PHP y MySQL; organización por módulos, sin framework propio.
- Dos perfiles: superadministrador y administrador.
- Superadministrador ve todo y mantiene usuarios y configuración.
- Administrador registra pacientes, atenciones y pagos; no ve finanzas.
- Fotografía de perfil por paciente.
- Múltiples evidencias por atención: fotografías, imágenes, Word y PDF.
- Medios de pago: efectivo, transferencia y tarjeta. Registro de pagos, no procesamiento bancario.
- Stock con unidades, cajas, frascos o mililitros y equivalencias por producto.
- Mínimo configurable por producto y alertas en campana superior al llegar al mínimo o menos.
- Descuentos y gratuidades deben conservar motivo, responsable y costos.
- Gastos y resumen financiero: solo superadministrador.
- Diseño legible, botones grandes y navegación simple.

### Criterios de implementación propuestos

Aplicarlos como valores iniciales ajustables; no presentarlos como condiciones comerciales confirmadas:

- PHP 8.3, MySQL 8, PDO y JavaScript nativo. HTML renderizado en servidor.
- CSS local sencillo con variables; no exigir Node para ejecutar el sistema. Si ya existe Tailwind compilado, reutilizarlo. No cargar un framework CSS desde CDN en producción.
- Zona horaria America/Santiago; persistir instantes en UTC y mostrarlos localmente. Fechas clínicas sin hora se guardan como DATE.
- Moneda CLP; montos en pesos enteros. Stock en DECIMAL(14,4), nunca float.
- Límite inicial: 10 MB por archivo, 10 adjuntos por envío; límites configurables y mensajes compatibles con límites PHP/servidor.
- Archivo de informe imprimible con opción del navegador para guardar PDF; Word adjunto se descarga, no se ejecuta ni convierte.
- Sesión inactiva: 30 minutos; advertir antes y evitar pérdida silenciosa de texto.
- Alerta de vencimiento inicial: 30 días, configurable.
- Backups: frecuencia y retención a confirmar antes de producción; proponer diario y 30 días, con copia externa.

## 4. Alcance del MVP y exclusiones

Incluye: acceso, usuarios, tutores, pacientes, atenciones, evidencias privadas, informes, catálogo de servicios, inventario por lotes, consumos, cobros, abonos, descuentos, gratuidades, gastos, cierre de efectivo, dashboard y exportación CSV autorizada.

Fuera del MVP: agenda completa de reservas, reservas públicas, hospitalización, IA, WhatsApp automático, portal de tutores, facturación electrónica, contabilidad tributaria, conciliación bancaria automática, pasarela de pagos, multisucursal e integraciones con laboratorio. Sí incluye una fecha de próximo control en la atención.

La migración de CPVet es una tarea posterior condicionada a recibir copia de sus datos. No bloquear el desarrollo por ella. No inventar historial ni saldos anteriores.

Los valores comerciales de implementación y mensualidad no se incorporan a las pantallas del producto ni a su lógica de cobro a pacientes.

## 5. Permisos

Los permisos deben comprobarse en servidor tanto en páginas como en acciones, descargas, búsquedas, exportaciones y consultas del dashboard. Ocultar menús no basta.

| Acción | Superadministrador | Administrador |
|---|---|---|
| Consultar y registrar tutores/pacientes | Sí | Sí |
| Registrar y consultar atenciones/evidencias | Sí | Sí |
| Registrar consumo de insumos en atención | Sí | Sí |
| Consultar cantidades y alertas de stock | Sí | Sí |
| Registrar entradas con cantidad/lote/vencimiento | Sí | Sí, sin costos |
| Cambiar equivalencias o mínimo del producto | Sí | No |
| Registrar merma y ajuste de inventario | Sí | No |
| Ver o editar costos de compra y valorización | Sí | No |
| Consultar catálogo de servicios y tarifa al cobrar | Sí | Sí |
| Crear o cambiar tarifas de servicios | Sí | No |
| Ver cuenta de atención y registrar pagos | Sí | Sí |
| Autorizar descuentos o gratuidades | Sí | No |
| Anular pagos, cargos o atenciones finalizadas | Sí, con motivo | No |
| Gastos, cierres y reportes financieros globales | Sí | No |
| Dashboard operativo | Sí | Sí |
| Dashboard económico y exportaciones financieras | Sí | No |
| Mantener usuarios y configuración | Sí | No |
| Ver auditoría completa | Sí | No |

El administrador ve importes de la atención que está cobrando y su saldo; no recibe totales globales, costos, gastos ni resultados financieros. No enviar esos datos ocultos en HTML o JSON.

La división detallada de permisos de stock anterior es una propuesta conservadora para concretar los dos perfiles; documentarla y permitir su revisión sin crear más roles en el MVP.

## 6. Diseño y experiencia

### Paleta

| Token | Valor | Aplicación |
|---|---|---|
| primario | #176B63 | Botones principales y navegación |
| suave | #E8F4EE | Fondos de sección y estados positivos |
| fondo | #F7F9F8 | Fondo general |
| texto | #24343A | Texto principal |
| alerta | #B86A08 | Avisos de stock y pendientes |
| peligro | #B42318 | Errores y acciones delicadas |
| superficie | #FFFFFF | Formularios y tarjetas |

Usar texto oscuro en fondos claros. Verificar contraste en combinaciones finales; el color de paleta no garantiza por sí solo accesibilidad. Incluir texto e icono para estados.

- Fuente de sistema, mínimo 16 px en contenido y formularios; títulos de 24 a 32 px.
- Botones mínimo 48 px de alto; nombre visible, no depender de iconos.
- Acciones primarias: Nuevo paciente, Nueva atención, Registrar pago y Guardar.
- Guardar en posición consistente. Cancelar secundario. No colocar eliminar junto a guardar sin separación.
- Etiquetas sobre campos; no sustituirlas por placeholders.
- Foco visible, navegación con teclado, modales accesibles y mensajes junto al campo.
- Listados paginados, búsqueda y filtros preservados al volver de una ficha.
- Confirmar anulaciones y advertir al salir con cambios sin guardar.
- Estados de carga, botón deshabilitado al enviar y confirmación clara de guardado.
- Sin reproducción automática de sonidos, animaciones excesivas o imágenes decorativas que dificulten trabajar.
- Datos de ejemplo únicamente en modo demo explícito, separados de producción.

### Distribución

Escritorio: menú lateral, barra superior con búsqueda y campana, contenido amplio.
Celular: menú plegable, una columna, acciones fáciles de pulsar y listados en tarjetas. No ocultar información crítica por falta de ancho.

Menú administrador: Inicio, Pacientes, Tutores, Atenciones, Stock, Cobros.
Menú superadministrador: lo anterior + Servicios, Finanzas, Usuarios, Configuración y Auditoría.

Campana: contador de productos con stock bajo o agotado. Mostrar nombre, disponible, unidad y mínimo, con enlace al producto. Actualizar tras movimientos y al cargar la página; no se requiere WebSocket. El aviso desaparece al superar el mínimo. Marcar como leído no resuelve una falta de stock.

### Pantallas prioritarias

1. Inicio: tarjetas operativas y alertas; datos económicos solo según perfil.
2. Pacientes: buscar por nombre, código, tutor o teléfono; foto en listado.
3. Ficha: foto y nombre; resumen, tutor, alertas clínicas e historial cronológico.
4. Nueva atención: información clínica, evidencias, insumos y servicios en secciones legibles.
5. Cobro: tarifa, descuentos autorizados, total, pagos, saldo y botón Registrar pago.
6. Stock: cantidades y alertas; costos únicamente para superadministrador.
7. Finanzas: filtros por fechas, ingresos recibidos, egresos, pendientes y cierre de efectivo.

## 7. Datos clínicos

### Tutor

Nombre obligatorio; identificación/RUT opcional, email, teléfono principal, teléfono alternativo, dirección y observaciones. Al menos un medio de contacto o motivo explícito de ausencia. No exigir RUT para registrar al paciente. Un tutor puede tener varias mascotas. No fusionar personas automáticamente por compartir teléfono.

### Paciente

Código único interno, tutor, nombre, fotografía, especie, raza, sexo (macho/hembra/no informado), color, nacimiento conocido o edad estimada, condición reproductiva, microchip opcional, antecedentes, alertas clínicas y estado activo/inactivo/fallecido.

No exigir fecha exacta si se desconoce. Calcular edad a la fecha de consulta; no guardar una edad fija que se vuelva obsoleta. Peso actual se deriva del último peso clínico válido.

### Atención

Paciente, fecha y hora clínica, usuario autor, profesional responsable, motivo, historia libre, examen/observaciones, peso opcional, diagnóstico, tratamiento, indicaciones, próximo control y estado borrador/finalizada/anulada.

Permitir texto clínico libre y campos separados, sin un cuestionario extenso. No generar diagnósticos ni dosis automáticamente.

Al finalizar exigir motivo, registro clínico y profesional; permitir diagnóstico pendiente. Cada consulta mantiene su propio peso y documentos. La próxima cita es un dato de seguimiento, no crea una reserva de agenda.

Finalizada: no sobrescribir silenciosamente. Permitir agregar una aclaración fechada; correcciones de contenido y anulaciones por superadministrador con motivo e historial. La anulación clínica no borra pagos ni revierte consumos automáticamente: revisar sus movimientos relacionados.

## 8. Fotos y evidencias

- Una foto de perfil vigente por paciente; JPEG, PNG o WebP.
- Varios adjuntos por atención: JPEG, PNG, WebP, PDF, DOC y DOCX.
- Descripción opcional, nombre original, MIME validado, tamaño, fecha, autor, paciente y atención.
- Previsualizar imágenes y PDF mediante endpoint autenticado; Word se descarga como adjunto.
- Guardar en directorio privado fuera de public con nombre aleatorio, nunca con ruta recibida del usuario.
- Validar extensión y contenido MIME; para DOCX validar estructura de paquete Office. Rechazar ejecutables, SVG, HTML, archivos con macros y archivos que excedan límites. Un filtro MIME no equivale a un antivirus.
- No ejecutar documentos. Agregar análisis antimalware si existe infraestructura disponible, sin fingir que fue aplicado.
- Downloads con autorización, Content-Type correcto, nosniff y cabeceras de disposición apropiadas.
- Comprobar pertenencia del archivo al paciente/atención solicitado. Evitar acceso directo por ruta o ID adivinable sin permiso.
- Sustituciones o retiros de evidencia deben conservar trazabilidad; no eliminar físicamente evidencia clínica finalizada desde la interfaz común.
- Manejar fallos entre disco y base de datos: limpiar temporales, no dejar referencias rotas y registrar errores.

## 9. Servicios, descuentos y cobros

Catálogo: nombre, categoría, tarifa vigente, activo/inactivo. La atención puede contener varios servicios. Copiar descripción y precio al cargo para conservar el valor histórico aunque cambie el catálogo.

Cuenta por atención con líneas: servicio, cantidad, precio unitario original, descuento aprobado y total final. Monto inicial en CLP entero; sin cálculos tributarios ni documentos fiscales automáticos.

- Descuento fijo por línea o porcentaje convertido a importe entero, calculado en servidor con redondeo documentado.
- No permitir descuento mayor que la línea ni total negativo.
- Para gratuidad usar descuento 100%, motivo y superadministrador autorizante. Total cero no genera pago ficticio.
- Administrador puede solicitar descuento mediante registro pendiente; mientras no se apruebe no reduce el cobro.
- Servicio gratuito no es merma y no elimina consumos.
- Deuda no es descuento ni gratuidad.
- Pagos con importe, fecha, medio, usuario y referencia opcional; referencia requerida para transferencia/tarjeta como criterio inicial.
- Registrar tarjeta o transferencia no confirma el pago con un banco. Mostrar que es un registro manual.
- Permitir pagos divididos por medios distintos y abonos en fechas diferentes.
- No permitir pagar más del saldo en MVP. No manejar anticipos no asignados.
- Pago y actualización de saldo dentro de transacción; bloquear cuenta mientras se valida para evitar sobrepago concurrente.
- Anular pago mediante reverso relacionado, con motivo; no borrar el registro.
- Impedir reducir un cobro por debajo de lo ya pagado: requerir resolver primero el excedente. Devoluciones completas o parciales por superadministrador, relacionadas a pagos originales, sin superar su importe neto disponible.
- Cambios de precio no afectan cuentas anteriores.
- Comprobante interno debe decir que no es boleta/factura tributaria.

## 10. Stock, unidades y costos

Cada producto tiene: código, nombre, categoría, unidad base de consumo, presentación de compra, factor de conversión, mínimo, activo/inactivo. Cada lote tiene código opcional, vencimiento cuando aplique, saldo y costo unitario base.

Ejemplos:

- Caja de 10 unidades: entrada 2 cajas = 20 unidades; consumo 3 unidades deja 17.
- Frasco de 100 ml: entrada 1 frasco = 100 ml; consumo 2,5 ml deja 97,5 ml.
- No convertir mg a ml automáticamente: eso requeriría concentración y reglas clínicas fuera de este control de inventario.

El mínimo se expresa en unidad base, visible junto al campo. Con mínimo 3 unidades se alerta cuando disponible <= 3.

Movimientos: apertura inicial, entrada, consumo en atención, merma, ajuste positivo/negativo y reverso relacionado. Todos con fecha, usuario y motivo o documento de origen.

- Consumo: vincular atención y lote, cantidad base y costo histórico.
- Lotes vencidos no pueden consumirse; mostrar stock físico y utilizable por separado. Alertar por stock utilizable.
- Selección por vencimiento más próximo entre lotes válidos (FEFO); permitir consumo de varios lotes dejando movimientos individuales.
- No permitir saldo negativo. Aplicar bloqueo de lote y transacción para concurrencia.
- Una acción repetida no debe duplicar consumos; usar identificador de operación único.
- Merma: vencido, dañado, perdido u otro, con motivo. Solo superadministrador.
- Ajuste: cantidad contada, diferencia y motivo; nunca editar el saldo directamente sin movimiento.
- Reversos no borran el movimiento original; validar disponibilidad y límite de reverso.
- Cambios de factor de conversión no alteran movimientos anteriores; impedir cambiar unidad base si ya hay movimientos.
- Administrador registra cantidad, lote y vencimiento de entradas; costo queda pendiente para superadministrador. Costo desconocido es NULL, no cero.
- Costos por lote se copian a consumos al contabilizarlos. Costos faltantes deben marcar el reporte como incompleto. Si se regularizan después, registrar ese cambio y conservar auditoría.

## 11. Finanzas y cierre

Fuentes de ingresos por atenciones: pagos válidos menos devoluciones. No agregar otra fila manual de ingreso por el mismo pago.

Otros ingresos y gastos: categoría, descripción, fecha, importe, medio, responsable y comprobante opcional privado. Solo superadministrador.

Compra de stock y pago al proveedor son eventos distintos: puede entrar mercadería hoy y pagarse después. Permitir relacionar gasto con entrada, pero no generar ambos silenciosamente ni duplicar egreso.

Separar dos vistas:

- Flujo de dinero: cobros recibidos + otros ingresos - egresos pagados - devoluciones.
- Aporte de atenciones: cargos finales - costo de insumos consumidos, solo si costos completos. No llamarlo utilidad neta.

No descontar a la vez compras y consumo de las mismas existencias para calcular una supuesta utilidad.

Cierre de efectivo: saldo inicial + entradas en efectivo - salidas/devoluciones en efectivo = efectivo esperado; registrar contado, diferencia y motivo. Tarjetas y transferencias se muestran separadas, no se suman a efectivo esperado.

Definir cortes no superpuestos y usuario que cierra. Un período cerrado bloquea movimientos nuevos dentro de él; correcciones se registran en período abierto mediante reversos y referencia al original. Superadministrador puede reabrir con motivo y auditoría, invalidando el cierre previo hasta volver a cerrarlo.

## 12. Dashboard e indicadores

Filtros: hoy, últimos 7 días, mes actual y rango personalizado. Mostrar fechas exactas y comparar períodos equivalentes. Nunca dividir por cero: sin base previa mostrar 'Sin base de comparación'.

### Operativo, ambos perfiles

- Pacientes únicos atendidos: distintos pacientes con atención finalizada en período.
- Atenciones finalizadas: cantidad; excluir anuladas y borradores.
- Tutores nuevos registrados y tutores atendidos, como métricas distintas.
- Recurrentes: tutores atendidos en período que ya tenían atención finalizada anterior al inicio del período.
- Próximos controles registrados, stock bajo, agotados y vencimientos.

### Económico, solo superadministrador

- Valor de servicios antes de descuentos.
- Descuentos aprobados y gratuidades separados sin doble conteo: gratuidad total en su categoría; descuento parcial en la otra.
- Cargos finales emitidos en período, distintos a dinero recibido.
- Cobros netos recibidos por fecha de pago y medio, con devoluciones identificadas.
- Deuda total a fecha de corte: cargos vigentes menos pagos netos aplicados hasta esa fecha; no mezclar con deuda generada solo en el período.
- Gastos pagados y flujo de dinero.
- Ticket medio: cargos finales / atenciones finalizadas con cuenta; indicar si incluye gratuitas (por defecto sí).
- Costos conocidos consumidos y cantidad de atenciones con costo incompleto.
- Mermas por cantidad y costo conocido.
- Tendencias mensuales de actividad y cobros; sin predicciones inventadas.

Cada tarjeta lleva al listado que permite comprobarla con los mismos filtros. Evitar sumas duplicadas por JOIN entre múltiples pagos, servicios y consumos: agregar cada fuente por separado antes de unir.

Si no hay datos mostrar estado vacío, no gráficas fabricadas. Mantener cifras CLP, unidades y rango visibles. Las proyecciones avanzadas quedan fuera del MVP.

## 13. Modelo de datos propuesto

Implementar con InnoDB, utf8mb4, claves foráneas, índices y fechas de creación/actualización. No borrar en cascada historias, pagos ni stock. Usar desactivación/anulación.

| Tabla | Contenido y relaciones principales |
|---|---|
| usuarios | Nombre, email único, password_hash, rol, activo, último acceso |
| tutores | Identidad, contactos, domicilio, estado |
| pacientes | Código único, tutor_id, datos de mascota, nacimiento/edad estimada, antecedentes y estado |
| atenciones | paciente_id, autor_id, responsable_id, fecha, clínica, peso, próximo control, estado |
| aclaraciones_atencion | atencion_id, texto, autor y fecha; correcciones con motivo |
| archivos | paciente_id, atencion_id nullable, tipo perfil/evidencia/comprobante, ruta privada, MIME, tamaño y autor |
| servicios | Catálogo y tarifa vigente |
| cuentas | atencion_id único, estado y versión de edición |
| cargos | cuenta_id, servicio_id nullable, descripción/precio histórico, cantidad y estado |
| descuentos | cargo_id, importe, tipo parcial/gratuidad, motivo, estado, solicitante y autorizante |
| pagos | cuenta_id, importe, medio, fecha, referencia, autor y operación única |
| devoluciones | pago_id, importe, medio, fecha, motivo y autor |
| anulaciones_pago | pago_id único, fecha, motivo y autor; excluir del saldo una sola vez |
| productos | Unidad base, presentación, factor, mínimo y estado |
| lotes | producto_id, lote, vencimiento, saldo base y costo base nullable |
| movimientos_stock | lote_id, atencion_id nullable, tipo, cantidad con signo, costo histórico, motivo, reverso_id y operación única |
| movimientos_financieros | Otros ingresos/gastos, categoría, medio, importe, fecha, relación opcional a entrada de stock y estado |
| cierres_caja | Intervalo, inicial, esperado, contado, diferencia, estado y autor |
| catalogos | Categorías/unidades/motivos configurables, desactivables |
| configuracion | Datos de clínica y parámetros sin secretos |
| auditoria | Usuario, acción, entidad, ID, fecha, motivo y diferencias relevantes |
| schema_migrations | Versiones SQL aplicadas |

Los saldos almacenados que se usen por eficiencia deben actualizarse transaccionalmente y poder reconciliarse con los movimientos. No tomar importes calculados por el navegador como autoridad.

No crear un constructor genérico de tablas, un ORM propio ni un motor de permisos complejo. Helpers específicos para stock y cobros se justifican para mantener sus reglas en un único lugar.

## 14. Árbol de archivos simple

```text
clinicavet/
├── public/
│   ├── index.php
│   ├── .htaccess
│   └── assets/
│       ├── estilos.css
│       ├── funciones.js
│       └── logo.svg
├── app/
│   ├── bootstrap.php
│   ├── config.php
│   ├── conexion.php
│   ├── seguridad.php
│   ├── archivos.php
│   ├── auditoria.php
│   ├── stock_funciones.php
│   ├── cobros_funciones.php
│   ├── indicadores.php
│   ├── comunes/             # encabezado.php, menu.php, alertas.php, pie.php
│   ├── acceso/              # login.php, acciones.php
│   ├── inicio/              # dashboard.php
│   ├── tutores/             # listado.php, formulario.php, acciones.php
│   ├── pacientes/           # listado.php, formulario.php, ficha.php, acciones.php
│   ├── atenciones/          # listado.php, formulario.php, detalle.php, informe.php, acciones.php
│   ├── servicios/           # listado.php, formulario.php, acciones.php
│   ├── stock/               # listado.php, producto.php, movimientos.php, acciones.php
│   ├── cobros/              # listado.php, detalle.php, acciones.php
│   ├── finanzas/            # resumen.php, gastos.php, cierre.php, acciones.php
│   ├── usuarios/            # listado.php, formulario.php, acciones.php
│   ├── configuracion/      # clinica.php, catalogos.php, acciones.php
│   └── auditoria/           # listado.php
├── storage/
│   ├── pacientes/
│   ├── atenciones/
│   ├── comprobantes/
│   └── logs/
├── database/
│   ├── estructura.sql
│   ├── datos_iniciales.sql
│   └── cambios/
├── scripts/
│   ├── crear_superadmin.php
│   ├── migrar.php
│   └── respaldo.php
├── docs/
│   ├── INSTALACION.md
│   ├── MANUAL.md
│   ├── VALIDACION.md
│   └── AVANCE.md
├── .env.example
├── .gitignore
└── README.md
```

No crear archivos vacíos por cumplir el árbol. Crear cada pantalla y acción al implementar su módulo. .env real es local, ignorado por Git, fuera de public.

public/index.php resuelve rutas mediante un mapa cerrado de pantalla/acción, método HTTP y permiso. Nunca incluir un archivo directamente desde un parámetro de URL. Descargas también pasan por este punto y validan permisos.

Scripts solo CLI; no instalación abierta mediante navegador. Los imports compartidos usan __DIR__, no dependen del directorio de ejecución.

## 15. Seguridad y consistencia

- PDO preparado; listas permitidas para nombres de ordenamiento, sin SQL concatenado con entradas.
- Password hash y verificación nativos; no contraseñas en texto, seeds o repositorio.
- Creación inicial de superadmin por CLI con contraseña introducida de forma segura. No credencial universal.
- Sesión regenerada al login, cookies HttpOnly, SameSite y Secure bajo HTTPS; logout por POST con CSRF.
- Límite de intentos de acceso persistente y mensajes que no revelen existencia de cuentas.
- Tokens CSRF en todas las mutaciones y validación del método HTTP.
- Escapar salidas; texto clínico plano por defecto. No HTML libre.
- Validación en servidor, límites de campos y archivos, errores legibles sin mostrar SQL ni secretos.
- Evitar modificación de entidades que no coincidan con IDs relacionados de la solicitud.
- Usar versión/updated_at para detectar ediciones simultáneas y ofrecer recargar, sin sobreescribir silenciosamente.
- Identificador de operación para pagos, devoluciones, cierres y movimientos de stock; prevenir doble envío también en servidor.
- Transacciones para operaciones relacionadas y tratamiento claro de rollback.
- Auditoría de modificaciones relevantes sin registrar contraseñas, secretos ni archivos completos.
- Bloquear desactivación del último superadministrador activo.
- CSV: neutralizar fórmulas al exportar campos que comiencen con =, +, -, @ o controles; respetar permisos y filtros.
- Copia externa de base y archivos, secretos fuera de Git. Documentar restauración y consistencia temporal del respaldo.

## 16. Migración futura de CPVet

Referencia documental identificada anteriormente: sitio oficial del autor, https://www.cpimario.com/faqcpvet.html y https://www.cpimario.com/index4.html . Revalidar documentación si se utiliza para escribir el importador.

La documentación indica CPVETDAT.MDB para datos clínicos, Farmacos.mdb para catálogo y carpetas Fotos e Imagenes. No conocemos aún el esquema real, versión, protección ni integridad del respaldo de esta clínica.

No programar importador ficticio ni asumir que el catálogo de fármacos contiene stock. Cuando llegue una copia, inventariar tablas y archivos en modo lectura, mapear IDs y conservar identificador original. Presentar recuentos, duplicados, campos mal utilizados y adjuntos sin relación. No corregir historias clínicas automáticamente ni inferir ingresos históricos.

Importar inicialmente a entorno separado, con autorización y reconciliación de cantidades antes de migración definitiva. Nunca convertir o reparar los archivos originales.

## 17. Plan de implementación y entregables

1. **Inspección y base:** estructura, configuración, conexión, rutas, login, roles, layout y usuario inicial CLI.
2. **Clínica:** tutores, pacientes, foto, consultas, adjuntos privados, historial y reporte imprimible.
3. **Cobros:** servicios, cargos históricos, descuentos autorizados, gratuidad, pagos, saldos y reversos.
4. **Stock:** unidades, lotes, entradas, consumo transaccional, merma, ajustes y campana.
5. **Control:** otros ingresos, gastos, cierre de efectivo y dashboard con detalle verificable.
6. **Entrega:** documentación de instalación, manual, SQL completo, plan de validación y listado honesto de pendientes.

En cada etapa conservar un flujo utilizable y actualizar docs/AVANCE.md. El dashboard final se alimenta de datos reales de módulos anteriores.

No desplegar al terminar automáticamente. Entregar proyecto instalable y pasos concretos. Si no hay autorización para pruebas, indicar explícitamente 'No ejecutadas: pendientes de autorización', no 'validado'.

## 18. Criterios de aceptación y plan de validación (ejecutar solo con autorización)

| Caso | Resultado esperado |
|---|---|
| Tutor con dos mascotas | Una identidad de tutor y dos fichas independientes |
| Paciente sin nacimiento exacto | Admite edad estimada y la distingue de fecha conocida |
| Perfil y evidencias | Foto visible y varios adjuntos asociados a la atención correcta |
| Acceso directo sin permiso | Deniega página, acción, descarga, datos y exportación |
| Administrador cobra | Registra pago y ve saldo de la atención sin acceder a finanzas globales |
| Atención normal | Cargo, consumo, pago y dashboard coinciden |
| Descuento | Conserva tarifa, motivo, autorización y cobro final |
| Gratuita | Total cero, sin pago ficticio; conserva actividad y costo de insumos |
| Abono mixto | Pagos parciales por distintos medios; saldo correcto |
| Doble clic o reintento | Un solo pago o consumo para la misma operación |
| Dos cobros simultáneos | No permite sobrepagar la cuenta |
| Anulación o devolución | Traza original y reverso; sin doble reducción |
| Cambio de tarifa | No cambia cargos anteriores |
| Caja y mililitros | Conversiones y decimales correctos, sin stock negativo |
| Mínimo 3 | Con 4 no alerta; con 3 y 2 sí; con 0 indica agotado |
| Vencimiento | Existencia física distinguida de utilizable; no consume lote vencido |
| Merma | Reduce stock, conserva motivo y costo; no crea atención ni descuento |
| Compra y consumo | No duplica egreso ni calcula falsa utilidad |
| Costo desconocido | Indicador incompleto, nunca costo cero inventado |
| Cierre efectivo | Tarjeta y transferencia no incrementan efectivo esperado |
| Archivo inválido | Rechaza contenido no permitido y no deja referencia rota |
| Edición simultánea | Advierte conflicto en lugar de perder cambios |
| Dashboard | Totales coinciden con detalle y no se multiplican por relaciones |
| Sin datos | Estado vacío claro, sin datos ficticios |
| Respaldo restaurado | Recupera base y archivos en entorno separado |
| Móvil y teclado | Formularios legibles, acciones accesibles y sin cortes críticos |

Casos de referencia con importes sintéticos:

- Servicio $25.000, descuento aprobado $10.000, total $15.000. Pago $10.000 efectivo + $5.000 transferencia: saldo $0. Insumos $3.000: aporte antes de otros costos $12.000.
- Servicio $25.000, gratuidad total: cargo final $0, pago $0, insumos $3.000. Gratuidad y consumo visibles; no contabilizar $25.000 como ingreso ni como merma.
- Servicio $25.000, abono $10.000: saldo $15.000, cobro recibido $10.000.

## 19. Instalación y documentación requerida

README debe explicar requisitos PHP/extensiones (PDO MySQL, fileinfo y zip para DOCX), creación de base, configuración del .env, SQL inicial/migraciones, creación segura de usuario y virtual host de Laragon con raíz public.

Incluir configuración Apache y alternativa para servidor PHP local, explicando sus límites. No asumir que abrir la raíz del repositorio es un despliegue seguro.

Variables mínimas: APP_ENV, APP_URL, APP_TIMEZONE, DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS, STORAGE_PATH, UPLOAD_MAX_MB y SESSION_TIMEOUT_MINUTES. Secretos solo por entorno; no volcarlos en logs.

Datos iniciales: roles, medios de pago y catálogos mínimos. No incluir pacientes, cobros ni contraseñas de prueba en producción.

Las migraciones deben versionarse; estructura.sql sirve para instalación nueva. No reimportar el esquema completo sobre una base con datos. No ejecutar DROP TABLE en una actualización.

Backup script: destino configurable fuera de public, credenciales no visibles en línea de comandos, manejo de fallos y documentación de copia externa. No afirmar que los respaldos son automáticos hasta configurar el programador y verificar restauración con permiso.

## 20. Definición de terminado

El MVP está terminado cuando una atención normal, con descuento, gratuita y con deuda puede recorrer todo el flujo sin perder ficha, evidencias, stock ni coherencia de cobro; los dos perfiles respetan permisos; las cifras se pueden explicar con registros; existe instalación reproducible y están informados los resultados reales de las validaciones autorizadas.

Prioridad: claridad de uso, integridad de datos y facilidad de mantenimiento. Agregar archivos solo cuando tengan una responsabilidad concreta. No extender el alcance con módulos avanzados antes de completar este flujo.
