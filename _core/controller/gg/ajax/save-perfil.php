<?php

Utils::ajaxHeader();

$objSession->set('nome', $request->post('perfilNome'));
$objSession->set('email', $request->post('perfilEmail'));
$objSession->set('tel', Utils::replace('/[^0-9]/','',$request->post('perfilTel')));

$imgBefore = '';
if (isset($_FILES['img']) && $_FILES['img']['name'] != '') {
    $imgBefore = $objSession->get('img');
    $objSession->set('img', Image::configureName($_FILES['img']['name']));
}

if (isset($_FILES['img']) && $_FILES['img']['name'] != '') {
    Image::saveFromUpload($imgBefore,'img', Usuario::$tamImg, $objSession->id, $objSession->getTableName());
}

if($_POST["perfilSenha"] != '')
    $objSession->set('senha', Usuario::encrypt($_POST["perfilSenha"]));

$objSession->save();

Utils::jsonResponse('Dados salvos com sucesso', true);