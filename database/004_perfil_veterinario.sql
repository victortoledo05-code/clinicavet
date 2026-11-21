-- Aplicar manualmente tras respaldar la base de datos.
-- Nuevo perfil clínico y permiso individual de edición de borradores ajenos.
ALTER TABLE usuarios
  MODIFY COLUMN rol ENUM('superadministrador','administrador','veterinario') NOT NULL DEFAULT 'administrador',
  ADD COLUMN editar_atenciones_ajenas TINYINT(1) NOT NULL DEFAULT 0;
-- Los usuarios existentes mantienen su rol. El permiso inicia desactivado.
