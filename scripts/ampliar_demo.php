<?php
declare(strict_types=1);
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
require dirname(__DIR__).'/app/bootstrap.php';
$u=fila('SELECT id FROM usuarios WHERE rol="superadministrador" AND activo=1 ORDER BY id LIMIT 1');if(!$u){fwrite(STDERR,"Falta superadministrador.\n");exit(1);}$uid=(int)$u['id'];
$servicios=[
 ['Vacunación séxtuple','Preventivo',18000],['Vacunación antirrábica','Preventivo',15000],['Desparasitación','Preventivo',12000],
 ['Consulta dermatológica','Especialidad',32000],['Limpieza de heridas','Procedimiento',22000],['Corte de uñas','Procedimiento',9000],
 ['Ecografía abdominal','Diagnóstico',48000],['Perfil bioquímico','Laboratorio',42000],['Hospitalización por día','Hospitalización',65000],
];
$productos=[
 ['MED-001','Amoxicilina 500 mg','Medicamento','comprimido','Caja 20 comprimidos',20,12,1,8,1200],
 ['MED-002','Meloxicam veterinario','Medicamento','ml','Frasco 100 ml',100,15,1,12,850],
 ['MED-003','Prednisona 20 mg','Medicamento','comprimido','Caja 30 comprimidos',30,10,1,25,500],
 ['MED-004','Antiparasitario oral','Medicamento','dosis','Caja 12 dosis',12,4,1,3,2800],
 ['INS-001','Jeringa 5 ml','Insumo','unidad','Caja 100 unidades',100,20,1,18,180],
 ['INS-002','Guantes de examen','Insumo','unidad','Caja 100 unidades',100,25,1,80,90],
 ['INS-003','Suero fisiológico','Insumo','ml','Bolsa 500 ml',500,500,1,1200,18],
 ['INS-004','Gasa estéril','Insumo','unidad','Paquete 100 unidades',100,15,1,9,110],
 ['ALM-001','Alimento recuperación','Alimento','gramo','Lata 400 g',400,300,0,800,12],
];
$pdo=db();try{$pdo->beginTransaction();
 foreach($servicios as $s){if(!fila('SELECT id FROM servicios WHERE nombre=?',[$s[0]]))ejecutar('INSERT INTO servicios(nombre,categoria,tarifa) VALUES (?,?,?)',$s);}
 foreach($productos as $i=>$p){$prod=fila('SELECT id FROM productos WHERE codigo=?',[$p[0]]);if(!$prod){ejecutar('INSERT INTO productos(codigo,nombre,categoria,unidad_base,presentacion,factor_conversion,stock_minimo,alerta_activa) VALUES (?,?,?,?,?,?,?,?)',array_slice($p,0,8));$prod=['id'=>(int)$pdo->lastInsertId()];$vence=date('Y-m-d',strtotime('+'.(6+$i).' months'));ejecutar('INSERT INTO lotes(producto_id,codigo_lote,vencimiento,saldo_base,costo_unitario_base) VALUES (?,?,?,?,?)',[$prod['id'],'DEMO-L'.str_pad((string)($i+1),2,'0',STR_PAD_LEFT),$vence,$p[8],$p[9]]);$lid=(int)$pdo->lastInsertId();ejecutar('INSERT INTO movimientos_stock(lote_id,tipo,cantidad,costo_historico,motivo,autor_id,operacion_id) VALUES (?,"apertura",?,?,"Carga inicial DEMO",?,?)',[$lid,$p[8],$p[9],$uid,bin2hex(random_bytes(16))]);}}
 $pacientes=filas('SELECT id,codigo FROM pacientes WHERE codigo LIKE "DEMO-%" ORDER BY codigo LIMIT 6');
 foreach($pacientes as $i=>$pa){for($n=2;$n<=($i<3?4:3);$n++){$motivo="Seguimiento DEMO {$n}";if(fila('SELECT id FROM atenciones WHERE paciente_id=? AND motivo=?',[$pa['id'],$motivo]))continue;$fecha=gmdate('Y-m-d H:i:s',strtotime('-'.(35-$i*3-$n*4).' days'));ejecutar('INSERT INTO atenciones(paciente_id,autor_id,responsable_id,fecha_clinica,motivo,historia,examen,peso,diagnostico,tratamiento,indicaciones,estado) VALUES (?,?,?,?,?,"Control de evolución clínica DEMO.","Paciente estable al examen.",?,"Evolución favorable","Continuar manejo indicado","Control según necesidad","finalizada")',[$pa['id'],$uid,$uid,$fecha,$motivo,5+$i*3+$n/10]);$aid=(int)$pdo->lastInsertId();ejecutar('INSERT INTO cuentas(atencion_id,estado) VALUES (?,"pagada")',[$aid]);$cid=(int)$pdo->lastInsertId();$sv=fila('SELECT id,nombre,tarifa FROM servicios WHERE activo=1 ORDER BY id LIMIT 1 OFFSET '.(($i+$n)%5));ejecutar('INSERT INTO cargos(cuenta_id,servicio_id,descripcion,cantidad,precio_unitario) VALUES (?,?,?,?,?)',[$cid,$sv['id'],$sv['nombre'],1,$sv['tarifa']]);ejecutar('INSERT INTO pagos(cuenta_id,importe,medio,fecha,referencia,autor_id,operacion_id) VALUES (?,?,?,?,?,?,?)',[$cid,$sv['tarifa'],$n%2?'efectivo':'transferencia',$fecha,$n%2?null:'DEMO-TRX-'.$aid,$uid,bin2hex(random_bytes(16))]);}}
 foreach([['gasto','Insumos','Compra de material clínico DEMO',78000,'transferencia','-12 days'],['gasto','Servicios básicos','Electricidad clínica DEMO',95000,'transferencia','-8 days'],['gasto','Mantención','Mantención equipo DEMO',45000,'efectivo','-3 days'],['ingreso','Otros','Certificado sanitario DEMO',18000,'efectivo','-2 days']] as $m){if(!fila('SELECT id FROM movimientos_financieros WHERE descripcion=?',[$m[2]]))ejecutar('INSERT INTO movimientos_financieros(tipo,categoria,descripcion,importe,medio,fecha,responsable_id,operacion_id) VALUES (?,?,?,?,?,?,?,?)',[$m[0],$m[1],$m[2],$m[3],$m[4],gmdate('Y-m-d H:i:s',strtotime($m[5])),$uid,bin2hex(random_bytes(16))]);}
 $pdo->commit();}catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();fwrite(STDERR,"Error: {$e->getMessage()}\n");exit(1);}fwrite(STDOUT,"Demo ampliada correctamente.\n");
