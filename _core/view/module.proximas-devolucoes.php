<div class="row mt-5">
    <div class="d-flex justify-content-between mb-3">
        <div>
          <h3>Pr&oacute;ximas Devolu&ccedil;&otilde;es</h3>
        </div>
        <div class="d-flex justify-content-between gap-3">
          <a class="btn btn-primary" onclick="modalForm('alugueis', 0)"><i class="ti ti-plus"></i> Novo Aluguel</a>
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
                    <th class="col-sm-2 text-center"><i class="ti ti-pencil"></i>Devolução</th>
                    <th class="px-3"></th>
                </tr>
            </thead>
            <tbody>
                <?php 
                foreach($view['alugueis'] as $aluguel) {?>
                    <tr>
                        <td class="text-left p-3"><a style="text-decoration: underline; cursor: pointer;" onclick="modalForm('alugueis', <?=$aluguel->get('id')?>, ``, function(){ location.reload(); })"><strong><?=$aluguel->getCliente()->getPessoa()->get('nome')?></strong></a></td>
                        <td><?=$aluguel->getItensAluguelString()?></td>
                        <td class="text-center <?=$aluguel->getStatus($aluguel->get('dt_prazo'))?>"><?=Utils::dateFormat($aluguel->get('dt_prazo'), 'd/m/Y')?></td>
                        <td class="text-center"><a class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalDevolucao" onclick="$(`#id_aluguel`).val(<?=$aluguel->get('id')?>);"><i class="ti ti-arrow-back-up"></i></a></td>
                        <th class="px-3"><a onclick="montarPdf('proximas-coletas', <?=$aluguel->get('id')?>)"  target="_blank" class="btn btn-sm border-transparent opacity-50" title="Imprimir Termos"><i class="ti ti-printer"></i></a></th>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php }else{?>
      <div class="alert alert-primary" role="alert">
        Nenhuma devolução encontrada
      </div>
    <?php }?>
</div>

<div class="modal fade modal-md" id="modalDevolucao" tabindex="-1" aria-labelledby="modalDevolucaoLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="modalDevolucaoLabel">Devolu&ccedil;&atilde;o</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="id_aluguel" name="id_aluguel" value="">
        <div class="row">
            <div class="col-sm-12 mb-3">
                <div class="form-floating">
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
    const url = '<?=__PATH__.$request->get('module')?>/devolver-aluguel/id/' + id + '/data/' + dataFormatada;
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