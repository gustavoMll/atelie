<?php

Utils::ajaxHeader();

$module = $request->get('class');
$id = $request->getInt('id');

if (!file_exists(__DIR__. "/../config.{$module}.php")) {
    Utils::jsonResponse('M&oacute;dulo n&atilde;o instalado. Contate o suporte t&eacute;cnico.');
}

include(__DIR__. "/../config.{$module}.php");
$objClass = new $Modules['class']();

$obj = $Modules['class']::load($id);
$html = $Modules['class']::getLine($obj);

Utils::jsonResponse('', true, ['html' => $html]);
