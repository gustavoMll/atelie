<?php

Utils::ajaxHeader();

$id = $request->getInt('id');
if(!Foto::exists("{$id}=id")){
    Utils::jsonResponse('Imagem n&atilde;o encontrada.');
}

$obj = Foto::load($id);
$oldName = $obj->get('img');
$newName = md5(microtime()).'.'.Utils::getFileExtension($oldName);

foreach (Foto::$tamImg as $key => $value) {
    $path = $obj->imagePath($key,'img',0, $obj->get('tipo').'/'.$obj->get('id_tipo').'/');
    $original = \WideImage\WideImage::load($path);
    $newImg = $original->rotate(90);
    $newImg->saveToFile(str_replace($oldName, $newName, $path));
    unlink($path);
}
$obj->set('img', $newName);
$obj->save();

Utils::jsonResponse('Sucesso girando imagem', true);
