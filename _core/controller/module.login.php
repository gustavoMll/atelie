<?php

if($view['acao'] == 'in'){
	
	Utils::ajaxHeader();

	$login = $request->post('login');
	$senha = isset($_POST['senha']) ? $_POST['senha'] : '';

	if($login == '') Utils::jsonResponse('Login inv&aacute;lido');
	if($senha == '') Utils::jsonResponse('Senha inv&aacute;lida');
	
	$retCode = Usuario::auth($login, $senha);
	switch($retCode){
		case 3 :
			Utils::jsonResponse('Login efetuado com sucesso, redirecionando...', true);
			break;
		case 0: case 2 : 
			Utils::jsonResponse('Login ou senha incorretos');
			break;
	} 

	Utils::jsonResponse('M&aacute;ximo de tentativas excedido. Aguarde '.Usuario::$bruteForceTime.'min para tentar novamente.');
	
}elseif($view['acao'] == 'out'){
	Utils::ajaxHeader();
	Usuario::destroySession();
	Utils::jsonResponse('Logout efetuado com sucesso, redirecionando...', true);	
}

if($view['logged']) Utils::location(__PATH__);