<?php
Utils::ajaxHeader();

$module = $request->get('class');
$id = $request->getInt('id');
$field = $request->get('imagem', 'img');

if (!file_exists(__DIR__. "/../config.{$module}.php")) {
    Utils::jsonResponse('M&oacute;dulo n&atilde;o instalado. Contate o suporte t&eacute;cnico.');
}

if(!$objSession->hasPermition($module, 'upd')){
    Utils::jsonResponse("Desculpe, voc&ecirc; n&atilde;o possui permiss&atilde;o para realizar esta opera&ccedil;&atilde;o neste m&oacute;dulo.");
}

include(__DIR__. "/../config.{$module}.php");
$objClass = new $Modules['class']();

if(!$Modules['class']::exists("{$objClass->getPk()[0]} = {$id}")){
    Utils::jsonResponse('Registro n&atilde;o encontrado.');
}

$Modules['class']::deleteImage($id, $field);
Utils::jsonResponse('Imagem exclu&iacute;da com sucesso.', true);
