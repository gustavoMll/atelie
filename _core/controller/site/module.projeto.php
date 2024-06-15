<?php 

$view['title'] .= 'Projeto ';

$view['projeto'] = array();
$view['movimentacoes'] = array();

$valor_recebimento = 0;
$valor_pagamento = 0;
$porcentagem_gasto = 0;

$data = '';


$action = $request->get('action');

$tipo = $request->query('tipo');

$rs = Projeto::search([
    's' => 'id',
    'w' => "MD5(CONCAT('".Projeto::$key."',id)) = '{$action}'"
]);

if($rs->next()){
    $view['projeto'] = Projeto::load($rs->getInt('id'));
}

if(!empty($view['projeto'])){    
    
    $where = '';

    $rs = Movimentacao::search([
        's' => 'id',
        'w' => "(id_projeto = {$view['projeto']->get('id')} OR id_conta IN (SELECT id FROM contas WHERE id_projeto = {$view['projeto']->get('id')}))"
    ]);

    while($rs->next()){
        $movimentacao = Movimentacao::load($rs->getInt('id'));
        $valor_total = $movimentacao->get('valor') + $movimentacao->get('juro') + $movimentacao->get('multa') - $movimentacao->get('desconto');
        if($movimentacao->get('tipo') == 1){
            $valor_pagamento += $valor_total;
        }elseif($movimentacao->get('tipo') == 2){
            $valor_recebimento += $valor_total;

        }
    }

    $porcentagem_gasto = $valor_pagamento * 100 / $view['projeto']->get('valor');

    unset($rs);

    if($tipo == 'entrada'){
        $where .= ' AND tipo=2';
    }elseif($tipo == 'saida'){
        $where .= ' AND tipo=1';
    }

    
    $rs = Movimentacao::search([
        's' => 'id',
        'w' => "(id_projeto = {$view['projeto']->get('id')} OR id_conta IN (SELECT id FROM contas WHERE id_projeto = {$view['projeto']->get('id')})) {$where}",
        'o' => 'data'
    ]);

    while($rs->next()){
        $view['movimentacoes'][] = Movimentacao::load($rs->getInt('id'));
    }

}



