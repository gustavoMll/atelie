<?php

Utils::ajaxHeader();

$module = $request->get('class');
$id = $request->getInt('id');

if (!file_exists(__DIR__. "/../config.{$module}.php")) {
    Utils::jsonResponse('M&oacute;dulo n&atilde;o instalado. Contate o suporte t&eacute;cnico.');
}

include(__DIR__. "/../config.{$module}.php");
$objClass = new $Modules['class']();

if (!$Modules['class']::exists("{$objClass->getPk()[0]} = {$id}")) {
    Utils::jsonResponse('Registro n&atilde;o encontrado.');
}

$obj = $Modules['class']::load($id);

Utils::jsonResponse('', true, ['obj' => $obj->getParams()]);