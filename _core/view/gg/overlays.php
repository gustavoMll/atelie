<div class="modal modal-xl fade bg-white-sm-down" id="perfil" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="perfilModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen-sm-down modal-lg">
        <div class="modal-content">
            <form id="form-perfil" name="form-perfil" method="post" onsubmit="return savePerfil();">
                <div class="modal-header">
                    <h5 class="modal-title small text-uppercase" id="perfilModalLabel">Perfil</h5>
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
                                <input type="text" class="form-control" name="perfilNome" id="perfilNome" placeholder="nome" value="<?= $objSession->get('nome') ?>">
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
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn border-transparent opacity-50" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-secondary"> Salvar</button>
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

<?php if($objSession->hasPermition('configuracoes')){ ?>

function saveConfig(id) {
    var formId = "form-config-" + id;
    for (instance in CKEDITOR.instances)
        CKEDITOR.instances[instance].updateElement();
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
                <h5 class="modal-title small text-uppercase"></h5>
            </div>                        

            <div class="modal-body">
                <div class="row" id="txtOverMessage">

                </div>
            </div>
        </div>
    </div>
</div>

<?php if($view['list-filter'] != ''){ ?>
<div class="modal fade" id="modalFilter" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen-sm-down">
        <form id="formFilter" class="modal-content" onsubmit="return modalFilter(this, `<?=$view['modulo']?>`);">
            <div class="modal-header">
                <h5 class="modal-title small text-uppercase" id="filtroModalLabel">Filtro</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>                        

            <div class="modal-body p-4">
                <div class="row">
                    <?=$view['list-filter']?>
                </div>
            </div>
            <div class="modal-footer justify-content-between px-4">
                <button type="button" class="btn border-transparent opacity-50" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-secondary text-white">Filtrar</button>
            </div>
        </form>
    </div>
</div>
<?php } ?>