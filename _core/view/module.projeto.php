<div class="home">
<?php if($view['projeto'] != null){?>

    <input type="hidden" value="<?=date('Y/m/d')?>" id="datar" name="datar">

    <div class="mt-3 d-flex justify-content-between">
        <div class="">
            <h3><?=$view['projeto']->get('nome')?></h3>
        </div>
        <div>
            <a class="btn btn-sm" href="<?=__PATH__?>projetos"><i class="ti ti-arrow-back-up"></i>Voltar</a>
        </div>
    </div>
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="card p-3">
                <span>Cliente</span>
                <strong class="fs-5"><?=$view['projeto']->getCliente()->get('nomefantasia')?></strong>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card p-3">
                <span>Valor (R$)</span>
                <strong class="fs-5"><?=Utils::parseMoney($view['projeto']->get('valor'))?></strong>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card p-3">
                <span>Início</span>
                <strong class="fs-5"><?=(Utils::dateValid($view['projeto']->get('dt_ini')) ? Utils::dateFormat($view['projeto']->get('dt_ini'), 'd/m/Y') : '')?></strong>
            </div>
        </div>
        
        <div class="col-xl-3 col-sm-6">
            <div class="card p-3">
                <span>Previsão</span>
                <strong class="fs-5"><?=(Utils::dateValid($view['projeto']->get('dt_entrega')) ? Utils::dateFormat($view['projeto']->get('dt_entrega'), 'd/m/Y') : 'Sem previsão')?></strong>
            </div>
        </div>
    </div>

    <div class="row">
        <ul class="nav nav-underline gap-4 shadow-sm py-0 bg-white flex-nowrap overflow-x-auto scroll-transparent px-2">
            <li class="active"><button type="button" class="nav-link d-flex gap-2 align-items-center active" data-bs-toggle="tab" data-bs-target="#contas<?=$view['projeto']->getTableName()?>" role="tab" aria-controls="#contas<?=$view['projeto']->getTableName()?>" aria-selected="true">Contas</button></li>

            <li class="active"><button type="button" class="nav-link d-flex gap-2 align-items-center" onclick="loadMovimentacoes(0)"data-bs-toggle="tab" data-bs-target="#movimentacoes<?=$view['projeto']->getTableName()?>" role="tab" aria-controls="#movimentacoes<?=$view['projeto']->getTableName()?>" aria-selected="true">Movimentações</button></li>
    
            <li class="active"><button type="button" class="nav-link d-flex gap-2 align-items-center" data-bs-toggle="tab" data-bs-target="#historico<?=$view['projeto']->getTableName()?>" role="tab" aria-controls="#historico<?=$view['projeto']->getTableName()?>" aria-selected="true">Histórico</button></li>
    
            
        </ul>
        <div class="tab-content">

            <div class="tab-pane fade show active" id="contas<?=$view['projeto']->getTableName()?>">
                <div class="mt-5 card py-3">
                    <div class="row justify-content-between mb-3 mx-3">
                        <div class="col-sm-12 col-md-4">
                            <h4>Contas <?=$request->query('tipo') == 'abertas' ? 'em Aberto' : ''?></h4>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <select class="form-control" id="select_contas" onchange="filtrarContas(this.value)">
                                        <option value="abertas" <?=$request->query('tipo') == 'abertas' ? 'selected' : ''?>>Em Aberto</option>
                                        <option value="todas" <?=$request->query('tipo') == 'todas' ? 'selected' : ''?>>Todas</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card p-2 text-success">
                                <span>Total a Receber</span>
                                <strong id="total_receber">R$ 0,00</strong>
                            </div>
                        </div>

                        <div class="col">
                            <div class="card p-2 text-danger">
                                <span>Total a Pagar</span>
                                <strong id="total_pagar">R$ 0,00</strong>
                            </div>
                        </div>
                    </div>
                   
                    <div class="mb-5">
                        <div class="row" id="div_contas">
                            
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="movimentacoes<?=$view['projeto']->getTableName()?>">
                <div class="mt-5 card py-3">
                    <div class="d-flex justify-content-center mb-3">
                        <h4>Movimentações</h4>
                    </div>
                    <div class="mb-3 mx-3"> 
                        <ul class="nav nav-underline gap-4 shadow-sm py-0 bg-white flex-nowrap overflow-x-auto scroll-transparent px-2">
                            <li class="active"><button type="button" class="nav-link d-flex gap-2 align-items-center active" data-bs-toggle="tab" data-bs-target="#geral" role="tab" aria-controls="#geral" aria-selected="true" onclick="loadMovimentacoes(0)">Geral</button></li>

                            <li class="active"><button type="button" class="nav-link d-flex gap-2 align-items-center" data-bs-toggle="tab" data-bs-target="#movimentacoes_entrada" role="tab" aria-controls="#movimentacoes_entrada" aria-selected="true" onclick="loadMovimentacoes(2)">Entrada</button></li>
                            
                            <li class="active"><button type="button" class="nav-link d-flex gap-2 align-items-center" data-bs-toggle="tab" data-bs-target="#movimentacoes_saida" role="tab" aria-controls="#movimentacoes_saida" aria-selected="true" onclick="loadMovimentacoes(1)">Saída</button></li>
                        </ul>
                    </div>
                    <div class="tab-content mb-5 mt-2">
                        <div class="tab-pane fade show active" id="geral">
                            <div class="row g-3 mb-4 mx-2">
                                <div class="col-sm-4">
                                    <div class="card p-2 text-success">
                                        <span>Total Entrada</span>
                                        <strong id="total_entrada">R$ 0,00</strong>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="card p-2 text-danger">
                                        <span>Total Saída</span>
                                        <strong id="total_saida">R$ 0,00</strong>
                                    </div>
                                </div>
                               
                                <div class="col-sm-4">
                                    <div class="card p-2">
                                        <span>Saldo</span>
                                        <strong id="saldo_mov">R$ 0,00</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex" id="div_movimentacoes">
                                
                            </div>
                        </div>

                        <div class="tab-pane fade" id="movimentacoes_entrada">
                            <div class="d-flex" id="div_mov_entrada">

                            </div>
                            
                        </div>
                        
                        <div class="tab-pane fade" id="movimentacoes_saida">
                            <div class="d-flex" id="div_mov_saida">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="tab-pane fade" id="historico<?=$view['projeto']->getTableName()?>">
                <div class="mt-5 shadow-sm py-3">
                    <div class="d-flex justify-content-center gap-2 mb-3">
                        <h4>Histórico</h4>
                        <a type="button" class="btn btn-primary btn-sm g-2" onclick="modalForm(`historico`,`0`,`/id_projeto/<?=$request->get('action')?>`, loadHistorico)" data-toggle="tooltip" data-bs-placement="top" title="Novo histórico" style="cursor: pointer !important; text-decoration: none"><i class="ti ti-plus"></i></a>
                    </div>
                    <div class="mb-5">
                        <div id="div_historico">
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php }else{?>
        <div class="d-flex justify-content-center alert alert-dark">
            <h4 class="fw-bold">O projeto não foi encontrado</h4>
        </div>
    <?php }?>
