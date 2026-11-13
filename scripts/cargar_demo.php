<?php
declare(strict_types=1);
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
require dirname(__DIR__).'/app/bootstrap.php';

$usuario=fila('SELECT id FROM usuarios WHERE rol="superadministrador" AND activo=1 ORDER BY id LIMIT 1');
if(!$usuario){fwrite(STDERR,"Primero crea un superadministrador.\n");exit(1);}
$uid=(int)$usuario['id'];
$demo=[
 ['Bruno','Canino','Golden Retriever','macho','Carlos Muñoz','912345601','Control general y vacunas al día.'],
 ['Luna','Felino','Europeo corto','hembra','Daniela Soto','912345602','Paciente nerviosa durante la manipulación.'],
 ['Milo','Canino','Jack Russell Terrier','macho','Francisca Rojas','912345603','Alergia conocida a picaduras de insectos.'],
 ['Nube','Felino','Mestizo','hembra','Jorge Silva','912345604','Dieta renal indicada.'],
 ['Mora','Canino','Labrador Retriever','hembra','Camila Pérez','912345605','Controlar peso en cada visita.'],
 ['Toby','Canino','Beagle','macho','Andrea Morales','912345606','Sin alertas clínicas vigentes.'],
 ['Simón','Felino','Mestizo pelo largo','macho','Rodrigo Díaz','912345607','No administrar medicamentos sin revisar función hepática.'],
 ['Kira','Canino','Mestizo','hembra','Paula Contreras','912345608','Rescatada; fecha de nacimiento estimada.'],
 ['Rex','Canino','Pastor Alemán','macho','Martín Fuentes','912345609','Antecedente de displasia de cadera.'],
 ['Olivia','Felino','Europeo bicolor','hembra','Valentina Reyes','912345610','Paciente indoor, vacunas vigentes.'],
];
$pdo=db();$creados=0;
try{
 $pdo->beginTransaction();
 $servicio=fila('SELECT id FROM servicios WHERE nombre="Consulta general DEMO" LIMIT 1');
 if(!$servicio){ejecutar('INSERT INTO servicios(nombre,categoria,tarifa) VALUES ("Consulta general DEMO","Consulta",25000)');$servicio=['id'=>(int)$pdo->lastInsertId()];}
 foreach($demo as $i=>$d){
  $codigo='DEMO-'.str_pad((string)($i+1),3,'0',STR_PAD_LEFT);
  if(fila('SELECT id FROM pacientes WHERE codigo=?',[$codigo]))continue;
  ejecutar('INSERT INTO tutores(nombre,email,telefono,observaciones) VALUES (?,?,?,"Registro sintético para demostración")',[$d[4],"demo".($i+1).'@clinicavet.local',$d[5]]);$tid=(int)$pdo->lastInsertId();
  ejecutar('INSERT INTO pacientes(codigo,tutor_id,nombre,especie,raza,sexo,edad_estimada_meses,antecedentes,alertas_clinicas) VALUES (?,?,?,?,?,?,?,"Ficha sintética de demostración",?)',[$codigo,$tid,$d[0],$d[1],$d[2],$d[3],24+$i*5,$d[6]]);$pid=(int)$pdo->lastInsertId();
  $archivo=sprintf('demo-paciente-%02d.png',$i+1);$ruta=dirname(__DIR__).'/storage/pacientes/'.$archivo;
  ejecutar('INSERT INTO archivos(paciente_id,tipo,ruta_privada,nombre_original,mime,tamano,autor_id) VALUES (?,"perfil",?,?,"image/png",?,?)',[$pid,$archivo,$d[0].'-demo.png',filesize($ruta),$uid]);
  $fecha=gmdate('Y-m-d H:i:s',strtotime('-'.(9-$i).' days')+36000);
  ejecutar('INSERT INTO atenciones(paciente_id,autor_id,responsable_id,fecha_clinica,motivo,historia,examen,peso,diagnostico,tratamiento,indicaciones,proximo_control,estado) VALUES (?,?,?,?,"Consulta general DEMO","Paciente ingresa para control preventivo.","Examen físico general sin hallazgos urgentes.",?,"Control preventivo","Seguimiento según evolución","Mantener cuidados habituales",DATE_ADD(CURRENT_DATE,INTERVAL ? DAY),"finalizada")',[$pid,$uid,$uid,$fecha,4.5+$i*2.3,7+$i]);$aid=(int)$pdo->lastInsertId();
  ejecutar('INSERT INTO cuentas(atencion_id,estado) VALUES (?,?)',[$aid,$i<7?'pagada':'abierta']);$cid=(int)$pdo->lastInsertId();
  ejecutar('INSERT INTO cargos(cuenta_id,servicio_id,descripcion,cantidad,precio_unitario) VALUES (?,? ,"Consulta general DEMO",1,25000)',[$cid,$servicio['id']]);
  if($i<7)ejecutar('INSERT INTO pagos(cuenta_id,importe,medio,fecha,referencia,autor_id,operacion_id) VALUES (?,25000,?, ?,NULL,?,?)',[$cid,$i%2?'tarjeta':'efectivo',$fecha,$uid,bin2hex(random_bytes(16))]);
  $creados++;
 }
 $pdo->commit();
}catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();fwrite(STDERR,"Error: {$e->getMessage()}\n");exit(1);}
fwrite(STDOUT,"Demo lista: {$creados} pacientes creados; los existentes fueron conservados.\n");
