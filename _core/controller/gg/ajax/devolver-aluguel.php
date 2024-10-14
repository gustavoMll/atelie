<?php 
Utils::ajaxHeader();

$id = $request->get('id');
if(!Aluguel::exists("id = {$id}")){
    Utils::jsonResponse("Aluguel inválido", false);
}

$data = str_replace('-', '/', $request->get('data'));
if(!Utils::dateValid($data)){
    Utils::jsonResponse("Data inválida", false);
}

$obj = Aluguel::load($id);
$valor_pago = $request->get('valor');
if($valor_pago > $obj->get('valor_restante')){
    Utils::jsonResponse("Valor inválido", false);
}
// echo $valor_pago; exit;
$ret = $obj->realizarDevolucao($data, $valor_pago);
Utils::jsonResponse($ret['msg'], $ret['success']);