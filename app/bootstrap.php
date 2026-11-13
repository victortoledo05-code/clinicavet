<?php
declare(strict_types=1);
$GLOBALS['config']=require __DIR__.'/config.php';date_default_timezone_set($GLOBALS['config']['timezone']);ini_set('session.use_strict_mode','1');session_name('clinicavet_session');session_set_cookie_params(['httponly'=>true,'secure'=>(!empty($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!=='off'),'samesite'=>'Lax','path'=>'/']);session_start();require __DIR__.'/conexion.php';require __DIR__.'/seguridad.php';
require __DIR__.'/archivos.php';
if(isset($_SESSION['ultimo_movimiento'])&&time()-(int)$_SESSION['ultimo_movimiento']>$GLOBALS['config']['session_timeout']){$_SESSION=[];session_regenerate_id(true);$_SESSION['flash'][]=['tipo'=>'aviso','mensaje'=>'Tu sesión terminó por inactividad.'];}$_SESSION['ultimo_movimiento']=time();
