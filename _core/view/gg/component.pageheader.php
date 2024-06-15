<div class="d-flex align-items-center justify-content-around gap-5">
    <div class="module-title">
        <h2 class="fw-bold text-center text-md-start mb-0 lh-1">
            <strong><?=$view['title']?></strong>
        </h2>
        <p class="fw-normal small mb-0 text-center text-md-start text-gray">Registros: <span data-model="<?=$view['modulo']?>" data-type="qtdRegistros"></span></p>
    </div>
    
    <div class="d-flex align-items-center gap-1 flex-wrap module-buttons">
        <button class="btn btn-danger btn-sm btn-md-normal btn-delete-selected" data-toggle="tooltip" onclick="deletaRegitrosSelecionados('<?= $view['modulo'] ?>');" data-placement="top" title="Apagar selecionado(s)" style="display:none;" >
            <i class="ti ti-trash"></i> <span class="d-none d-md-inline-block">Apagar selecionados</span>
        </button>
        <?php if($view['list-filter'] != ''){ ?>
        <button type="button" onclick="$('#modalFilter').modal('show')" class="btn btn-sm btn-md-normal btn-dark">
            <i class="ti ti-filter"></i> <span class="d-none d-md-inline-block">Filtro</span>
        </button>
        <?php } ?>
        <button type="button" onclick="modalForm('<?=$view['modulo']?>',0,'',function(){ tableList('<?=$view['modulo']?>', window.location.search.substr(1), 'resultados', false); }); return false;" class="btn btn-sm btn-md-normal btn-secondary text-white">
            <i class="ti ti-plus"></i> <span class="d-none d-md-inline-block">Adicionar</span>
        </button>
    </div>
</div>

<script>
    function controlDelButton(){
        if($('.chkDel:checked').length > 0){
            $('.btn-delete-selected').fadeIn();
        }else{
            $('.btn-delete-selected').fadeOut();
        }
    }

    function marcaCheckBoxGG(el) {
        $('.chkDel').prop('checked',el.checked);
        controlDelButton();
    }

    function deletaRegitrosSelecionados(module) {
        var ids = '';
        $('.chkDel:checked').each(function(i,el){
            ids += (ids == '' ? '' : ',') + el.value;
        });
        if (ids != '') {
            delFormAction(module, ids, function(){ 
                $('.chkDel:checked').each(function(i,el){
                    $(`#tr-${module}${el.value}`).fadeOut(`slow`, function(){ $(this).remove(); });
                });
                $('.btn-delete-selected').fadeOut();
            });
            
        } else {
            tAlert('','Favor selecionar ao menos um registro.','e');
        }
    }
</script>