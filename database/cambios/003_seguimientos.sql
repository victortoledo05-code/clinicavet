CREATE TABLE seguimientos (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 paciente_id BIGINT UNSIGNED NOT NULL,
 atencion_id BIGINT UNSIGNED NULL,
 tipo ENUM('vacuna','desparasitacion','control','examen','medicamento','procedimiento','otro') NOT NULL,
 titulo VARCHAR(160) NOT NULL,
 fecha_programada DATE NOT NULL,
 prioridad ENUM('normal','alta') NOT NULL DEFAULT 'normal',
 estado ENUM('pendiente','completado','cancelado') NOT NULL DEFAULT 'pendiente',
 observaciones VARCHAR(1000) NULL,
 contacto_resultado VARCHAR(255) NULL,
 contactado_en DATETIME NULL,
 creado_por BIGINT UNSIGNED NOT NULL,
 completado_por BIGINT UNSIGNED NULL,
 completado_en DATETIME NULL,
 creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 actualizado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 FOREIGN KEY(paciente_id) REFERENCES pacientes(id),
 FOREIGN KEY(atencion_id) REFERENCES atenciones(id),
 FOREIGN KEY(creado_por) REFERENCES usuarios(id),
 FOREIGN KEY(completado_por) REFERENCES usuarios(id),
 INDEX(estado,fecha_programada),INDEX(paciente_id,fecha_programada)
) ENGINE=InnoDB;
INSERT INTO schema_migrations(version) VALUES ('003_seguimientos');
