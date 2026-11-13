ALTER TABLE productos ADD COLUMN alerta_activa TINYINT(1) NOT NULL DEFAULT 1 AFTER stock_minimo;
INSERT INTO schema_migrations(version) VALUES ('002_alertas_stock');
