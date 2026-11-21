<?php
declare(strict_types=1);
function e(mixed $v):string{return htmlspecialchars((string)$v,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');}
function usuario():?array{if(empty($_SESSION['usuario']['id']))return null;static $actual=null;if($actual!==null)return $actual;try{$u=fila('SELECT id,nombre,rol,activo,editar_atenciones_ajenas FROM usuarios WHERE id=?',[(int)$_SESSION['usuario']['id']]);}catch(PDOException $e){if($e->getCode()!=='42S22')throw $e;$u=fila('SELECT id,nombre,rol,activo FROM usuarios WHERE id=?',[(int)$_SESSION['usuario']['id']]);}if(!$u||!(int)$u['activo']){$_SESSION['usuario']=null;return null;}return $actual=['id'=>(int)$u['id'],'nombre'=>$u['nombre'],'rol'=>$u['rol'],'editar_atenciones_ajenas'=>(int)($u['editar_atenciones_ajenas']??0)];}
function es_superadmin():bool{return (usuario()['rol']??'')==='superadministrador';}
function es_veterinario():bool{return (usuario()['rol']??'')==='veterinario';}
function puede_editar_atencion(array $atencion):bool{if(($atencion['estado']??'')!=='borrador')return false;if(!es_veterinario())return true;if((int)($atencion['responsable_id']??0)===(int)(usuario()['id']??0))return true;return (int)(usuario()['editar_atenciones_ajenas']??0)===1;}
function exigir_edicion_atencion(int $id):array{$a=fila('SELECT id,responsable_id,estado FROM atenciones WHERE id=?',[$id]);if(!$a)abortar(404,'Atención no encontrada.');if(!puede_editar_atencion($a))abortar(403,'No tienes permiso para modificar esta atención.');return $a;}
function exigir_no_veterinario():void{exigir_acceso();if(es_veterinario())abortar(403,'Esta sección no está disponible para el perfil clínico.');}
function exigir_acceso():void{if(!usuario())redirigir('login');}
function exigir_superadmin():void{exigir_acceso();if(!es_superadmin())abortar(403,'No tienes permiso para acceder a esta sección.');}
function csrf():string{if(empty($_SESSION['csrf']))$_SESSION['csrf']=bin2hex(random_bytes(32));return $_SESSION['csrf'];}
function campo_csrf():string{return '<input type="hidden" name="csrf" value="'.e(csrf()).'">';}
function validar_csrf():void{if(!hash_equals($_SESSION['csrf']??'',(string)($_POST['csrf']??'')))abortar(419,'La sesión del formulario venció. Recarga e inténtalo nuevamente.');}
function redirigir(string $r,array $q=[]):never{$u='index.php?r='.rawurlencode($r);if($q)$u.='&'.http_build_query($q);header('Location: '.$u);exit;}
function flash(string $t,string $m):void{$_SESSION['flash'][]=['tipo'=>$t,'mensaje'=>$m];}
function abortar(int $c,string $m):never{http_response_code($c);$GLOBALS['error_http']=$m;require __DIR__.'/comunes/error.php';exit;}
function post():void{if($_SERVER['REQUEST_METHOD']!=='POST')abortar(405,'Método no permitido.');validar_csrf();}
function operacion_id():string{return bin2hex(random_bytes(16));}
function clp(int|float|string $m):string{return '$'.number_format((int)$m,0,',','.');}
function configuracion_valor(string $clave,string $defecto=''):string{static $cache=[];if(array_key_exists($clave,$cache))return $cache[$clave];try{$r=fila('SELECT valor FROM configuracion WHERE clave=?',[$clave]);return $cache[$clave]=(string)($r['valor']??$defecto);}catch(Throwable){return $defecto;}}
function utc_a_local(?string $f,string $fmt='d-m-Y H:i'):string{if(!$f)return '—';return(new DateTimeImmutable($f,new DateTimeZone('UTC')))->setTimezone(new DateTimeZone($GLOBALS['config']['timezone']))->format($fmt);}
function auditar(string $a,string $e,int|string|null $id=null,?string $motivo=null,?array $c=null):void{ejecutar('INSERT INTO auditoria (usuario_id,accion,entidad,entidad_id,motivo,cambios,ip,creado_en) VALUES (?,?,?,?,?,?,?,UTC_TIMESTAMP())',[usuario()['id']??null,$a,$e,$id,$motivo,$c?json_encode($c,JSON_UNESCAPED_UNICODE):null,$_SERVER['REMOTE_ADDR']??null]);}
