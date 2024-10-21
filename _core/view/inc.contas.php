<div class="modal fade" id="filtroModal" tabindex="-1" aria-labelledby="filtroModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title small text-uppercase" id="filtroModalLabel">Filtros</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row g-2 g-lg-3">

            <div class="col-6">
                <div class="form-floating">
                    <input type="date" class="form-control" id="dti_over" value="<?=($view['dti'] != '' ? Utils::dateFormat($view['dti'], 'Y-m-d') : $view['dti'])?>">
                    <label>In&iacute;cio</label>
                </div>
            </div>

            <div class="col-6">
                <div class="form-floating">
                    <input type="date" class="form-control" id="dtf_over" value="<?=($view['dtf'] != '' ? Utils::dateFormat($view['dtf'], 'Y-m-d') : $view['dtf'])?>">
                    <label>Fim</label>
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-floating">
                    <select id="situacoes" class="form-select multisel bg-white" multiple="multiple" data-select="Situa&ccedil;&atilde;o">
                        <?php foreach (Conta::$nmStatus as $key => $value) { ?>
                            <option value="<?= $key ?>" <?= (in_array($key, $view['situacoes']) ? 'selected' : '') ?>><?= $value ?></option>
                        <?php } ?>
                    </select>
                    <label>Status</label>
                </div>
            </div>

            
            <div class="col-md-8">
                <div class="form-floating">
                    <input id="pessoa" type="text" placeholder="Pessoa" class="form-control autocomplete" autocomplete="off" data-table="pessoas" data-name="nomefantasia" data-field="pessoa_id" value="<?= $view['pessoa']->get('nomefantasia') ?>" />
                    <label>Pessoa</label>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-floating">
                    <input id="pessoa_id" name="id_pessoa" type="text" class="form-control" value="<?=$request->query('id_pessoa')?>"  placeholder="C&oacute;digo"/>
                    <label>C&oacute;digo</label>
                </div>
            </div>


            <div class="form-group col-sm-1">
            </div>

            </div>
      </div>
      <div class="modal-footer">
        <button type="button" onclick="doFilter();" class="btn btn-secondary fw-bold text-white">Filtrar</button>
      </div>
    </div>
  </div>
</div>

