<?php

$ids = $_POST['ids'];
$data = $_POST['data'];

foreach ($ids as $id) {
    $objConta = Conta::load($id);

    if($objConta->getValorPago() < $objConta->get('valor')){
        $objeto = new Movimentacao();
        $objeto->set('tipo', $objConta->get('tipo'));
        $objeto->set('id_conta', $objConta->get('id'));
        $objeto->set('id_projeto', 0);
        $objeto->set('status', 3);
        $objeto->set('data', Utils::dateFormat($data, 'Y-m-d'));
        $objeto->set('obs', 'Baixa');
        $objeto->set('valor', $objeto->getConta()->get('valor')-$objeto->getConta()->totalRecebido());
        $objeto->set('juro', 0);
        $objeto->set('multa', 0);
        $objeto->set('desconto', 0);
        
        $objeto->save();
        
        $objConta->set('status',3);
        $objConta->set('pagamento',$objeto->get('data'));
    
        $objConta->save();
        unset($objeto,$objConta);
    }
}

Utils::jsonResponse('Baixa realizada com sucesso.',true);
