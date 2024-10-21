<?php

Utils::ajaxHeader();

$module = $request->get('class');
$field = $request->get('field', 'nome');

if (!file_exists(__DIR__. "/../config.{$module}.php")) {
    Utils::jsonResponse('M&oacute;dulo n&atilde;o instalado. Contate o suporte t&eacute;cnico.');
}

include(__DIR__. "/../config.{$module}.php");
$objClass = new $Modules['class']();

$rs = $Modules['class']::search(['s' => "MAX({$objClass->getPk()[0]}) id"]);
$rs->next();
$id = $rs->getInt('id');

if($id == 0){
    Utils::jsonResponse('N&atilde;o encontrado');
}

$obj = $Modules['class']::load($id);

Utils::jsonResponse('',true, [
    'nome' => $obj->get($field),
    'id' => $id,
    'obj' => $obj->getParams(),
]);