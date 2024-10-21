<?php
$view['title'] = 'Configurações Gerais';
$view['dados'] = array();

$view['dados']['zapi_id'] = $view['dados']['zapi_token'] = '';

$conn = new Connection();
$rs = $conn->prepareStatement("SELECT chave, valor FROM configs")->executeReader();

while($rs->next()){
	$view['dados'] += array($rs->getString('chave') => $rs->getString('valor'));
}

if($request->get('action') == 'desconectar-zapi'){

	$zapi = new ZApi($view['dados']['zapi_id'], $view['dados']['zapi_token']);
    if($zapi->isConencted()){
        $zapi->disconnect();
    }
    Utils::location(__PATH__.$request->get('module').'/index/msg/success');

}elseif(isset($_POST) && $request->get('action') == 'save'){

	//print_r($_POST);exit;
	$conn = new Connection();
	$conn->prepareStatement("DELETE FROM configs")->executeQuery();
	$Config = new Config();
	foreach ($_POST as $k=>$v) {
		$Config->set($k, $v);
	}

	Utils::location(__PATH__.$request->get('module').'/index/msg/success');
	exit;	
}
