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
$ret = $obj->realizarDevolucao($data);
Utils::jsonResponse($ret['msg'], $ret['success']);