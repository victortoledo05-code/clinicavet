-- Migración manual para registros DEMO existentes. Ejecutar con respaldo previo.
-- Los identificadores internos (id) y las relaciones históricas no cambian.
START TRANSACTION;
UPDATE pacientes SET codigo=CONCAT('MIG-PAC-',id);
UPDATE productos SET codigo=CONCAT('MIG-PRO-',id);
UPDATE pacientes SET codigo=CONCAT('PAC-',LPAD(id,6,'0'));
UPDATE productos SET codigo=CONCAT('PRO-',LPAD(id,6,'0'));
COMMIT;
-- Tutores y servicios ya usan id AUTO_INCREMENT. Sus códigos visibles se derivan de id.
