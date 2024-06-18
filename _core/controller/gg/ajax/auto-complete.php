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

if($Modules['class'] == 'Pessoa'){
    $paramAdd .= " AND id IN (SELECT id_pessoa FROM clientes)";
}

$rs = $Modules['class']::search([
    's' => "{$objClass->getPk()[0]},{$name}",
    'w' => "{$paramAdd}",
    'a' => $term,
    'o' => '2',
]);

$json = array();
$id = 0;
while($rs->next()){
    $id = $rs->getInt($objClass->getPk()[0]);
    if($Modules['class'] == 'Pessoa'){
        $rs2  = Cliente::search([
            's' => 'id',
            'w' => 'id_pessoa = '.$rs->getInt($objClass->getPk()[0])
        ]);
        if($rs2->next()){
            $id = $rs2->getInt('id');
        }
    }
    $json[] = array('value' => "{$id}", 'label' => $rs->getString($name));
}

echo json_encode($json);
exit;       
                