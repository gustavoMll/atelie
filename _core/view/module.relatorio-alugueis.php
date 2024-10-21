<div class="row mt-5">
    <div class="row justify-content-between mb-3">
        <div class="col-sm-3">
          <h3>Relat&oacute;rio de Alugueis</h3>
        </div>
        <div class="col-sm-9 row">
         <div class="col-sm-3">
            <div class="form-floating">
              <select class="form-select" name="status" id="status_filter">
                <option value="">Todos</option>
                <?php foreach(Aluguel::$arr_status as $k => $v){?>
                  <option value="<?=$k?>" <?=$request->query('status') == $k ? 'selected': ''?>><?=$v?></option>
                <?php } ?>
              </select>
              <label>Status</label>
            </div>
          </div>

          <div class="col-sm-3">
              <div class="form-floating">
                <input type="text" class="form-control date" name="dt_ini" id="dt_ini_filter" value="<?=$request->query('dt_ini') != '' ? $request->query('dt_ini') : $dt_ini?>">
                <label>Data Início</label>
              </div>
          </div>
         
          <div class="col-sm-3">
              <div class="form-floating">
                <input type="text" class="form-control date" name="dt_fim" id="dt_fim_filter" value="<?=$request->query('dt_fim') != '' ? $request->query('dt_fim') : $dt_fim?>">
                <label>Data Fim</label>
              </div>
          </div>
          
          <div class="col-sm-3 d-flex align-items-center">
              <div class="form-floating">
               <a class="btn btn-primary" onclick="buscar()"><i class="ti ti-search"></i>Buscar</a>
              </div>
          </div>
        </div>
    </div>
    <?php if(count($view['alugueis']) > 0){ ?>
    <table class="table table-striped">
        <thead class="thead-dark">
            <tr>
                <th class="p-3">Cliente</th>
                <th class="text-center">Data de Aluguel</th>
                <th class="text-center">Data de Devolução</th>
                <th class="text-center">Valor (R$)</th>
            </tr>
        </thead>
        <tbody>
          <?php foreach($view['alugueis'] as $obj){?>
            <tr>
                <td class="p-3 fw-bold"><a style="text-decoration: none; cursor: pointer;" onclick="modalForm('alugueis', <?=$obj->get('id')?>, ``, function(){ location.reload(); })"><strong><?=$obj->getCliente()->getPessoa()->get('nome')?></strong></a></td>
                <td class="text-center"><?=Utils::dateFormat($obj->get('dt_cad'), 'd/m/Y')?></td>
                <td class="text-center"><?=(Utils::dateValid($obj->get('dt_entrega')) ? Utils::dateFormat($obj->get('dt_entrega'), 'd/m/Y') : ' - ')?></td>
                <td class="text-center"><?=Utils::parseMoney($obj->get('valor_aluguel'))?></td>
            </tr>
            <?php }?>
        </tbody>
      </table>

      <div class="row mt-4">
          <div class="col-md-6">
              <h5 class="text-secondary">Total de Aluguéis: <span class="fw-bold"><?=count($view['alugueis'])?></span></h5>
          </div>
          <div class="col-md-6 text-end">
              <h5 class="text-secondary">Valor: <span class="fw-bold">R$ <?=Utils::parseMoney($valor_total)?></span></h5>
          </div>
      </div>
    <?php }else{?>
      <div class="alert alert-primary" role="alert">
        Nenhum aluguel encontrado
      </div>
    <?php }?>
</div>
    
<script>
  function buscar(){
    const status = $('#status_filter').val();
    let dt_ini = $('#dt_ini_filter').val();
    let dt_fim = $('#dt_fim_filter').val();

    let url = '<?=$request->get('module')?>?';
 
    if (status) {
        url += `status=${encodeURIComponent(status)}&`;
    }
    if (dt_ini) {
        dt_ini = dt_ini.replace(/\//g, '-');
        url += `dt_ini=${encodeURIComponent(dt_ini)}&`;
    }
    if (dt_fim) {
        dt_fim = dt_fim.replace(/\//g, '-');
        url += `dt_fim=${encodeURIComponent(dt_fim)}&`;
    }

    url = url.slice(-1) === '&' ? url.slice(0, -1) : url;

    window.location.href = url;
  }


</script>