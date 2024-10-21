<?php
Utils::ajaxHeader();

$module = $request->get('class');
$id = $request->getInt('id');
$to = $request->getInt('to');
if (!file_exists(__DIR__. "/../config.{$module}.php")) {
    Utils::jsonResponse('M&oacute;dulo n&atilde;o instalado. Contate o suporte t&eacute;cnico.');
}

if(!$objSession->hasPermition($module, 'upd')){
    Utils::jsonResponse("Desculpe, voc&ecirc; n&atilde;o possui permiss&atilde;o para realizar esta opera&ccedil;&atilde;o neste m&oacute;dulo.");
}

include(__DIR__. "/../config.{$module}.php");
$objClass = new $Modules['class']();
if (!$Modules['class']::exists("{$objClass->getPk()[0]} = {$id}")) {
    Utils::jsonResponse('Registro n&atilde;o encontrado.');
}

$obj = $Modules['class']::load($id);
$obj->set('ativo', $to);
$obj->save();

Utils::jsonResponse('',true);