</div>


<script>

    function filtrarContas(valor){
        window.location.href = "?tipo=" + valor;
    }

    function loadContas(){
        var tipo = '<?=$request->query('tipo') != '' ? $request->query('tipo') : 'abertas'?>';
        blockUi();
        $.ajax({
            url: '<?=__PATH__.$request->get('module')?>/load-contas/id_projeto/' + <?=$request->get('action')?>+'/tipo/'+tipo,
            dataType: `json`,
            type: 'GET',
            success: function(resp) {
                unblockUi();
                if(resp.success) {
                    $('#div_contas').html(resp.html);
                    $('#total_receber').html("R$ " + resp.total_receber);
                    $('#total_pagar').html("R$ " + resp.total_pagar);
                    $('#saldo_contas').html("R$ " + resp.saldo);
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
    
    function loadMovimentacoes(tipo = 0){
        console.log(tipo);
        blockUi();
        $.ajax({
            url: '<?=__PATH__.$request->get('module')?>/load-movimentacoes/id_projeto/' + <?=$request->get('action')?>+'/tipo/'+tipo,
            dataType: `json`,
            type: 'GET',
            success: function(resp) {
                unblockUi();
                if(resp.success) {
                    if(tipo == 0){ 
                        $('#div_movimentacoes').html(resp.html);
                        $('#saldo_mov').html("R$ " + resp.saldo);
                        $('#total_entrada').html("R$ " + parseMoney(resp.total_entrada));
                        $('#total_saida').html("R$ " +  parseMoney(resp.total_saida));
                        $('#saldo_movimentacoes').html("R$ " +  parseMoney(resp.saldo));
                    }
                    else if(tipo == 1) $('#div_mov_saida').html(resp.html); 
                    else if(tipo == 2) $('#div_mov_entrada').html(resp.html);
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
    
    function loadHistorico(){
        tableList(`historico`, `id_projeto=<?=$request->getInt('action')?>&offset=10`, `div_historico`, false);
    }
    
    function removeObj(classe, id, ret){
        Swal.fire({
            title: "Tem certeza que deseja remover?",
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

    function baixarContas(conta = '', tipo = '') {
        var data = $('#datar').val();
        var contas = [];
        
        contas.push(conta);

        if(conta == ''){
            contas = [];
            $('input[name="contas[]"]').each(function() {
                contas.push($(this).val());
            });
        }

        if (data == '') MessageBox.error('Data n&atilde;o informada.');
        else if (contas.length == 0) MessageBox.error('Nenhuma conta informada');
        else {
            var ids = contas;
            dtAux = data.split('/');
            blockUi();
            $.ajax({
                url: __PATH__ + 'ajax/baixar-contas/',
                type: 'POST',
                data: {
                    ids: ids,
                    data: dtAux[0] + '-' + dtAux[1] + "-" + dtAux[2],
                },
                dataType: 'json',
                success: function(resp) {
                    unblockUi();
                    MessageBox.success(resp.message);
                    setTimeout(function() {
                        location.reload()
                    }, 1500);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    unblockUi()
                    showErrorAlert('JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown)
                }
            });
        }
    }
</script>    
