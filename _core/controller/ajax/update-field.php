<?php

$module = $request->get('class');
$field = $request->get('field', 'nome');

if (!file_exists(__DIR__. "/../config.{$module}.php")) {
    Utils::jsonResponse('',false, ['options' => '<option value="">M&oacute;dulo n&atilde;o instalado</option>']);
}

include(__DIR__. "/../config.{$module}.php");
$objClass = new $Modules['class']();
$paramAdd = $Modules['class']::filter($request);

if($request->getInt('nsli') == 1 ){ //no select last inserted
    $id = 0;
}else{
    $rs = $Modules['class']::search(['s' => "MAX({$objClass->getPk()[0]}) id"]);
    $rs->next();
    $id = $rs->getInt('id');
}

$rs = $Modules['class']::search(['w' => $paramAdd, 'o' => $Modules['ordenacao']]);
if($rs->numRows() == 0 ){
    Utils::jsonResponse('', true, ['options' => '<option value="">Nenhum registro encontrado</option>']);
}

$options = '';
while($rs->next()){
    $selected = $rs->getInt($objClass->getPk()[0]) == $id ? ' selected': '';
    $options .= '<option value="'.$rs->getInt($objClass->getPk()[0]).'" '.$selected.'>'.$rs->getString($field).'</option>';
}

Utils::jsonResponse('', true, ['options' => $options]);