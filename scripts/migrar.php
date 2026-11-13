<?php
declare(strict_types=1);
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
fwrite(STDERR,"Este MVP usa database/estructura.sql para instalación nueva. No se ejecutó ningún cambio automáticamente.\n");
exit(2);
