<?php
declare(strict_types=1);

function guardar_insumo_atencion():void{
    post();$id=id_get();$lote=(int)($_POST['lote_id']??0);$cantidad=(float)($_POST['cantidad']??0);
    if(!is_finite($cantidad)||$cantidad<=0||$cantidad>99999999)abortar(422,'Cantidad inválida.');
    $pdo=db();
    try{
        $pdo->beginTransaction();
        if(!fila('SELECT id FROM atenciones WHERE id=? AND estado<>"anulada"',[$id]))throw new RuntimeException('Atención no disponible.');
        $l=fila('SELECT l.*,p.activo FROM lotes l JOIN productos p ON p.id=l.producto_id WHERE l.id=? FOR UPDATE',[$lote]);
        if(!$l||!$l['activo']||($l['vencimiento']&&$l['vencimiento']<date('Y-m-d'))||$cantidad>(float)$l['saldo_base'])throw new RuntimeException('Lote vencido o stock insuficiente.');
        ejecutar('UPDATE lotes SET saldo_base=saldo_base-? WHERE id=?',[$cantidad,$lote]);
        ejecutar('INSERT INTO movimientos_stock(lote_id,atencion_id,tipo,cantidad,costo_historico,motivo,autor_id,operacion_id) VALUES (?,?,"consumo",?,?,"Insumo utilizado en atención",?,?)',[$lote,$id,-$cantidad,$l['costo_unitario_base'],usuario()['id'],requerido('operacion_id',32)]);
        $mov=(int)$pdo->lastInsertId();$pdo->commit();
    }catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();abortar(422,$e instanceof RuntimeException?$e->getMessage():'No fue posible registrar el consumo.');}
    auditar('consumo_atencion','movimientos_stock',$mov);flash('ok','Insumo descontado del inventario.');redirigir('atencion.detalle',['id'=>$id]);
}
function revertir_insumo_atencion():void{
    post();$id=id_get();$mov=(int)($_POST['movimiento_id']??0);$pdo=db();
    try{
        $pdo->beginTransaction();
        $m=fila('SELECT * FROM movimientos_stock WHERE id=? AND atencion_id=? AND tipo="consumo" FOR UPDATE',[$mov,$id]);
        if(!$m||fila('SELECT id FROM movimientos_stock WHERE reverso_id=?',[$mov]))throw new RuntimeException('Consumo inexistente o ya revertido.');
        $l=fila('SELECT id FROM lotes WHERE id=? FOR UPDATE',[$m['lote_id']]);
        if(!$l)throw new RuntimeException('Lote no encontrado.');
        $q=abs((float)$m['cantidad']);
        ejecutar('UPDATE lotes SET saldo_base=saldo_base+? WHERE id=?',[$q,$m['lote_id']]);
        ejecutar('INSERT INTO movimientos_stock(lote_id,atencion_id,tipo,cantidad,costo_historico,motivo,reverso_id,autor_id,operacion_id) VALUES (?,?,"reverso",?,?,"Corrección de consumo clínico",?,?,?)',[$m['lote_id'],$id,$q,$m['costo_historico'],$mov,usuario()['id'],operacion_id()]);
        $nuevo=(int)$pdo->lastInsertId();$pdo->commit();
    }catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();abortar(422,$e instanceof RuntimeException?$e->getMessage():'No fue posible revertir el consumo.');}
    auditar('reverso_consumo','movimientos_stock',$nuevo);flash('ok','Consumo revertido y stock restituido.');redirigir('atencion.detalle',['id'=>$id]);
}
