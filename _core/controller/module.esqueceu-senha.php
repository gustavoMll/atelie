<?php
$action = $request->get('action');
$msg = "";

 
if($action == 'send-email') {
    Utils::ajaxHeader();
    if(!isset($_POST['login']) || $_POST['login'] == '')
        Utils::jsonResponse('Verifique se os campos foram preenchidos corretamente');
    $login = $_POST['login'];
    $somenteNumeros = Utils::replace('/[^0-9]/','',$_POST['login']);
    
    $rs = Usuario::search([
        's' => 'id',
        'w' => "tel = '{$somenteNumeros}' OR login = '{$login}' OR email = '{$login}'",
        'l' => 1
    ]);

    if(!$rs->next()) Utils::jsonResponse('Usu&aacute;rio n&atilde;o encontrado');

    $id_usuario = $rs->getInt('id');

    $token = rand(111111, 999999);

    $url = '';

    $objUsuario = Usuario::load($id_usuario);
    $objUsuario->set('token', $token);
    $objUsuario->save();


    $msg = "
    <p>Ol&aacute; {$objUsuario->get('nome')},</p>
    <p>Conforme solicitado, segue abaixo o c&oacute;digo para gera&ccedil;&atilde;o de uma nova senha:</p>
    <p><strong>{$token}</strong></p>
    <p>Atenciosamente,<br>
    Equipe {$GLOBALS['Dominio']}</p>";
    
    if (Mail::send($GLOBALS['EmailUsername'], $GLOBALS['AppName'], "Recupera&ccedil;&atilde;o de senha - {$GLOBALS['Dominio']}", array($objUsuario->get('email')), $msg)) $url .= 'success';
    else $url .= 'error-mail';

    $html = '
    <div class="d-flex flex-column justify-content-between">
        <input type="hidden" id="id_usuario" name="id_usuario" value="'.$id_usuario.'" />
        <h6 class="mb-3 text-white">
            Informe abaixo o c&oacute;digo enviado por e-mail
        </h6>
        <div class="col-md-12 mb-3">
            <div class="d-flex gap-1 no-wrap digits" id="digits">
                <input id="digit1" name="digit1" autocomplete="off" class="digit-input form-control input-white aspect-ratio-1x1" value="" size="1" maxlength="1" autocomplete="off" type="text">
                <input id="digit2" name="digit2" autocomplete="off" class="digit-input form-control input-white aspect-ratio-1x1" value="" size="1" maxlength="1" autocomplete="off" type="text">
                <input id="digit3" name="digit3" autocomplete="off" class="digit-input form-control input-white aspect-ratio-1x1" value="" size="1" maxlength="1" autocomplete="off" type="text">
                <input id="digit4" name="digit4" autocomplete="off" class="digit-input form-control input-white aspect-ratio-1x1" value="" size="1" maxlength="1" autocomplete="off" type="text">
                <input id="digit5" name="digit5" autocomplete="off" class="digit-input form-control input-white aspect-ratio-1x1" value="" size="1" maxlength="1" autocomplete="off" type="text">
                <input id="digit6" name="digit6" autocomplete="off" class="digit-input form-control input-white aspect-ratio-1x1" value="" size="1" maxlength="1" autocomplete="off" type="text">
            </div>
        </div>
        <div class="text-white mb-3 small">
            Não recebeu o código de verificação? 
            <a id="reenviarCodigoLink" href="javascript:;" class="fw-bold text-secondary" onclick="reenviarEmail('.$token.','.$objUsuario->get('id').');">Reenviar código!</a>
        
        </div>
        <div class="d-flex flex-wrap-reverse flex-lg-nowrap align-items-center gap-3">
            <a class="btn btn-outline-secondary flex-fill col-sm-4" href="'.__PATH__.$request->get('module').'">Cancelar</a>
            <a class="btn btn-secondary flex-fill col-sm-8" href="javascript:;" onclick="return confirmEmail();">Verificar</a>
        </div>
    </div>
    <script>
        document.getElementById("digits").addEventListener("paste", (e) => {
            e.preventDefault();
        
            const text = (e.clipboardData || window.clipboardData).getData("text");
        
            const chars = text.split("");
        
            const inputs = document.querySelectorAll("#digits input");
        
            chars.forEach((char, index) => {
                if (inputs[index]) {
                    inputs[index].value = char;
                }
            });
        });
    </script>
    ';         

    Utils::jsonResponse('Enviamos um e-mail com um c&oacute;digo, acesse seu e-mail e confira.', true, ['html' => $html]);

}elseif($action == 'confirm-email') {

    Utils::ajaxHeader();

    $html = '';
    $token = Utils::replace('/[^0-9]/','',$_POST['token']);
    $id = (int) Utils::replace('/[^0-9]/','',$_POST['id']);

    if (strlen($token) != 6) {
        Utils::jsonResponse('Código inv&aacute;lido', false);
    }

    $rs = Usuario::search([
        's' => 'id',
        'w' => "token = '{$token}' AND id = {$id}",
    ]);

    if (!$rs->next()) {
        Utils::jsonResponse('Código inv&aacute;lido');
    }

    $html .= '
    <form onsubmit="return changePassword(this);" method="post" class="h-100 d-flex flex-column justify-content-between">
        <div>
            <h4 class="fw-bold text-secondary mb-3">Alterar senha</h4>
            <p class="small text-white">Preencha uma nova senha de acesso nos campos abaixo.</p>
        </div>';

    $html .=  ' 
        <input type="hidden" value="'.$rs->getInt('id').'" name="id_usuario">
        <div>
            <div class="form-floating mb-3">
                <input type="password" name="senha" class="form-control" placeholder="Digite a nova senha" required>
                <label>Nova senha</label>
            </div>
            <div class="form-floating mb-3">
                <input type="password" name="csenha" class="form-control" placeholder="Digite a nova senha novamente" required>
                <label>Confirme a senha</label>
            </div>
        </div>
        <div class="d-flex flex-wrap-reverse flex-lg-nowrap align-items-center gap-3">
            <a class="btn btn-outline-secondary col-sm-4 flex-fill" href="'.__PATH__.'">Cancelar</a>
            <button class="btn btn-secondary col-sm-8 flex-fill" type="submit">Alterar</button>
        </div>
    </form>

<script>
    function changePassword(form) {
        const url = `'.__PATH__.'esqueceu-senha/change-password`;
        $(form).ajaxSubmit({
            type: "POST",
            url: url,
            dataType: `json`,
            success: function (resp) {
                if(resp.success) {
                    MessageBox.success(`Senha alterada com sucesso`);
                    setTimeout(function() {
                    window.location.href = `'.__PATH__.'`;
                    }, 3000);
                } else {
                    MessageBox.error(`Senhas não confeerem.`);
                }
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            }
        });

        return false;
    }
    
</script>
';

Utils::jsonResponse("Código verificado com sucesso.", true, ['html' => $html]);     

}elseif($action == 'change-password') {
    Utils::ajaxHeader();

    $id_usuario = $_POST['id_usuario'];
    $senha = $_POST['senha'];
    $csenha = $_POST['csenha'];

    if($senha != $csenha) {
        Utils::jsonResponse('Senhas não conferem', false);
    }

    $objUsuario = Usuario::load($id_usuario);
    $objUsuario->set('senha', Usuario::encrypt($senha));
    $objUsuario->set('ip', Utils::getIp());
    $objUsuario->set('ultimo_acesso', date('Y-m-d H:i:s'));
    $objUsuario->set('ultima_tentativa', '');
    $objUsuario->save();

    Usuario::storeSession($objUsuario);

    Utils::jsonResponse('Senha Alterada com sucesso', true);

}elseif($action == 'reenviar-email') {

    Utils::ajaxHeader();

    $token = $request->get('token');
    $id = $request->get('id');

    $objUsuario = Usuario::load($id);

    $msg = "<p>Ol&aacute; {$objUsuario->get('nome')},</p>
    <p>Conforme solicitado, segue abaixo o token para gera&ccedil;&atilde;o de uma nova senha:</p>
    <p><strong>{$token}</strong></p>
    <p>Atenciosamente,<br>
    Equipe {$GLOBALS['Dominio']}</p>";
        if (Mail::send($GLOBALS['EmailUsername'], $GLOBALS['AppName'], "Recupera&ccedil;&atilde;o de senha - {$GLOBALS['Dominio']}", array($objUsuario->get('email')), $msg)) {
        $url .= 'success';
        } else {
        $url .= 'error-mail';
        }

    Utils::jsonResponse('Email reenviado com sucesso', true);

}



if($view['logged']) Utils::location(__PATH__);