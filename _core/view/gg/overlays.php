<div class="modal modal-xl fade bg-white-sm-down" id="perfil" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="perfilModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen-sm-down modal-lg">
        <div class="modal-content">
            <form id="form-perfil" name="form-perfil" method="post" onsubmit="return savePerfil();">
                <div class="modal-header">
                    <h4 class="modal-title fw-bold text-uppercase" id="perfilModalLabel">Perfil</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">

                    <div class="row">

                        <div class="form-group col-sm-12 mb-3">
                            <label for="input_imgProfile_<?=$objSession->getTableName()?>">Foto de perfil<small class="rule"><?=implode(', ',Image::$typesAllowed)?></small></label>
                            <input name="img" id="input_imgProfile_<?=$objSession->getTableName()?>" onchange="showPreview(this, `imgProfile`, `<?=$objSession->getTableName()?>`);" type="file" class="form-control" value=""/>
                        </div><?=Usuario::getPreviewImage($objSession, "img", "profile")?>

                        <div class="col-sm-6 mb-3">
                            <div class="form-floating">
                                <input type="text" class="form-control" name="perfilNome" id="perfilNome" placeholder="nome" value="<?= $objSession->getPessoa()->get('nome') ?>">
                                <label for="perfilNome" class="form-label">Nome</label>
                            </div>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <div class="form-floating">
                                <input type="text" class="form-control" name="perfilEmail" id="perfilEmail" placeholder="email" value="<?= $objSession->get('email') ?>">
                                <label for="perfilEmail" class="form-label">E-mail</label>
                            </div>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="perfilLogin" placeholder="username" disabled value="<?= $objSession->get('login') ?>" autocomplete="username">
                                <label for="perfilLogin" class="form-label">Usu&aacute;rio</label>
                            </div>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <div class="form-floating">
                                <input type="text" class="form-control phone" name="perfilTel" id="perfilTel" placeholder="tel" value="<?= $objSession->get('tel') ?>" autocomplete="mobile">
                                <label for="perfilTel" class="form-label">Telefone</label>
                            </div>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <div class="form-floating">
                                <input type="password" name="perfilSenha" id="perfilSenha" class="form-control" placeholder="senha" autocomplete="new-password" data-type="togglePassword">
                                <label for="perfilSenha" class="form-label">Nova senha</label>
                            </div>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <div class="form-floating">
                                <input type="password" name="perfilC_senha" id="perfilC_senha" class="form-control" placeholder="confirmar senha" autocomplete="new-password" data-type="togglePassword">
                                <label for="perfilC_senha" class="form-label">Confirmar nova senha</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-secondary"><span class="glyphicon glyphicon-save"></span> Salvar</button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
    
function savePerfil() {
    if($('#perfilSenha').val() !== $('#perfilC_senha').val()){
        MessageBox.error('As senhas digitadas não coincidem!');
        return false;
    }
    var formId = 'form-perfil';
    $("#" + formId).ajaxSubmit({
        url: __PATH__ + 'ajax/save-perfil/',
        type: "POST",
        dataType: "json",
        beforeSend: function () {
            blockOnSubmit();
            $("#progressPercent").html("0%");
        },
        uploadProgress: function (event, position, total, percentComplete) {
            $("#progressPercent").html(percentComplete + "%");
        },
        success: function (resp) {
            if (resp.success) {
                MessageBox.success(resp.message);
                $("#perfil").find(".btn-close").click();
                location.reload();
            } else {
                MessageBox.error(resp.message);
            }
        },
        error: errorFunction,
        complete: function(){ unblockUi(); },
    });

    return false;
}

<?php if($objSession->hasPermition('over.parametros') || $objSession->hasPermition('over.metatags') || $objSession->hasPermition('over.contato')){ ?>

function saveConfig(id) {
    var formId = "form-config-" + id;
    $("#" + formId).ajaxSubmit({
        url: __PATH__ + "ajax/save-config/",
        type: "POST",
        dataType: "json",
        beforeSend: function () {
            blockOnSubmit();
            $("#progressPercent").html("0%");
        },
        uploadProgress: function (event, position, total, percentComplete) {
            $("#progressPercent").html(percentComplete + "%");
        },
        success: function (resp) {
            if (resp.success) {
                MessageBox.success(resp.message);
                $("#" + formId).find(".btn-close").click();
            } else {
                MessageBox.error(resp.message);
            }
        },
        error: errorFunction,
        complete: function(){ unblockUi(); }
    });

    return false;
}
<?php } ?>

</script>

