<?php
header('X-Robots-Tag: noindex, nofollow', true);

global $view;
$view['menus'] = array();
$view['title']  = '';
$view['modulo'] = $request->get('module');
$view['acao']   = $request->get('action');
$view['order']  = $request->get('order');
$view['list-filter'] = '';
$view['paginas'] = 0;
$view['hasHeader'] = false;

$nonLoggedModules = array("login", 'esqueceu-senha');
$freeModules = array('ajax', 'login', 'esqueceu-senha');

$view['logged'] = (isset($_SESSION[$GLOBALS['Sessao']]) && $_SESSION[$GLOBALS['Sessao']]['autorizado']);
if($view['logged']){
    $_SESSION[$GLOBALS['Sessao']]['obj'] = Usuario::load($_SESSION[$GLOBALS['Sessao']]['obj']->get('id'));
    $objSession = $_SESSION[$GLOBALS['Sessao']]['obj'];
}else{
    $objSession = new Usuario();
}

if ($view['logged']) {
    if (file_exists(dirname(__FILE__) . "/module.{$view['modulo']}.php")) {
        if ($objSession->hasPermition($view['modulo']) || in_array($view['modulo'], $freeModules)){
            $view['module'] = "{$request->get('module')}.php";
        }
        
    }else {
        if ($view['modulo'] == '') {
            $view['module'] = 'proximas-coletas.php';
            $view['page_class'] = 'proximas-coletas';
            
        } elseif (file_exists(dirname(__FILE__) . "/config.{$view['modulo']}.php")) {
            include(dirname(__FILE__) . "/config.{$view['modulo']}.php");
            if ($objSession->hasPermition($view['modulo'])) {
                $view['module'] = "base.php";
            }
        }
    }

    $view['menus'] = $objSession->getMenusUsuario();
    
} else {
    $view['page_class'] = 'login';
    
    if (in_array($view['modulo'], $nonLoggedModules)) {
        $view['module'] = "{$view['modulo']}.php";
    } else {
        $view['module'] = "login.php";
    }
}

include dirname(__FILE__) . "/module." . $view['module'];
