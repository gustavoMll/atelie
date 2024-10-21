
<h2 class="mb-0 me-auto h4">
    <?=$view['title']?>
    <small class="opacity-50 fs-6">(<span data-model="<?=$view['modulo']?>" data-type="qtdRegistros"></span><span class="d-none d-md-inline"> registros</span>)</small>
</h2>

<div class="d-flex flex-grow-1 flex-xl-grow-0 gap-2 ms-auto">

    <div class="btn-delete-selected" style="display:none;">
        <button class="btn btn-danger text-white fw-bold p-2 px-lg-3" data-bs-toggle="tooltip" onclick="deletaRegitrosSelecionados('<?= $view['modulo'] ?>');" data-bs-placement="right" title="Apagar selecionado(s)" >
            <i class="ti ti-trash"></i> 
            <span class="d-none d-lg-inline-block">Apagar selecionados</span>
        </button>
    </div>

    <?php if($request->get('module') == 'pessoas'){?>
    <form class="flex-fill" onsubmit="$(`#filterNomefantasia`).val($(`#filterNome`).val()); return modalFilter(this, `pessoas`);"><input id="filterNome" type="text" placeholder="Pesquisar pelo nome (enter)" class="fw-normal form-control h-100" name="nomefantasia" value="<?=$request->query('nomefantasia')?>" /></form>
    <?php } ?>

    <?php if($view['list-filter'] != ''){ ?>
    <div class="ms-auto">
        <button type="button" id="btnSearchBase" onclick="$('#modalFilter').modal('show')" class="d-flex gap-2 align-items-center btn btn-light text-black-75 border-dark border-opacity-10 p-2 px-lg-3 text-nowrap">
            <i class="ti ti-filter"></i> 
            <span class="d-none d-lg-inline-block">Filtro</span> 
            <span class="d-none d-lg-inline-block text-black-50 fw-normal small">F3</span>
        </button>
    </div>
    <?php } ?>

    <div>
        <button type="button" id="btnAddBase" onclick="modalForm('<?=$view['modulo']?>',0,'',function(){ tableList('<?=$view['modulo']?>', window.location.search.substr(1), 'resultados', false); }); return false;" class="d-flex gap-2 align-items-center btn btn-primary text-white fw-bold p-2 px-lg-3">
            <i class="ti ti-plus"></i> 
            <span class="d-none d-lg-inline-block">Adicionar</span> 
            <span class="d-none d-lg-inline-block opacity-50 fw-normal small">F2</span>
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