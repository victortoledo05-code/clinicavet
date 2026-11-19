-- Ejecutar manualmente con respaldo previo. No modifica saldos ni movimientos existentes.
ALTER TABLE lotes ADD COLUMN identificador_frasco VARCHAR(80) NULL AFTER codigo_lote, ADD COLUMN abierto_en DATE NULL AFTER identificador_frasco, ADD COLUMN vence_abierto DATE NULL AFTER abierto_en;
-- Los lotes existentes permanecen como registros agregados; no se separan automáticamente en frascos.
