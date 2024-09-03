<div class="row mt-5">
    <div class="d-flex justify-content-between mb-3">
        <div>
          <h3>Pr&oacute;ximos Alugueis</h3>
        </div>
        <div class="d-flex justify-content-between gap-3">
          <a class="btn btn-primary" onclick="modalForm('pedidos', 0)"><i class="ti ti-plus"></i> Novo Aluguel</a>
          <a class="btn btn-light border-dark" data-bs-toggle="modal" data-bs-target="#exampleModal" onclick="tableList(`alugueis`, `offset=10`, `txtalugueis`, false);"><i class="ti ti-clipboard-list fs-4"></i>Ver Todos</a>
        </div>
    </div>
    <?php if(count($view['alugueis']) > 0){ ?>
    <div class="table-responsive shadow-sm p-3">
        <table class="table table-striped table-bordered m-0">
            <thead class="thead-dark">
                <tr>
                    <th class="col-sm-3 text-center p-3"><i class="ti ti-user"></i>Cliente</th>
                    <th class="col-sm-5 text-center"><i class="ti ti-hanger-2"></i>Itens</th>
                    <th class="col-sm-2 text-center"><i class="ti ti-calendar-event"></i> Data Previs&atilde;o</th>
                    <th class="col-sm-2 text-center"><i class="ti ti-calendar-event"></i> Data Devolu&ccedil;&atilde;o</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                foreach($view['alugueis'] as $aluguel) {?>
                    <tr>
                        <td class="text-left p-3"><a style="text-decoration: underline; cursor: pointer;" onclick="modalForm('clientes', <?=$aluguel->getPedido()->getCliente()->get('id')?>)"><?=$aluguel->getPedido()->getCliente()->getPessoa()->get('nome')?></a></td>
                        <td><?=$aluguel->getItensAluguel()?></td>
                        <td class="text-center  <?=$aluguel->getStatus()?>"><?=Utils::dateValid($aluguel->get('dt_prazo'))? Utils::dateFormat($aluguel->get('dt_prazo'), 'd/m/Y'): '-'?></td>
                        <td class="text-center">
                            <?=Utils::dateValid($aluguel->get('dt_entrega')) ?
                            '<a style="cursor: pointer" onclick="modalForm(`alugueis`, '.$aluguel->get('id').', ``, function(){setTimeout(function() {
                                    window.location.reload();
                            }, 1000); });">'.Utils::dateFormat($aluguel->get('dt_entrega'),'d/m/Y').' <i class="ti ti-pencil"></i></a>'
                            :
                            '<a class="btn btn-sm btn-primary" onclick="modalForm(`alugueis`, '.$aluguel->get('id').', ``, function(){setTimeout(function() {
                                    window.location.reload();
                            }, 1000); });">
                            <i class="ti ti-plus"></i>
                            </a>'; 
                            ?>
                           
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php }?>
</div>
    

<div class="modal fade modal-xl" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Alugueis</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="txtalugueis">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary fw-bold text-white" data-bs-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>