<div class="contas">

    <div class="d-flex justify-content-between">
        <div>
            <h4>Contas a <?=$tipoConta == 1 ? 'pagar' : 'receber'?></h4>
        </div>

        <div>
            <a type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#filtroModal">
            Filtros
            <i class="ti ti-filter"></i>
            </a>
        </div>
        
    </div>
    <div class="table-responsive px-xs-0">
        
    <?php if (count($view['contas'])) { ?>
            <table class="table table-hover">
                <thead>
                    <tr class="d-none d-lg-table-row">
                        <th></th>
                        <!-- <th>Conta</th> -->
                        <th width="25%">Pessoa</th>
                        <th width="25%">Projeto</th>
                        <th class="text-lg-center">Vencimento</th>
                        <th class="text-end">Valor</th>
                        <th class="d-none d-md-table-cell text-end">Pago</th>
                        <th class="d-none d-md-table-cell text-end">Restante</th>
                        <th class="d-none d-md-table-cell"></th>
                    </tr>
                </thead>
                <tbody>

                        <?php
                        $i = 0;
                        $total = array(0, 0);
                        $totalGeral = array(0, 0);
                        $mes = '00000';
                        foreach ($view['contas'] as $obj) {
                            if (Utils::dateFormat($obj->get('vencimento'), 'mY') != $mes) {
                                $mes = Utils::dateFormat($obj->get('vencimento'), 'mY');
                                if ($total[0] > 0) {
                                    echo '<tr class="mes'.Utils::dateFormat($obj->get('vencimento'), 'm').'">
                                            <td class="d-none d-md-table-cell text-lg-end text-black-50" colspan="7">Subtotal:</td>
                                            <td class="d-md-none text-lg-end text-black-50" colspan="2">Subtotal:</td>
                                            <td class="text-nowrap text-end"><strong>R$ ' . Utils::parseMoney($total[0]) . '</strong></td>
                                            <td class="d-none d-md-table-cell text-nowrap text-end"><strong>R$ ' . Utils::parseMoney($total[1]) . '</strong></td>
                                            <td class="d-none d-md-table-cell text-nowrap text-end"><strong>R$ ' . Utils::parseMoney($total[0] - $total[1]) . '</strong></td>
                                            <td class="d-none d-md-table-cell text-right">&nbsp;</td>
                                        </tr>';
                                }
                                echo '<tr class="nohover" style="box-shadow: -1.5rem 0 0 0 var(--lev-light), 1.5rem 0 0 0 var(--lev-light);">
                                        <td colspan="11" class="text-black-50 small bg-light ps-3 ps-lg-0"><!--<a href="javascript:;" class="text-decoration-none dropdown-toggle" onclick="toggleMes(\'mes'.Utils::dateFormat($obj->get('vencimento'), 'm').'\');">' . Utils::getMonthName(Utils::dateFormat($obj->get('vencimento'), 'm'), 'pt') . '/' . Utils::dateFormat($obj->get('vencimento'), 'Y') . '</a>-->' . Utils::getMonthName(Utils::dateFormat($obj->get('vencimento'), 'm'), 'pt') . '/' . Utils::dateFormat($obj->get('vencimento'), 'Y') . '</td>
                                    </tr>';
                                $total = array(0, 0);
                            }?>
                            <tr class="position-relative mes<?=Utils::dateFormat($obj->get('vencimento'), 'm');?>" id="tr<?= $i ?>r">

                            <td class="fw-bold" onclick="javascript: modalForm('contas',<?= $obj->get('id') ?>)" style="cursor: pointer; text-decoration: underline">#<?=$obj->get('id')?></td>
                            <td class="text-uppercase">
                                <div class="d-none d-md-block"><?= $obj->getProjeto()->getCliente()->get('nomefantasia') ?></div>
                                <div class="text-truncate d-md-none" style="width:100px"><?= $obj->getProjeto()->getCliente()->get('nomefantasia') ?>
                                </div>
                            </td>
                           
                            <td class="text-uppercase">
                                <div class="d-none d-md-block"><?= $obj->getProjeto()->get('nome') ?></div>
                                <div class="text-truncate d-md-none" style="width:100px"><?= $obj->getProjeto()->get('nome') ?>
                                </div>
                            </td>
                           

                            <td class="text-lg-center text-black-50">
                                <div class="d-none d-md-block"><?= Utils::dateFormat($obj->get('vencimento'), 'd/m/Y') ?></div>
                                <div class="d-md-none"><?= Utils::dateFormat($obj->get('vencimento'), 'd/m') ?></div>
                            </td>

                            <td class="text-nowrap text-end">R$ <?= Utils::parseMoney($obj->get('valor')) ?></td>
                            <td class="d-none d-md-table-cell text-nowrap text-end">R$ <?=Utils::parseMoney($obj->getValorPago())?></td>
                            <td class="d-none d-md-table-cell text-nowrap text-end"><strong>R$ <?=Utils::parseMoney($obj->get('valor') - $obj->getValorPago())?></strong></td>
                            
                            
                            <td class="d-none d-md-table-cell position-relative">
                            <?php if($obj->get('valor') - $obj->getValorPago() > 0){?>    
                                <a href="javascript:;" id="btn-liquidar" onclick="javascript: modalForm('movimentacoes','','/id_conta/<?= $obj->get('id') ?>/tipo/<?=$tipoConta?>', function(){ $('#btn-liquidar').fadeOut('slow'); setTimeout(function() {
                                    window.location.reload();
        }, 3000); });" class="text-success" data-bs-toggle="tooltip" data-bs-placement="left" title="Liquidar"><i class="ti ti-cash fs-3"></i></a>
                                <?php }?>
                            </td>
                        </tr>
                        <?php }
                        ?>
                </tbody>
            </table>
            <?php } else { ?>
                <div class="p-3 text-center">Nenhuma conta para <?=$request->get('module') == 'contas-a-pagar' ? 'pagar' : 'receber'?> encontrada</div>
            <?php } ?>
        </div>
    </div>

    <script>

        function toggleMes(className) {
            var mesSelectors = document.querySelectorAll('.' + className);
            mesSelectors.forEach(function(mesSelector) {
                mesSelector.classList.toggle('d-none');
            });
        }

        function doFilter() {
            var url = __PATH__ + '<?= $request->get('module') ?>?';
            var dtAux = '';
            if ($('#dti_over').val() != '') url += `inicio=${$('#dti_over').val()}&`;
            if ($('#dtf_over').val() != '') url += `fim=${$('#dtf_over').val()}&`;
            if ($('#pessoa_id').val() != '') url += `id_pessoa=${$('#pessoa_id').val()}&`;
          
            var selecteds = '';
            $('#situacoes :selected').each(function(i, selected) {
                selecteds += (selecteds === '' ? '' : ',') + $(selected).val();
            });

            if (selecteds !== '') url += `situacoes=${selecteds}&`;

            selecteds = '';

            location = url.substring(0, url.length - 1);
        }

    </script>
    


    <?php
    if ($view['paginas'] > 1) {
        $pagina = $view['pagina'];
        $paginas = $view['paginas'];
        $maxPages = 4;
        $start = max(1, $pagina - intval($maxPages / 2));
        $end = min($paginas, $start + $maxPages - 1);
        $start = max(1, min($start, $paginas - $maxPages + 1));

        if ($pagina > $paginas - intval($maxPages / 2)) $start = max(1, $paginas - $maxPages + 1);

        echo '<nav aria-label="List pagination" class="my-3"><ul class="pagination m-0">';

        $queryes = $_GET;
        if (isset($queryes['pagina'])) unset($queryes['pagina']);

        $url = __PATH__ . $request->get('module') . '?';
        foreach ($queryes as $k => $v) $url .= "{$k}={$v}&";
        $url .= 'pagina=';

        if ($pagina > 1) {
            echo '<li class="page-item"><a class="page-link" href="' . $url . '1"><i class="ti ti-chevron-left-pipe"></i></a></li>';
            echo '<li class="page-item"><a class="page-link" href="' . $url . ($pagina - 1) . '"><i class="ti ti-chevron-left"></i></a></li>';
        }

        for ($i = $start; $i <= $end; $i++)
            echo '<li class="page-item ' . ($i == $pagina ? "active" : '') . '"><a class="page-link" href="' . ($i == $pagina ? 'return false' : $url . $i) . '">' . $i . '</a></li>';

        if ($pagina < $paginas) {
            echo '<li class="page-item"><a class="page-link" href="' . $url . ($pagina + 1) . '"><i class="ti ti-chevron-right"></i></a></li>';
            echo '<li class="page-item"><a href="' . $url . $paginas . '" class="page-link"><i class="ti ti-chevron-right-pipe"></i></a></li>';
        }

        echo '</ul></nav>';
    }
    ?>
</div>