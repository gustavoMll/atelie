<?php

Utils::ajaxHeader();

$module = $request->get('class');
$id = $request->getInt('id');
if (!file_exists(__DIR__. "/../config.{$module}.php")) {
    Utils::jsonResponse('M&oacute;dulo n&atilde;o instalado. Contate o suporte t&eacute;cnico.');
}

if(!$objSession->hasPermition($module,'ins') && $objSession->hasPermition($module,'upd')){
    Utils::jsonResponse("Desculpe, voc&ecirc; n&atilde;o possui permiss&atilde;o para realizar esta opera&ccedil;&atilde;o neste m&oacute;dulo.");
}

include(__DIR__. "/../config.{$module}.php");
$objClass = new $Modules['class']();

$ret = $Modules['class']::duplicate($id, $objClass->getPk()[0]);

Utils::jsonResponse("Duplicado com sucesso. ID gerado: ".$ret, true, ['id' => $ret]);