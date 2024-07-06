<?php
Utils::ajaxHeader();

$module = $request->get('class');
$id = Utils::replace('/[^0-9\,]/','',$request->get('id'));

if (!file_exists(__DIR__. "/../config.{$module}.php")) {
    Utils::jsonResponse('M&oacute;dulo n&atilde;o instalado. Contate o suporte t&eacute;cnico.');
}

if(!$objSession->hasPermition($module,'del')){
    Utils::jsonResponse("Desculpe, voc&ecirc; n&atilde;o possui permiss&atilde;o para realizar esta opera&ccedil;&atilde;o neste m&oacute;dulo.");
}

include(__DIR__. "/../config.{$module}.php");
$objClass = new $Modules['class']();

if(!$Modules['class']::delete($id)){
    $msg = 'Erro ao excluir registro.';
    if($Modules['class'] == 'Acessorio' || $Modules['class'] == 'Fantasia' || $Modules['class'] == 'Cliente'){
        $msg .= ' Este registro está vinculado a um aluguel.';
    }
    Utils::jsonResponse($msg);
}

Utils::jsonResponse('Registro exclu&iacute;do com sucesso.',true);
