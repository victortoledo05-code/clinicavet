INSERT INTO catalogos(tipo,codigo,etiqueta) VALUES
('medio_pago','efectivo','Efectivo'),('medio_pago','transferencia','Transferencia'),('medio_pago','tarjeta','Tarjeta'),
('unidad','unidad','Unidad'),('unidad','ml','Mililitro'),('unidad','frasco','Frasco'),('unidad','caja','Caja'),
('merma','vencido','Vencido'),('merma','danado','Dañado'),('merma','perdido','Perdido'),('merma','otro','Otro');
INSERT INTO configuracion(clave,valor) VALUES ('clinica_nombre','ClinicVet'),('alerta_vencimiento_dias','30'),('moneda','CLP');
