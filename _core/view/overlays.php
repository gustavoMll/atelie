<script>

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
    <div class="modal-dialog modal-fullscreen-sm-down <?=$request->get('module') == 'alugueis' ? 'modal-lg' : ''?>">
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