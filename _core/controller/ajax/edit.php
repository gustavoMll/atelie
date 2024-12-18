<?php 

Utils::ajaxHeader();

$id = $request->getInt('id');
$module = $request->get('class');

if (!file_exists(__DIR__. "/../config.{$module}.php")) {
    Utils::jsonResponse('M&oacute;dulo n&atilde;o instalado. Contate o suporte t&eacute;cnico.');
}

if(!$objSession->hasPermition($module,'sel')){
    Utils::jsonResponse("Desculpe, voc&ecirc; n&atilde;o possui permiss&atilde;o para realizar esta opera&ccedil;&atilde;o neste m&oacute;dulo.");
}

include(__DIR__. "/../config.{$module}.php");
$objClass = new $Modules['class']();

if (!$Modules['class']::exists("{$objClass->getPk()[0]} = {$id}")) {
    Utils::jsonResponse('Registro n&atilde;o encontrado.');
}

$html = '
<form id="form-dados-'.time().'" name="form-dados" '.($Modules['envia-arquivo'] ? ' enctype="multipart/form-data" ' : '') . ' method="post">
    <div class="row">
    '.$Modules['class']::form($id).'
    </div>
</form>';

Utils::jsonResponse('',true,[
    'html' => $html,
    'title' => 'Editar <span class="glyphicon glyphicon-chevron-right small"></span> '.$Modules['nome'],
    'modalSize' => $objClass->modalSize,
]);
