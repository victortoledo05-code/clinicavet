<?php
declare(strict_types=1);
require dirname(__DIR__).'/app/bootstrap.php';
require dirname(__DIR__).'/app/modulos.php';
require dirname(__DIR__).'/app/insumos_atencion.php';
$ruta=(string)($_GET['r']??'inicio');
$rutas=[
 'login'=>['pagina_login',false], 'login.enviar'=>['accion_login',false], 'logout'=>['accion_logout',true],
 'inicio'=>['pagina_inicio',true], 'buscar'=>['pagina_buscar',true],
 'recepcion'=>['pagina_recepcion',true], 'recepcion.guardar'=>['guardar_recepcion',true],
 'recepcion.guardar_foto'=>['guardar_recepcion_con_foto',true],
 'tutores'=>['pagina_tutores',true], 'tutor.form'=>['form_tutor',true], 'tutor.guardar'=>['guardar_tutor',true],
 'pacientes'=>['pagina_pacientes',true], 'paciente.form'=>['form_paciente',true], 'paciente.guardar'=>['guardar_paciente',true], 'paciente.ficha'=>['ficha_paciente',true],
 'pacientes.resumen'=>['resumen_atenciones_pacientes',true],
 'paciente.guardar_foto'=>['guardar_paciente_con_foto',true],
 'paciente.foto'=>['servir_foto_paciente',true],
 'atenciones'=>['pagina_atenciones_filtrada',true], 'atencion.form'=>['form_atencion',true], 'atencion.guardar'=>['guardar_atencion',true], 'atencion.detalle'=>['detalle_atencion',true], 'atencion.archivos.guardar'=>['guardar_evidencias_atencion',true], 'atencion.archivo'=>['servir_evidencia_atencion',true], 'atencion.aclarar'=>['aclarar_atencion',true], 'atencion.insumo.guardar'=>['guardar_insumo_atencion',true], 'atencion.insumo.revertir'=>['revertir_insumo_atencion',true],
 'seguimientos'=>['pagina_seguimientos',true], 'seguimiento.form'=>['form_seguimiento',true], 'seguimiento.guardar'=>['guardar_seguimiento',true], 'seguimiento.estado'=>['cambiar_estado_seguimiento',true], 'seguimiento.contacto'=>['registrar_contacto_seguimiento',true], 'seguimientos.contador'=>['contador_seguimientos_json',true],
 'servicios'=>['pagina_servicios_gestion','super'], 'servicio.guardar'=>['guardar_servicio_gestion','super'], 'servicio.estado'=>['cambiar_estado_servicio','super'], 'servicio.eliminar'=>['eliminar_servicio','super'],
 'servicio.categorias'=>['pagina_categorias_servicio','super'], 'servicio.categorias.json'=>['categorias_servicio_json','super'], 'servicio.categoria.guardar'=>['guardar_categoria_servicio','super'], 'servicio.categoria.estado'=>['cambiar_estado_categoria_servicio','super'], 'servicio.categoria.eliminar'=>['eliminar_categoria_servicio','super'],
 'cobros'=>['pagina_cobros',true], 'cobro.detalle'=>['detalle_cobro',true], 'cargo.guardar'=>['guardar_cargo',true], 'pago.guardar'=>['guardar_pago',true], 'descuento.guardar'=>['guardar_descuento',true],
 'stock'=>['pagina_stock_mejorada',true], 'producto.form'=>['form_producto_operativo','super'], 'producto.guardar'=>['guardar_producto_mejorado','super'], 'stock.entrada'=>['guardar_entrada','super'], 'stock.consumo'=>['guardar_consumo',true],
 'finanzas'=>['pagina_finanzas_mejorada','super'], 'finanza.guardar'=>['guardar_finanza','super'],
 'usuarios'=>['pagina_usuarios','super'], 'usuario.form'=>['form_usuario','super'], 'usuario.guardar'=>['guardar_usuario','super'],
 'configuracion'=>['pagina_configuracion_marca','super'], 'configuracion.guardar'=>['guardar_configuracion_marca','super'], 'configuracion.logo'=>['servir_logo_clinica',true], 'auditoria'=>['pagina_auditoria','super'],
];
if(!isset($rutas[$ruta]))abortar(404,'La página solicitada no existe.');
[$fn,$permiso]=$rutas[$ruta];if($permiso===true)exigir_acceso();elseif($permiso==='super')exigir_superadmin();$fn();
