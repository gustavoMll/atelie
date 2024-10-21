<?php

Utils::ajaxHeader();

$module = $request->get('class');
$id = $request->getInt('id');

if (!file_exists(__DIR__. "/../config.{$module}.php")) {
    Utils::jsonResponse('M&oacute;dulo n&atilde;o instalado. Contate o suporte t&eacute;cnico.');
}

if(!$objSession->hasPermition($module, ($id == 0 ? 'ins' : 'upd'))){
    Utils::jsonResponse("Desculpe, voc&ecirc; n&atilde;o possui permiss&atilde;o para realizar esta opera&ccedil;&atilde;o neste m&oacute;dulo.");
}

include(__DIR__. "/../config.{$module}.php");
$objClass = new $Modules['class']();

$ret = $Modules['class']::saveForm();

$output = ob_get_contents();
ob_end_clean();

if(!$ret['success']){
    Utils::jsonResponse($output);
}

Utils::jsonResponse($output, true, ['obj' => $ret['obj']->getParams()]);