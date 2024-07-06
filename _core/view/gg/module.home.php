<div class="home">
    <div>
        <div class="d-flex justify-content-between">
            <div>
                <h1>Pr&oacute;ximos Alugu&eacute;is</h1>
            </div>
        </div>
    </div>
    <div>
        <?php if(!count($view['alugueis'])){?>
            <div class="alert alert-light" role="alert">
                Nenhum aluguel encontrado
            </div>
        <?php }else{?>
        <div class="accordion mt-3" id="accordionPanelsStayOpenExample">
            <?php foreach($view['alugueis'] as $obj){?>
            <div class="accordion-item">
                <h2 class="accordion-header">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapse<?=$obj->get('id')?>" aria-expanded="true" aria-controls="panelsStayOpen-collapse<?=$obj->get('id')?>">
                    <h6>Prazo de Devolução: <strong><?=Utils::dateFormat($obj->get('dt_prazo'), 'd/m/Y')?> - <?=$obj->getPedido()->getCliente()->getPessoa()->get('nome')?></strong>
                    </h6>
                </button>
                </h2>
                <div id="panelsStayOpen-collapse<?=$obj->get('id')?>" class="accordion-collapse collapse show">
                    <div class="accordion-body">
                        <div class="row">
                            <div class="col-10 row">
                                <?=$obj->getItensAluguel()?>
                            </div>
                            <div class="col-2 text-end">
                                <a class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modal_baixa"><i class="ti ti-coin"></i></a>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <div>
                                <span class="fs-5">Valor Total: R$ <strong><?=Utils::parseMoney($obj->getValorAluguel())?></strong></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php }
            }?>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_baixa" tabindex="-1" aria-labelledby="modal_baixaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modal_baixaLabel">Baixar Aluguel</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-8 mb-3">
                        <div class="form-floating">
                            <input name="dt_devolucao" id="dt_devolucao" maxlength="255" type="text" placeholder="seu dado aqui" class="form-control date" value="<?=date('d/m/Y')?>">
                            <label for="dt_devolucao">Data de Devolu&ccedil;o</label>
                        </div>
                    </div>
                    
                    <div class="col-sm-4 mb-3">
                        <div class="form-floating">
                            <input name="valor" id="valor" maxlength="255" type="text" placeholder="seu dado aqui" class="form-control money" value="">
                            <label for="valor">Valor</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary text-white fw-bold" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary fw-bold">Salvar</button>
            </div>
        </div>
    </div>
</div>

<script>
    function baixarAluguel(id){
        Swal.fire({
            title: "Confirmar baixa do aluguel  ?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Sim, deletar!",
            cancelButtonText: "Cancelar!"
            }).then((result) => {
            if (result.isConfirmed) {
                blockUi();
                $.ajax({
                    url: '<?=__PATH__.$request->get('module')?>/remover/id/'+id+'/classe/'+classe,
                    dataType: `json`,
                    type: 'GET',
                    success: function(resp) {
                        unblockUi();
                        if(resp.success) {
                            $('#'+resp.ret).fadeOut('slow');
                        }else{
                            MessageBox.error(resp.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        unblockUi();
                        MessageBox.error('Ocorreu um erro. Para detalhes pressione F12 e verifique no console.');
                    }
                })
            }
        });
    }

</script>