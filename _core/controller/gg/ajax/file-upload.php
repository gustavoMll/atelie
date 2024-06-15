<?php

$module = $request->post('class');
$id = (int) $request->post('id',0);
$tid = (int) $request->post('tid',0);
$type = $request->post('type');

if($id==0){
    $id = $tid;
}

if($module == '' || ($id == 0 && $tid == 0) || $type == ''){
    echo json_encode([
        'jsonrpc' => 2.0,
        'error' => [
            'code' => 103,
            'message' => 'Invalid configuration data',
        ],
        'id' => $id,
    ]);
    exit;
}

if ($_FILES["file"]["name"]=='') {
    echo json_encode([
        'jsonrpc' => 2.0,
        'error' => [
            'code' => 103,
            'message' => 'No file added.',
        ],
        'id' => $id,
    ]);
    exit;
}

if($type == 'arquivos'){
    $obj = new Arquivo();
    $obj->set('tipo', $module);
    $obj->set('id_tipo', $id);
    $obj->set('descricao', File::configureName($_FILES["file"]["name"]));
    $obj->set('file', File::configureName($_FILES["file"]["name"]));
    $obj->save();

    if( !File::saveFromUpload('', 'file', $obj->get('id'), "{$module}/{$id}/") ){
        Arquivo::delete($obj->get('id'));
    }
}else{
    $rs = Foto::search([
        's' => 'IFNULL(MAX(ordem),0)+1 ordem',
        'w' => "id_tipo = {$id} AND tipo = '{$module}'",
    ]);
    $rs->next();
     
    $obj = new Foto();
    $obj->set('tipo', $module);
    $obj->set('id_tipo', $id);
    $obj->set('descricao', '');
    $obj->set('img', Image::configureName($_FILES["file"]["name"], 'file'));
    $obj->set('ordem', $rs->getInt('ordem'));
    $obj->save();
    Image::saveFromUpload('','file', Foto::$tamImg, $obj->get('id'), "{$module}/{$id}/");
}

echo json_encode([
    'jsonrpc' => 2.0,
    'result' => null,
    'id' => $obj->get('id'),
]);
exit;