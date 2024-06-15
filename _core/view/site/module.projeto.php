<style>
 .progress-container {
        position: relative;
    }
    .progress-value {
        position: absolute;
        top: -25px;
        right: 0;
        font-weight: bold;
    }
    .progress-bar {
        position: relative;
    }
    .progress-bar::before {
        content: attr(data-tooltip-complete);
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: black;
        font-weight: bold;
    }
    .progress-remaining {
        position: absolute;
        left: calc(<?=$porcentagem_gasto?>% + 10px);
        top: 50%;
        transform: translateY(-50%);
        color: black;
        font-weight: bold;
        cursor: pointer;
    }
    .tooltip-inner {
        background-color: #333;
        color: #fff;
        border-radius: 4px;
        padding: 5px 10px;
    }
</style>

<div class="shadow-lg col-sm-12">
    <div class="bg-white rounded-4 mt-3">
        <div class="p-5 align-items-center">
            <?php if(isset($view['projeto']) && !empty($view['projeto'])){?>
                <div class="mt-3 mb-3 row justify-content-between">
                    <div class="col-sm-4">
                        <h3><?=$view['projeto']->get('nome')?></h3>
                    </div>
                    <div class="col-sm-4 col-md-2">
                        <div class="form-floating">
                            <select class="form-control" id="movimentacoes" onchange="filtrarMovimentacoes(this.value)">
                                <option value="todas" <?=($request->query('tipo') == 'todas' ? 'selected' : '')?>>Todas</option>
                                <option value="entrada" <?=($request->query('tipo') == 'entrada' ? 'selected' : '')?>>Entrada</option>
                                <option value="saida" <?=($request->query('tipo') == 'saida' ? 'selected' : '')?>>Saída</option>
                            </select>
                            <label>Movimentações</label>
                        </div>
                    </div>
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-sm-4">
                        <div class="card p-3">
                            <span>Cliente</span>
                            <strong class="fs-5"><?=$view['projeto']->getCliente()->get('nomefantasia')?></strong>
                        </div>
                    </div>
                   
                    <div class="col-sm-4">
                        <div class="card p-3">
                            <span>Início</span>
                            <strong class="fs-5"><?=(Utils::dateValid($view['projeto']->get('dt_ini')) ? Utils::dateFormat($view['projeto']->get('dt_ini'), 'd/m/Y') : '')?></strong>
                        </div>
                    </div>
                    
                    <div class="col-sm-4">
                        <div class="card p-3">
                            <span>Previsão</span>
                            <strong class="fs-5"><?=(Utils::dateValid($view['projeto']->get('dt_entrega')) ? Utils::dateFormat($view['projeto']->get('dt_entrega'), 'd/m/Y') : 'Sem previsão')?></strong>
                        </div>
                    </div>

                    <div class="col-sm-4">
                            <div class="card p-2 text-success">
                                <span>Total Recebido</span>
                                <strong id="total_receber">R$ <?=Utils::parseMoney($valor_recebimento)?></strong>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="card p-2 text-danger">
                                <span>Total Pago</span>
                                <strong id="total_pagar">R$ <?=Utils::parseMoney($valor_pagamento)?></strong>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="card p-2">
                                <span>Valor (R$)</span>
                                <strong><?=Utils::parseMoney($view['projeto']->get('valor'))?></strong>
                            </div>
                        </div>
                    </div>

                    <div class="container mt-5">
                        <h3>Utilização do valor inicial</h3>
                        <div class="progress-container">
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: <?=$porcentagem_gasto?>%;" aria-valuenow="<?=Utils::parseMoney(100 - $porcentagem_gasto)?>" aria-valuemin="0" aria-valuemax="100" data-bs-toggle="tooltip" data-bs-placement="top" title="<?=Utils::parseMoney($porcentagem_gasto)?>% - R$ <?=Utils::parseMoney($valor_pagamento)?>"><?=Utils::parseMoney($porcentagem_gasto)?>%</div>
                                <div class="progress-remaining" data-bs-toggle="tooltip" data-bs-placement="top" title="<?=Utils::parseMoney(100 - $porcentagem_gasto)?>% - R$ <?=Utils::parseMoney($view['projeto']->get('valor') - $valor_pagamento)?>"><?=Utils::parseMoney(100 - $porcentagem_gasto)?>%</div>
                            </div>
                            <div class="progress-value">R$ <?=Utils::parseMoney($view['projeto']->get('valor'))?></div>
                        </div>
                    </div>

                    <div class="card mt-2 w-100">
                        <div class="card-header">
                            <strong>Movimentações Financeiras</strong>
                        </div>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="col-sm-2">Data</th>
                                    <th class="col-sm-2">Total (R$)</th>
                                    <th class="col-sm-4">Descrição</th>
                                    <th class="col-sm-3">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!isset($view['movimentacoes']) || count($view['movimentacoes']) == 0){?>
                                    <tr>
                                        <td colspan="4" class="text-center">Nenhuma movimentação encontrada</td>
                                    </tr>
                                    <?php }else{
                                        foreach($view['movimentacoes'] as $movimentacao){?>
                                    <tr>
                                        <td><?=Utils::dateFormat($movimentacao->get('data'), 'd/m/Y')?></td>
                                        <td><?=Utils::parseMoney($movimentacao->get('valor') + $movimentacao->get('juro') + $movimentacao->get('multa') - $movimentacao->get('desconto'))?></td>
                                        <td><span class="<?=$movimentacao->get('tipo') == 1 ? 'text-danger' : 'text-success'?> fw-bold"><?=Movimentacao::$nmTipo[$movimentacao->get('tipo')]?></span> - <?=$movimentacao->get('obs')?></td>
                                        <td><?=Movimentacao::$nmStatus[$movimentacao->get('status')]?></td>
                                    </tr>
                                <?php }
                                }
                                ?>
                            </tbody>
                        </table>
                </div>
            <?php }else{?>
                <div class="d-flex justify-content-center">
                    <h2>Nenhum projeto encontrado</h2>
                </div>
            <?php }?>
        </div>

        
    </div>
</div>


<script>
    
    function filtrarMovimentacoes(valor){
        window.location.href = "?tipo=" + valor;
    }
</script>