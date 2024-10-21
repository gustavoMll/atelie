<?php
Utils::ajaxHeader();

$module = $request->get('class');
$position = $request->getInt('position');
$order = $request->get('order');
$field = $request->get('field', 'ordem');

if (!file_exists(__DIR__. "/../config.{$module}.php")) {
    Utils::jsonResponse('M&oacute;dulo n&atilde;o instalado. Contate o suporte t&eacute;cnico.');
}

if(!$objSession->hasPermition($module, 'upd')){
    Utils::jsonResponse("Desculpe, voc&ecirc; n&atilde;o possui permiss&atilde;o para realizar esta opera&ccedil;&atilde;o neste m&oacute;dulo.");
}

include(__DIR__. "/../config.{$module}.php");
$objClass = new $Modules['class']();

$arr = explode(",",$order);
$conn = new Connection();
$sql = "UPDATE {$objClass->getTableName()} SET {$field} = NULL WHERE id IN($order)"; 
$conn->prepareStatement($sql)->executeQuery();
$i = 1;
foreach($arr as $id){
    $sql = "UPDATE {$objClass->getTableName()} SET {$field} = ".$i++." WHERE id = $id ";
    $conn->prepareStatement($sql)->executeQuery();
}

Utils::jsonResponse('Ordem alterada com sucesso em '.date("d/m/Y H:i:s"), true);
