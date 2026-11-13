<?php
declare(strict_types=1);
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
require dirname(__DIR__).'/app/bootstrap.php';
$email=$argv[1]??'';$nombre=$argv[2]??'';
if(!filter_var($email,FILTER_VALIDATE_EMAIL)||trim($nombre)===''){fwrite(STDERR,"Uso: php scripts/crear_superadmin.php correo nombre\n");exit(1);}
fwrite(STDOUT,"Contraseña (mínimo 12 caracteres): ");
if(PHP_OS_FAMILY!=='Windows')shell_exec('stty -echo');$password=trim((string)fgets(STDIN));if(PHP_OS_FAMILY!=='Windows')shell_exec('stty echo');fwrite(STDOUT,"\n");
if(mb_strlen($password)<12){fwrite(STDERR,"Contraseña demasiado corta.\n");exit(1);}
ejecutar('INSERT INTO usuarios(nombre,email,password_hash,rol) VALUES (?,?,?,"superadministrador")',[trim($nombre),mb_strtolower($email),password_hash($password,PASSWORD_DEFAULT)]);
fwrite(STDOUT,"Superadministrador creado.\n");
