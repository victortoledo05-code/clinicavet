<?php $titulo='Error';require __DIR__.'/encabezado.php';?>
<section class="tarjeta estado-vacio"><h1><?=http_response_code()?></h1><p><?=e($GLOBALS['error_http']??'Ocurrió un error.')?></p><a class="boton" href="index.php">Volver al inicio</a></section>
<?php require __DIR__.'/pie.php';?>
