<div class="row mt-5">
    <div class="d-flex justify-content-between mb-3">
        <div>
          <h3>Pr&oacute;ximas Coletas</h3>
        </div>
        <div class="d-flex justify-content-between gap-3">
          <a class="btn btn-primary" onclick="modalForm('alugueis', 0, ``, () => location.reload())"><i class="ti ti-plus"></i> Novo Aluguel</a>
        </div>
    </div>
    <?php if(count($view['alugueis']) > 0){ ?>
    <div class="table-responsive shadow-sm p-3">
        <table class="table table-striped table-bordered m-0">
            <thead class="thead-dark">
                <tr>
                    <th class="col-sm-4 text-center p-3"><i class="ti ti-user"></i>Cliente</th>
                    <th class="col-sm-4 text-center"><i class="ti ti-hanger-2"></i>Itens</th>
                    <th class="col-sm-3 text-center"><i class="ti ti-calendar-event"></i> Data Coleta</th>
                    <th class="col-sm-3 text-center"><i class="ti ti-calendar-month"></i>Coleta</th>
                    <th class="px-3"></th>
                </tr>
            </thead>
            <tbody>
                <?php 
                foreach($view['alugueis'] as $aluguel) {?>
                    <tr class="position-relative">
                        <td class="text-left p-3"><a style="text-decoration: underline; cursor: pointer;" onclick="modalForm('alugueis', <?=$aluguel->get('id')?>, ``,  () => location.reload())"><strong><?=$aluguel->getCliente()->getPessoa()->get('nome')?></strong></a>
                        <?php if($aluguel->getModificacoesPendentes()){?>
                          <span class="badge bg-danger position-absolute start-50 top-0 translate-middle" style="z-index: 10;">Modifica&ccedil;&otilde;es Pendentes</span>
                        <?php }elseif($aluguel->modificadoUltimoAluguel()){?>
                          <span class="badge bg-primary position-absolute start-50 top-0 translate-middle" style="z-index: 10;">Modificado em aluguel anterior</span>
                        <?php } ?>
                      </td>
                      <td><?=$aluguel->getItensAluguelString()?></td>
                      <td class="text-center <?=$aluguel->getStatus($aluguel->get('dt_coleta'))?>"><?=Utils::dateValid($aluguel->get('dt_coleta'))? Utils::dateFormat($aluguel->get('dt_coleta'), 'd/m/Y'): '-'?></td>
                      <td class="text-center"><a class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalDevolucao" onclick="setarCamposModal(`<?=$aluguel->get('id')?>`, `<?=Utils::parseMoney($aluguel->get('valor_aluguel'))?>`, `<?=Utils::parseMoney($aluguel->get('valor_entrada'))?>`, `<?=Utils::parseMoney($aluguel->get('valor_restante'))?>`)"><i class="ti ti-calendar-plus"></i></a></td>
                      <th class="px-3"><a onclick="montarPdf('<?=$request->get('module')?>', <?=$aluguel->get('id')?>)"  target="_blank" class="btn btn-sm border-transparent opacity-50" title="Imprimir Termos"><i class="ti ti-printer"></i></a></th>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php }else{?>
      <div class="alert alert-primary" role="alert">
        Nenhuma coleta encontrada
      </div>
    <?php }?>
</div>
    

<div class="modal fade modal-lg" id="modalDevolucao" tabindex="-1" aria-labelledby="modalDevolucaoLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="modalDevolucaoLabel">Coleta</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="id_aluguel" name="id_aluguel" value="">
        <div class="row">
            <div class="col-sm-4 mb-3">
                <div class="form-floating">
                  <input type="text" class="form-control money" name="valor_aluguel" id="valor_aluguel" value="" disabled>
                  <label>Valor Total</label>
                </div>
            </div>
            <div class="col-sm-4 mb-3">
                <div class="form-floating">
                  <input type="text" class="form-control money" name="valor_entrada" id="valor_entrada" value="" disabled>
                  <label>Valor Entrda</label>
                </div>
            </div>
            <div class="col-sm-4 mb-3">
                <div class="form-floating">
                  <input type="text" class="form-control money" name="valor_restante" id="valor_restante" disabled value="">
                  <label>Valor Restante</label>
                </div>
            </div>
            
            <div class="col-sm-6 mb-3">
                <div class="form-floating">
                  <input type="text" class="form-control money" name="valor_pago" id="valor_pago" value="">
                  <label>Valor</label>
                </div>
            </div>
            <div class="col-sm-6 mb-3">
                <div class="form-floating">
                  <input class="form-control date " id="dt_coleta_pc" name="dt_coleta_pc">
                  <label>Data de Coleta</label>
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary fw-bold text-white" data-bs-dismiss="modal" onclick="realizarColeta()">Salvar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade modal-sm" id="modalDevolucao" tabindex="-1" aria-labelledby="modalDevolucaoLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="modalDevolucaoLabel">Devolu&ccedil;&atilde;o</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
            <div class="col-sm-12">
                <div class="form-floating">
                  <input type="hidden" id="id_aluguel" name="id_aluguel" value="">
                  <input class="form-control date " id="dt_coleta_pc" name="dt_coleta_pc">
                  <label>Data de Devolu&ccedil;&atilde;o</label>
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary fw-bold text-white" data-bs-dismiss="modal" onclick="realizarColeta()">Salvar</button>
      </div>
    </div>
  </div>
</div>

<script>
  function setarCamposModal(id_aluguel, valor_aluguel, valor_entrada, valor_restante){
    console.log(valor_aluguel);
    console.log(valor_entrada);
    console.log(valor_restante);
    $(`#id_aluguel`).val(id_aluguel);
    $(`#valor_aluguel`).val(valor_aluguel);
    $(`#valor_entrada`).val(valor_entrada);
    $(`#valor_restante`).val(valor_restante);
  }

  function realizarColeta(){
    let id = $('#id_aluguel').val();
    let data = $('#dt_coleta_pc').val();
    let valor = $('#valor_pago').val();
    let partes = data.split("/");

    let dataFormatada = partes[0] + "-" + partes[1] + "-" + partes[2];
    console.log(dataFormatada + ' # ' + valor);
    const url = '<?=__PATH__.$request->get('module')?>/coletar-aluguel/id/' + id + '/data/' + dataFormatada + '/valor/' + valor;
    $.ajax({
        url: url,
        dataType: `json`,
        type: 'GET',

        success: function(resp) {
            if(resp.success) {
                MessageBox.success(resp.message);
                setTimeout(function() {
                  window.location.reload();
                }, 3000);
            }else{
                MessageBox.error(resp.message);
            }
        },
        error: function(xhr, status, error) {
            MessageBox.error('Ocorreu um erro. Para detalhes pressione F12 e verifique no console.');
            console.error(xhr.responseText);
        }
    })
  }

  
</script>