<?php
declare(strict_types=1);

function guardar_foto_paciente(int $pacienteId, string $campo='foto'): void
{
    $archivo=$_FILES[$campo]??null;
    if(!$archivo || (int)$archivo['error']===UPLOAD_ERR_NO_FILE) return;
    if((int)$archivo['error']!==UPLOAD_ERR_OK) abortar(422,'No fue posible recibir la fotografía. Inténtalo nuevamente.');
    $max=$GLOBALS['config']['upload_max_mb']*1024*1024;
    if((int)$archivo['size']<1 || (int)$archivo['size']>$max) abortar(422,'La fotografía supera el límite permitido.');
    $finfo=new finfo(FILEINFO_MIME_TYPE);$mime=$finfo->file($archivo['tmp_name']);
    $permitidos=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
    if(!isset($permitidos[$mime]) || @getimagesize($archivo['tmp_name'])===false) abortar(422,'La fotografía debe ser JPEG, PNG o WebP válida.');
    $dir=rtrim($GLOBALS['config']['storage'],'/\\').DIRECTORY_SEPARATOR.'pacientes';
    if(!is_dir($dir) && !mkdir($dir,0770,true) && !is_dir($dir)) abortar(500,'No fue posible preparar el almacenamiento privado.');
    $nombre=bin2hex(random_bytes(20)).'.'.$permitidos[$mime];$destino=$dir.DIRECTORY_SEPARATOR.$nombre;
    if(!move_uploaded_file($archivo['tmp_name'],$destino)) abortar(500,'No fue posible guardar la fotografía.');
    try{
        ejecutar('UPDATE archivos SET retirado_en=UTC_TIMESTAMP() WHERE paciente_id=? AND tipo="perfil" AND retirado_en IS NULL',[$pacienteId]);
        ejecutar('INSERT INTO archivos(paciente_id,tipo,ruta_privada,nombre_original,mime,tamano,autor_id) VALUES (?,"perfil",?,?,?,?,?)',[$pacienteId,$nombre,mb_substr(basename((string)$archivo['name']),0,255),$mime,(int)$archivo['size'],usuario()['id']]);
    }catch(Throwable $e){@unlink($destino);throw $e;}
    auditar('foto_perfil','pacientes',$pacienteId);
}

function servir_foto_paciente(): never
{
    $id=id_get();$a=fila('SELECT * FROM archivos WHERE paciente_id=? AND tipo="perfil" AND retirado_en IS NULL ORDER BY id DESC LIMIT 1',[$id]);
    if(!$a) abortar(404,'Fotografía no encontrada.');
    $base=realpath(rtrim($GLOBALS['config']['storage'],'/\\').DIRECTORY_SEPARATOR.'pacientes');
    $ruta=$base?realpath($base.DIRECTORY_SEPARATOR.$a['ruta_privada']):false;
    if(!$base || !$ruta || !str_starts_with($ruta,$base.DIRECTORY_SEPARATOR) || !is_file($ruta)) abortar(404,'Fotografía no disponible.');
    header('Content-Type: '.$a['mime']);header('Content-Length: '.filesize($ruta));header('X-Content-Type-Options: nosniff');header('Cache-Control: private, max-age=300');readfile($ruta);exit;
}

function guardar_logo_clinica(string $campo='logo'): void
{
    $archivo=$_FILES[$campo]??null;if(!$archivo||(int)$archivo['error']===UPLOAD_ERR_NO_FILE)return;
    if((int)$archivo['error']!==UPLOAD_ERR_OK||(int)$archivo['size']<1||(int)$archivo['size']>3*1024*1024)abortar(422,'El logo no pudo recibirse o supera 3 MB.');
    $mime=(new finfo(FILEINFO_MIME_TYPE))->file($archivo['tmp_name']);$permitidos=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
    if(!isset($permitidos[$mime])||@getimagesize($archivo['tmp_name'])===false)abortar(422,'El logo debe ser una imagen JPEG, PNG o WebP válida.');
    $dir=rtrim($GLOBALS['config']['storage'],'/\\').DIRECTORY_SEPARATOR.'configuracion';if(!is_dir($dir)&&!mkdir($dir,0770,true)&&!is_dir($dir))abortar(500,'No fue posible preparar el almacenamiento del logo.');
    $anterior=configuracion_valor('clinica_logo_archivo');$nombre='logo-'.bin2hex(random_bytes(12)).'.'.$permitidos[$mime];
    if(!move_uploaded_file($archivo['tmp_name'],$dir.DIRECTORY_SEPARATOR.$nombre))abortar(500,'No fue posible guardar el logo.');
    ejecutar('INSERT INTO configuracion(clave,valor) VALUES ("clinica_logo_archivo",?) ON DUPLICATE KEY UPDATE valor=VALUES(valor)',[$nombre]);
    ejecutar('INSERT INTO configuracion(clave,valor) VALUES ("clinica_logo_mime",?) ON DUPLICATE KEY UPDATE valor=VALUES(valor)',[$mime]);
    if($anterior&&basename($anterior)===$anterior)is_file($dir.DIRECTORY_SEPARATOR.$anterior)&&@unlink($dir.DIRECTORY_SEPARATOR.$anterior);
    auditar('logo','configuracion');
}
function servir_logo_clinica():never{$nombre=configuracion_valor('clinica_logo_archivo');$mime=configuracion_valor('clinica_logo_mime','image/png');$base=realpath(rtrim($GLOBALS['config']['storage'],'/\\').DIRECTORY_SEPARATOR.'configuracion');$ruta=$base&&$nombre?realpath($base.DIRECTORY_SEPARATOR.basename($nombre)):false;if(!$ruta||!$base||!str_starts_with($ruta,$base.DIRECTORY_SEPARATOR))abortar(404,'Logo no disponible.');header('Content-Type: '.$mime);header('Content-Length: '.filesize($ruta));header('X-Content-Type-Options: nosniff');header('Cache-Control: private, max-age=3600');readfile($ruta);exit;}

