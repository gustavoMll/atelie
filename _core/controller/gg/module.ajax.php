<?php

global $request, $view;

Utils::ajaxHeader();

$view['logged'] = (isset($_SESSION[$GLOBALS['Sessao']]) && $_SESSION[$GLOBALS['Sessao']]['autorizado']);
if($view['logged']){
    $_SESSION[$GLOBALS['Sessao']]['obj'] = Usuario::load($_SESSION[$GLOBALS['Sessao']]['obj']->get('id'));
    $objSession = $_SESSION[$GLOBALS['Sessao']]['obj'];
}else{
    $objSession = new Usuario();
}

$raction = $request->get('action');
$includeRAction = __DIR__.'/ajax/'.$raction.'.php';

if(!file_exists($includeRAction)){
    Utils::ajaxHeader();
    Utils::jsonResponse("A&ccedil;&atilde;o incorreta.");
}

if(!$view['logged']){
    Utils::ajaxHeader();
    Utils::jsonResponse( "Sess&atilde;o expirada. Efetue login novamente.");
}

include $includeRAction;
    
exit;