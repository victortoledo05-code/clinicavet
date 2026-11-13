<?php
declare(strict_types=1);
function cargar_entorno(string $archivo): void {
    if (!is_file($archivo)) return;
    foreach (file($archivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $linea) {
        $linea=trim($linea); if ($linea==='' || str_starts_with($linea,'#') || !str_contains($linea,'=')) continue;
        [$clave,$valor]=explode('=',$linea,2); $clave=trim($clave); $valor=trim($valor," \t\n\r\0\x0B\"'");
        if ($clave!=='' && getenv($clave)===false) putenv("{$clave}={$valor}");
    }
}
cargar_entorno(dirname(__DIR__).'/.env');
function env(string $clave, mixed $defecto=null): mixed { $v=getenv($clave); return $v===false?$defecto:$v; }
$storage=(string)env('STORAGE_PATH','storage');
return [
 'env'=>env('APP_ENV','production'),'url'=>rtrim((string)env('APP_URL','http://clinicavet.test'),'/'),'timezone'=>env('APP_TIMEZONE','America/Santiago'),
 'db'=>['host'=>env('DB_HOST','127.0.0.1'),'port'=>env('DB_PORT','3306'),'name'=>env('DB_NAME','clinicavet'),'user'=>env('DB_USER','root'),'pass'=>env('DB_PASS','')],
 'storage'=>preg_match('~^[A-Za-z]:[\\\\/]|^/~',$storage)?$storage:dirname(__DIR__).'/'.$storage,
 'upload_max_mb'=>max(1,(int)env('UPLOAD_MAX_MB',10)),'session_timeout'=>max(5,(int)env('SESSION_TIMEOUT_MINUTES',30))*60,
];
