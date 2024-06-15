<?php 
Utils::ajaxHeader();

if(isset($_SESSION[$GLOBALS['Sessao']]['autorizado'])){
    $_SESSION[$GLOBALS['Sessao']]['autorizado'] = $_SESSION[$GLOBALS['Sessao']]['autorizado'];
}

Utils::jsonResponse(date('d/m/Y H:i:s'), true);