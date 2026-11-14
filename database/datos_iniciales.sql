INSERT INTO catalogos(tipo,codigo,etiqueta) VALUES
('medio_pago','efectivo','Efectivo'),('medio_pago','transferencia','Transferencia'),('medio_pago','tarjeta','Tarjeta'),
('unidad','unidad','Unidad'),('unidad','ml','Mililitro'),('unidad','frasco','Frasco'),('unidad','caja','Caja'),
('merma','vencido','Vencido'),('merma','danado','Dañado'),('merma','perdido','Perdido'),('merma','otro','Otro');
INSERT INTO catalogos(tipo,codigo,etiqueta) VALUES ('categoria_servicio','consulta','Consulta'),('categoria_servicio','preventivo','Preventivo'),('categoria_servicio','especialidad','Especialidad'),('categoria_servicio','procedimiento','Procedimiento'),('categoria_servicio','diagnostico','Diagnóstico'),('categoria_servicio','laboratorio','Laboratorio'),('categoria_servicio','hospitalizacion','Hospitalización');
INSERT INTO configuracion(clave,valor) VALUES ('clinica_nombre','ClinicVet'),('alerta_vencimiento_dias','30'),('moneda','CLP');
