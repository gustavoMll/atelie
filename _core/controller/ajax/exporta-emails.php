<?php

Utils::ajaxHeader();

$separador = $_POST['separador'];

if($separador == ''){
	$separador = ',';
}

$retorno['ok'] = true;
$rs = Lead::search([
	's' => "GROUP_CONCAT(email SEPARATOR '{$separador} ') emails",
	'w' => 'ativo=1',
	'o' => 'email',
]);
$html = '';
if($rs->next()){
	$html .= $rs->getString('emails');
}

Utils::jsonResponse('', true, ['html' => $html]);        