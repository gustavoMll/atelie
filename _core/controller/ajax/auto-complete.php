<?php

Utils::ajaxHeader();

$module = $request->get('class');
$term = urldecode($request->get('term'));
$data_div = $request->get('data-div');
$input_aux = $request->get('input-aux');
$fields = $request->get('camponome','nome');
$filter = $request->query('dt_coleta');
$dt_coleta = ($request->query('dt_coleta'));
$dt_prazo = ($request->query('dt_prazo'));


if (!file_exists(__DIR__. "/../config.{$module}.php")) {
    echo json_encode(['value' => '', 'label' => 'M&oacute;dulo n&atilde;o instalado. Contate o suporte t&eacute;cnico.']);
    exit;
}

$arr = explode('-', $fields);
$name = $arr[0];

if(count($arr) > 1){
    $name .= ', ';
}
for($i=1;$i<count($arr)-1;$i++){
    $name .= $arr[$i] .', ';
}
if(count($arr) > 1){
    $name .= $arr[$i];
}

include(__DIR__. "/../config.{$module}.php");
$objClass = new $Modules['class']();

$paramAdd = $Modules['class']::filter($request);

if($Modules['class'] == 'Pessoa'){
    $paramAdd .= " AND id IN (SELECT id_pessoa FROM clientes)";
}

$where = '';
if ($dt_coleta != '' || $dt_prazo != ''){
    $tipo = 1;
    $parts_coleta = explode('-',$dt_coleta);
    $parts_prazo = explode('-',$dt_prazo);
    if( $Modules['class'] == 'Fantasia'){
        $tipo = 2;
    }
    $dt_coleta = $parts_coleta[2] .'-'.$parts_coleta[1].'-'.$parts_coleta[0];
    $dt_prazo = $parts_prazo[2] .'-'.$parts_prazo[1].'-'.$parts_prazo[0];

    $objIA = new ItemAluguel();
    $objA = new Aluguel();
    $where = " AND id NOT IN (
        SELECT id_item FROM {$objIA->getTableName()} WHERE tipo_item = {$tipo} AND id_aluguel IN 
        (
            SELECT id FROM {$objA->getTableName()} WHERE dt_coleta >= '{$dt_coleta}' AND dt_prazo <= '{$dt_prazo}' AND status <> " . Aluguel::$status_devolvido . "
        )
    )";
}

$rs = $Modules['class']::search([
    's' => "{$objClass->getPk()[0]},{$name}",
    'w' => "{$paramAdd} {$where}",
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

    $campos = array();
    $label = $rs->getString($arr[0]);
    $campo = $rs->getString($arr[0]);
    $campos[0] = $campo;
    if(count($arr) > 1){
        for($i=1;$i<count($arr);$i++){
            $campo = $rs->getString($arr[$i]);
            if($arr[$i] == 'cpf'){
                $campo =  Utils::getMaskedDoc($rs->getString($arr[$i]));
            }elseif($campo == 'dt_nasc'){
                $campo = Utils::dateFormat($rs->getString($arr[$i]), 'd/m/Y');
            }

            if($input_aux == ''){
                if($data_div == "()"){
                    $label .= ' ('.$campo.")";
                }elseif($data_div == "/"){
                    $label .= '/'.$campo;
                }
            }
            $campos[] = $campo;
        }
        
    }

    $json[] = array('value' => "{$id}", 'label' => $label, 'campos' => $campos);
}

echo json_encode($json);
exit;       
                