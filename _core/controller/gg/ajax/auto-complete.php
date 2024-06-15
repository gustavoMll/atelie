<?php
Utils::ajaxHeader();

$module = $request->get('class');
$term = urldecode($request->get('term'));
$name = $request->get('camponome','nome');

if (!file_exists(__DIR__. "/../config.{$module}.php")) {
    echo json_encode(['value' => '', 'label' => 'M&oacute;dulo n&atilde;o instalado. Contate o suporte t&eacute;cnico.']);
    exit;
}

include(__DIR__. "/../config.{$module}.php");
$objClass = new $Modules['class']();

$paramAdd = $Modules['class']::filter($request);

$rs = $Modules['class']::search([
    's' => "{$objClass->getPk()[0]},{$name}",
    'w' => $paramAdd,
    'a' => $term,
    'o' => '2',
]);

$json = array();
while($rs->next()){
    $json[] = array('value' => "{$rs->getInt($objClass->getPk()[0])}", 'label' => $rs->getString($name));
}

echo json_encode($json);
exit;       
                