<div class="modal modal-xl fade bg-white-sm-down" id="overMessage" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header" id="">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"></h4>
            </div>                        

            <div class="modal-body">
                <div class="row" id="txtOverMessage">

                </div>
            </div>
        </div>
    </div>
</div>

<?php if($view['list-filter'] != ''){ ?>
<div class="modal modal-xl fade bg-white-sm-down modal-lg" id="modalFilter" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen-sm-down">
        <form id="formFilter" class="modal-content" onsubmit="return modalFilter(this, `<?=$view['modulo']?>`);">
            <div class="modal-header">
                <h4 class="modal-title fw-bold text-uppercase">Filtro</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>                        

            <div class="modal-body p-4">
                <div class="row">
                    <?=$view['list-filter']?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary px-3 py-2" data-bs-dismiss="modal">Fechar</button>
                <button type="submit" class="btn btn-secondary text-white px-3 py-2"><i class="ti ti-filter"></i> Filtrar</button>
            </div>
        </form>
    </div>
</div>
<?php } ?>

<?php if($objSession->hasPermition('over.parametros')){ ?>


<div class="modal modal-xl fade bg-white-sm-down modal-xl" id="parametros" tabindex="-1" aria-labelledby="labelParametros" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen-sm-down">
        <div class="modal-content">
            <form id="form-config-parametros" method="post" onsubmit="javascript: return saveConfig('parametros');">
                <div class="modal-header">
                    <h4 class="modal-title fw-bold text-uppercase" id="labelParametros">Par&acirc;metros Gerais</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-sm-12 mb-3">
                                <div class="form-floating"> 
                                <input type="text" name="nome-site" id="nome-site" class="form-control" maxlength="70" placeholder="Seu dado aqui" value="<?= $Config->get('nome-site') ?>">
                                <label for="nome-site" class="form-label">T&iacute;tulo do Site <small class="rule">(M&aacute;ximo de 70 caracteres)</small></label>
                            </div>
                        </div>
                        <div class="col-sm-12 mb-3">
                            <div class="form-floating">
                                <input type="text" name="slogan" id="slogan" class="form-control" maxlength="70" placeholder="Seu dado aqui" value="<?= $Config->get('slogan') ?>">
                                <label for="slogan" class="form-label">Slogan <small class="rule">(M&aacute;ximo de 70 caracteres)</small></label>
                            </div>
                            
                        </div>
                        
                        <div class="mb-3 col-sm-6">
                            <div class="form-floating">
                                <input type="text" name="facebook" id="parametroFacebook" class="form-control" value="<?= $Config->get('facebook') ?>" placeholder="seu dado aqui">
                                <label for="parametroFacebook" class="form-label">Facebook</label>
                            </div>
                        </div>
                        
                        <div class="mb-3 col-sm-6">
                            <div class="form-floating">
                                <input type="text" name="instagram" id="parametroInstagram" class="form-control" value="<?= $Config->get('instagram') ?>" placeholder="seu dado aqui" >
                                <label for="parametroInstagram" class="form-label">Instagram</label>
                            </div>
                        </div>
                        
                        <div class="mb-3 col-sm-6">
                            <div class="form-floating">
                                <input type="text" name="linkedin" id="paramLinkedin" class="form-control" value="<?= $Config->get('linkedin') ?>" placeholder="seu dado aqui" >
                                <label for="paramLinkedin" class="form-label">Linkedin</label>
                            </div>
                        </div>

                        <div class="mb-3 col-sm-6">
                            <div class="form-floating">
                                <input type="text" name="youtube" id="paramYoutube" class="form-control" value="<?= $Config->get('youtube') ?>" placeholder="seu dado aqui">
                                <label for="paramYoutube" class="form-label">Youtube</label>
                            </div>
                        </div>

                        <div class="mb-3 col-sm-12">
                            <div class="form-floating">
                                <input type="text" name="googlemaps" id="paramGooglemaps" class="form-control" value="<?= $Config->get('googlemaps') ?>" placeholder="seu dado aqui" >
                                <label for="paramGooglemaps" class="form-label">Link do Google Maps</label>
                            </div>
                        </div>
                        
                        <div class="mb-3 col-sm-12">
                            <div class="form-floating">
                                <input type="text" name="fb-access-token" id="param-fb-access-token" class="form-control" value="<?= $Config->get('fb-access-token') ?>" placeholder="seu dado aqui" >
                                <label for="param-fb-access-token" class="form-label">Facebook API AccessToken</label>
                            </div>
                        </div>

                        <div class="mb-3 col-sm-6">
                            <div class="form-floating">
                                <input type="text" name="fb-pixel" id="param-fb-pixel" class="form-control" value="<?= $Config->get('fb-pixel') ?>" placeholder="seu dado aqui" >
                                <label for="param-fb-pixel" class="form-label">Facebook FBPixelID</label>
                            </div>
                        </div>

                        <div class="mb-3 col-sm-6">
                            <div class="form-floating">
                                <input type="text" name="ga-id" id="param-ga-id" class="form-control" value="<?= $Config->get('ga-id') ?>" placeholder="seu dado aqui" >
                                <label for="param-ga-id" class="form-label">Google Analytics ID</label>
                            </div>
                        </div>
                        
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary px-3 py-2" data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" id="btnSave-parametros" class="btn btn-secondary px-3 py-2 text-white d-flex align-items-center gap-2"><i class="ti ti-device-floppy"></i>Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>


<?php } ?>
<?php if($objSession->hasPermition('over.metatags')){ ?>


<div class="modal modal-xl fade bg-white-sm-down modal-lg" id="metatags" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen-sm-down">
        <div class="modal-content">
            <form id="form-config-meta" method="post" onsubmit="javascript: return saveConfig('meta');">
                <div class="modal-header">
                    <h4 class="modal-title fw-bold text-uppercase">Meta tags</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">

                        <div class="col-sm-12 mb-3">
                            <div class="alert alert-secondary" role="alert">
                                <h4 class="alert-heading fw-bold">O que significam?</h4>
                                <hr>
                                <p class="mb-0"> As Meta Tags s&atilde;o utilizadas para passar aos sites de busca, como o Bing e o Google, instru&ccedil;&otilde;es sobre o t&iacute;tulo da p&aacute;gina e uma breve descri&ccedil;&atilde;o a ser exibida nos resultados de busca, entre outras instru&ccedil;&otilde;es.</p>
                            </div>
                        </div>

                        <div class="form-group col-sm-12 mb-3">
                            <label for="meta-desc">Descri&ccedil;&atilde;o <small class="rule">(M&aacute;ximo de 140 caracteres)</small></label>
                            <textarea class="form-control" name="meta-desc" id="meta-desc" rows="3" maxlength="140"><?= $Config->get('meta-desc') ?></textarea>
                        </div>
                        <div class="form-group col-sm-12 mb-3">
                            <label for="keywords" class="form-label">Keywords  <small class="rule">(m&aacute;x. 10 keywords separadas por v&iacute;rgula)</small></label>
                            <span class="btn btn-dark btn-sm small text-white ti ti-help" data-bs-toggle="tooltip" data-bs-placement="top" title="Keywords, em portugu&ecirc;s palavras-chave, s&atilde;o os termos principais que determinam qual &eacute; o assunto de uma determinada p&aacute;gina da internet. &eacute; muito importante escolher as keywords certas, pois &eacute; baseado nelas que os mecanismos de buscas exibem seus resultados. Quando voc&ecirc; busca na internet pela palavra ''eletrodom&eacute;sticos'', por exemplo, s&atilde;o listados todos os sites que possuam essa como uma de suas keywords."></span>

                            <input type="text" class="form-control" name="keywords" id="keywords" value="<?= $Config->get('keywords') ?>" id="" class="view" title="Visualizar">
                        </div>

                        <div class="form-group col-sm-12 mb-3">
                            <label for="head-scripts" class="form-label">C&oacute;digos para incluir no cabe&ccedil;alho <small class="rule">(css, javascript, google analytics, facebook...)</small></label>
                            <textarea class="form-control" name="head-scripts" id="head-scripts" rows="6"><?= $Config->get('head-scripts') ?></textarea>
                        </div>

                        <div class="form-group col-sm-12 mb-3">
                            <label for="footer-scripts">C&oacute;digos para incluir no final da p&aacute;gina <small class="rule">(css, javascript, google analytics, facebook...)</small></label>
                            <textarea class="form-control" name="footer-scripts" id="footer-scripts" rows="6"><?= $Config->get('footer-scripts') ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btnClose-meta" class="btn btn-primary px-3 py-2" data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" id="btnSave-meta" class="btn btn-secondary d-flex align-items-center gap-1 px-3 py-2 text-white"><i class="ti ti-device-floppy"></i>Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>


<?php } ?>
<?php if($objSession->hasPermition('over.contato')){ ?>


<div class="modal modal-xl modal-lg bg-white-sm-down fade" id="contato" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen-sm-down">
        <div class="modal-content">
            <form id="form-config-contato" method="post" onsubmit="javascript: return saveConfig('contato');">
                <div class="modal-header">
                    <h4 class="modal-title fw-bold text-uppercase">Contato</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">

                        <div class="col-sm-12 mb-3">
                            <div class="form-floating">
                                <input type="text" name="razao" id="ctrazao" class="form-control" value="<?= $Config->get('razao') ?>" placeholder="seu dado aqui">
                                <label for="ctrazao" class="form-label">Nome da Empresa</label>
                            </div>
                        </div>

                        <div class="col-sm-6 mb-3">
                            <div class="form-floating">
                                <input type="text" name="documento" id="ctdocumento" class="form-control" value="<?= $Config->get('documento') ?>" placeholder="seu dado aqui">
                                <label for="ctdocumento" class="form-label">CPF/CNPJ</label>
                            </div>
                        </div>

                        <div class="col-sm-6 mb-3">
                            <div class="form-floating">
                                <input type="text" name="email" id="ctemail" class="form-control" value="<?= $Config->get('email') ?>" placeholder="seu dado aqui">
                                <label for="ctemail" class="form-label">E-mail</label>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-4 mb-3">
                            <div class="form-floating">
                                <input type="text" name="cep" id="ctcep" class="form-control cep" value="<?= $Config->get('cep') ?>" placeholder="seu dado aqui">
                                <label for="ctcep" class="form-label">CEP</label>
                            </div>
                        </div>

                        <div class="col-xl-7 col-md-5 mb-3">
                            <div class="form-floating">
                                <input type="text" name="endereco" id="ctendereco" class="form-control" value="<?= $Config->get('endereco') ?>" placeholder="seu dado aqui">
                                <label for="ctendereco" class="form-label">Rua</label>
                            </div>
                        </div>

                        <div class="col-xl-2 col-md-3 mb-3">
                            <div class="form-floating">
                                <input type="number" name="numero" id="ctnumero" class="form-control" value="<?= $Config->get('numero') ?>" placeholder="seu dado aqui">
                                <label for="ctnumero" class="form-label">N&uacute;mero</label>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-4 mb-3">
                            <div class="form-floating">
                                <input type="text" name="complemento" id="ctcomplemento" class="form-control" value="<?= $Config->get('complemento') ?>" placeholder="seu dado aqui">
                                <label for="ctcomplemento" class="form-label">Complemento</label>
                            </div>
                        </div>

                        <div class="col-xl-5 col-md-8 mb-3">
                            <div class="form-floating">
                                <input type="text" name="bairro" id="ctbairro" class="form-control" value="<?= $Config->get('bairro') ?>" placeholder="seu dado aqui">
                                <label for="ctbairro" class="form-label">Bairro</label>
                            </div>
                        </div>

                        <div class="col-xl-4 col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="text" name="cidade" id="ctcidade" class="form-control" value="<?= $Config->get('cidade') ?>" placeholder="seu dado aqui">
                                <label for="ctcidade" class="form-label">Cidade</label>
                            </div>
                        </div>

                        <div class="col-sm-6 mb-3">
                            <div class="form-floating">
                                <select class="form-select" name="estado" id="ctestado">
                                    <?php foreach ($GLOBALS['Estados'] as $key => $value) { ?>
                                        <option value="<?= $key ?>" <?= ($Config->get('estado') == $key ? " selected " : "") ?>><?= $value ?></option>
                                    <?php } ?>
                                </select>
                                <label for="ctestado" class="form-label">Estado</label>
                            </div>
                        </div>
                        
                        <div class="col-sm-6 mb-3">
                            <div class="form-floating">
                                <input type="text" name="telefone" id="cttelefone" class="form-control phone" value="<?= $Config->get('telefone') ?>" placeholder="seu dado aqui">
                                <label for="cttelefone" class="form-label">Telefone Fixo</label>
                            </div>
                        </div>
                        
                        <div class="col-sm-6 mb-3">
                            <div class="form-floating">
                                <input type="text" name="whatsapp" id="ctwhatsapp" class="form-control phone" value="<?= $Config->get('whatsapp') ?>" placeholder="seu dado aqui">
                                <label for="ctwhatsapp" class="form-label">Whatsapp</label>
                            </div>
                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" id="btnClose-contato" class="btn px-3 py-2 btn-primary" data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" id="btnSave-contato" class="btn px-3 py-2 btn-secondary text-white"><i class="ti ti-device-floppy"></i> Salvar</button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('ctcep').addEventListener('blur', () => getAddress('ct'));
</script>

<?php } ?>