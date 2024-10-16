<?php 

$view['title'] = 'Relat&oacute;rio de Alugueis - ' . $view['title'];
$view['alugueis'] = $view['itens-aluguel'] = array();

$status = $request->query('status');
$dt_ini = $request->query('dt_ini');
$dt_fim = $request->query('dt_fim');

$where = '';

if($dt_ini == ''){
    $dt_ini = '01-'.date('m-Y');
}

if($dt_fim == ''){
    $dt_fim = date("t-m-Y");
}

if($status != '' && isset(Aluguel::$arr_status[$status])){
    $where .= "status = {$status}";
}

if($dt_ini != ''){
    if($where != ''){
        $where .= " and ";
    }
    $dt_ini_form = '';
    $parts = explode('-', $dt_ini);
    if(count($parts) == 3){
        $dt_ini_form = $parts[2] . '-'. $parts[1] .'-' . $parts[0];
    };

    $where .= "DATE('dt_cad') >= {$dt_ini_form}";
}

if($dt_fim != ''){
    if($where != ''){
        $where .= " and ";
    }
    $dt_fim_form = '';
    $parts = explode('-', $dt_fim);
    if(count($parts) == 3){
        $dt_fim_form = $parts[2] . '-'. $parts[1] .'-' . $parts[0];
    };

    $where .= "DATE('dt_cad') <= {$dt_fim_form}";

}

$valor_total = 0;

$rs = Aluguel::search([
    's' => 'id',
    'w' => $where,
    'o' => 'dt_cad DESC'
]);

while($rs->next()){
    $obj = Aluguel::load($rs->getInt('id'));
    $view['alugueis'][] = $obj;
    $valor_total += $obj->get('valor_aluguel');
    $rs2 = ItemAluguel::search([
        's' => 'id',
        'w' => 'id_aluguel='.$obj->get('id')
    ]);

    while($rs2->next()){
        $view['itens-aluguel'][$obj->get('id')][] = ItemAluguel::load($rs->getInt('id'));
    }
}





