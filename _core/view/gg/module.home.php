<div class="row mt-5">
    <div class="d-flex justify-content-between mb-3">
        <div>
          <h3>Pr&oacute;ximas Coletas</h3>
        </div>
        <div class="d-flex justify-content-between gap-3">
          <a class="btn btn-primary" onclick="modalForm('alugueis', 0, ``, function(){ location.reload(); })"><i class="ti ti-plus"></i> Novo Aluguel</a>
        </div>
    </div>
    <?php if(count($view['alugueis']) > 0){ ?>
    <div class="table-responsive shadow-sm p-3">
        <table class="table table-striped table-bordered m-0">
            <thead class="thead-dark">
                <tr>
                    <th class="col-sm-4 text-center p-3"><i class="ti ti-user"></i>Cliente</th>
                    <th class="col-sm-5 text-center"><i class="ti ti-hanger-2"></i>Itens</th>
                    <th class="col-sm-3 text-center"><i class="ti ti-calendar-event"></i> Data Coleta</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                foreach($view['alugueis'] as $aluguel) {?>
                    <tr class="position-relative">
                        <td class="text-left p-3"><a style="text-decoration: underline; cursor: pointer;" onclick="modalForm('alugueis', <?=$aluguel->get('id')?>, ``, function(){ location.reload(); })"><strong><?=$aluguel->getCliente()->getPessoa()->get('nome')?></strong></a>
                        <?php if($aluguel->getModificacoesPendentes()){?>
                          <span class="badge bg-danger position-absolute start-50 top-0 translate-middle" style="z-index: 10;">Modifica&ccedil;&otilde;es Pendentes</span>
                        <?php }?> 
                      </td>
                        <td><?=$aluguel->getItensAluguel()?></td>
                        <td class="text-center <?=$aluguel->getStatus($aluguel->get('dt_coleta'))?>"><?=Utils::dateValid($aluguel->get('dt_coleta'))? Utils::dateFormat($aluguel->get('dt_coleta'), 'd/m/Y'): '-'?></td>
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
                  <input class="form-control date " id="dt_devolucao" name="dt_devolucao">
                  <label>Data de Devolu&ccedil;&atilde;o</label>
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary fw-bold text-white" data-bs-dismiss="modal" onclick="realizarDevolucao()">Salvar</button>
      </div>
    </div>
  </div>
</div>

<script>
  function realizarDevolucao(){
    let id = $('#id_aluguel').val();
    let data = $('#dt_devolucao').val();
    let partes = data.split("/");
    let dataFormatada = partes[0] + "-" + partes[1] + "-" + partes[2];
    console.log(dataFormatada);
    const url = '<?=__PATH__?>ajax/devolver-aluguel/id/' + id + '/data/' + dataFormatada;
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