function guardar_evidencias_atencion():void{
    post();$id=id_get();$a=fila('SELECT id,paciente_id FROM atenciones WHERE id=?',[$id]);
    if(!$a)abortar(404,'Atención no encontrada.');
    $files=$_FILES['evidencias']??null;
    if(!$files||!is_array($files['name']??null))abortar(422,'Selecciona fotografías o documentos.');
    $permitidos=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','application/pdf'=>'pdf'];
    $max=min((int)($GLOBALS['config']['upload_max_mb']??10),10)*1024*1024;
    $dir=rtrim($GLOBALS['config']['storage'],'/\\').DIRECTORY_SEPARATOR.'atenciones';
    if(!is_dir($dir)&&!mkdir($dir,0770,true)&&!is_dir($dir))abortar(500,'No se pudo preparar el almacenamiento.');
    $guardados=0;
    foreach($files['name'] as $i=>$nombreOriginal){
        $error=(int)$files['error'][$i];if($error===UPLOAD_ERR_NO_FILE)continue;
        if($error!==UPLOAD_ERR_OK)abortar(422,'Error al recibir uno de los archivos.');
        $tmp=$files['tmp_name'][$i];$size=(int)$files['size'][$i];
        if($size<1||$size>$max||!is_uploaded_file($tmp))abortar(422,'Archivo vacío o demasiado grande (máximo 10 MB).');
        $mime=(new finfo(FILEINFO_MIME_TYPE))->file($tmp);
        if(!isset($permitidos[$mime]))abortar(422,'Solo se permiten JPG, PNG, WebP o PDF.');
        if($mime!=='application/pdf'&&@getimagesize($tmp)===false)abortar(422,'Imagen inválida.');
        if($mime==='application/pdf'&&file_get_contents($tmp,false,null,0,5)!=='%PDF-')abortar(422,'PDF inválido.');
        $nombre=bin2hex(random_bytes(20)).'.'.$permitidos[$mime];$destino=$dir.DIRECTORY_SEPARATOR.$nombre;
        if(!move_uploaded_file($tmp,$destino))abortar(500,'No se pudo guardar el archivo.');
        try{ejecutar('INSERT INTO archivos(paciente_id,atencion_id,tipo,ruta_privada,nombre_original,mime,tamano,autor_id) VALUES (?,?,"evidencia",?,?,?, ?,?)',[(int)$a['paciente_id'],$id,$nombre,mb_substr(basename((string)$nombreOriginal),0,255),$mime,$size,usuario()['id']]);}
        catch(Throwable $e){@unlink($destino);throw $e;}
        $guardados++;
    }
    if(!$guardados)abortar(422,'Selecciona al menos un archivo.');
    auditar('adjuntar_evidencia','atenciones',$id);flash('ok',$guardados.' archivo(s) agregado(s) a la atención.');
    redirigir('atencion.detalle',['id'=>$id]);
}
function servir_evidencia_atencion():never{
    $id=id_get();$a=fila('SELECT * FROM archivos WHERE id=? AND tipo="evidencia" AND retirado_en IS NULL',[$id]);
    if(!$a)abortar(404,'Archivo no encontrado.');
    $base=realpath(rtrim($GLOBALS['config']['storage'],'/\\').DIRECTORY_SEPARATOR.'atenciones');
    $ruta=$base?realpath($base.DIRECTORY_SEPARATOR.$a['ruta_privada']):false;
    if(!$base||!$ruta||!str_starts_with($ruta,$base.DIRECTORY_SEPARATOR)||!is_file($ruta))abortar(404,'Archivo no disponible.');
    header('Content-Type: '.$a['mime']);header('Content-Length: '.filesize($ruta));
    header('X-Content-Type-Options: nosniff');header('Content-Disposition: inline; filename="evidencia-'.$id.'.'.($a['mime']==='application/pdf'?'pdf':'jpg').'"');
    header('Cache-Control: private, no-store');readfile($ruta);exit;
}
