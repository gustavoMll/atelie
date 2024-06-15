<?php

$view['title'] = 'Projetos';

$action = $request->get('action');

$view['projeto'] = null;

$contas_pagar = array();
$contas_receber = array();
$movimentacoes = array();
$total_entrada = 0;
$total_saida = 0;
$total_pagar = 0;
$total_receber = 0;
$saldo = 0;


if($action == 'load-contas'){
    Utils::ajaxHeader();

    $id_projeto = $request->getInt('id_projeto');
    $tipo = $request->get('tipo');
    
   
    $where = '(1, 2)';
    
    if($tipo == 'todas'){
        $where = '(1, 2, 3, 4)';
    }

    $valor_total = 1;
    $rs = Conta::search([
        's' => 'id, tipo, status',
        'w' => "id_projeto = {$id_projeto} AND status IN {$where}",
        'o' => 'vencimento',
    ]);
    
    $dar_baixa_pagar = 0;
    $dar_baixa_receber = 0;
    // echo "id_projeto = {$id_projeto} AND status IN {$where}"; exit;
    while($rs->next()){
        if($rs->getInt('tipo') == 1){
            if(in_array($rs->getInt('status'), [1,2])){
                $dar_baixa_pagar = 1;
            }
            $contas_pagar[] = Conta::load($rs->getInt('id'));
        }elseif($rs->getInt('tipo') == 2){
            if(in_array($rs->getInt('status'), [1,2])){
                $dar_baixa_receber = 1;
            }
            $contas_receber[] = Conta::load($rs->getInt('id'));
        }
    }

    $html = '';

    $data_atual = date('');

    $html .= '
            <div class="col-xl-6 mb-3">
                <div class="card mx-2">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <strong>Contas a Pagar</strong>
                        </div>
                        <div class="d-flex gap-2">';
                        if(count($contas_pagar) && $dar_baixa_pagar){
                            $html .= '
                            <a class="d-flex align-items-center btn btn-light text-black-75 border-dark border-opacity-10 p-2 px-lg-3" href="javascript:;" onclick="baixarContas()" data-bs-toggle="modal" data-bs-target="#baixaModal" data-toggle="tooltip" data-bs-placement="top" title="Dar baixa em todas as contas" ><i class="ti fs-5 ti-copy-check"></i></a>';
                        }
                        $html .= '
                            <button type="button" class="d-flex gap-2 align-items-center btn btn-primary text-black-75 border-dark border-opacity-10 p-2 px-lg-3" onclick="modalForm(`contas`,`0`,`/id_projeto/'.$id_projeto.'/tipo/1`, loadContas)" data-toggle="tooltip" data-bs-placement="top" title="Nova conta"><i class="ti ti-plus"></i></button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="col-sm-3">Destino</th>
                                    <th class="col-sm-3">Valor</th>
                                    <th class="col-sm-3">Status</th>
                                    <th class="col-sm-3">Vencimento</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>';
                            if(!count($contas_pagar)){
                                $html.= '
                                <tr>
                                    <td colspan="8" class="text-center">Nenhuma conta encontrada</td>
                                </tr>';
                            }else{
                                foreach($contas_pagar as $conta_p){
                                    $total_pagar += $conta_p->get('valor');

                                    $html .= '<input type="hidden" id="conta_'.$conta_p->get('id').'" '.(in_array($conta_p->get('status'), [1, 2]) ? 'name="contas[]"' : '' ).' value="'.$conta_p->get('id').'">';
                                    $html .='
                                    <tr id="tr-'.$conta_p->getTableName().$conta_p->get('id').'">
                                    <td>'.$conta_p->getCategoria()->get('nome').'</td>
                                    <td>'.Utils::parseMoney($conta_p->get('valor')).'</td>
                                    <td>'.Conta::$nmStatus[$conta_p->get('status')].'</td>
                                    <td>
                                        <span class="d-flex align-items-center">'.Utils::dateFormat($conta_p->get('vencimento'), 'd/m/Y').'
                                        </span>
                                    </td>
                                    
                                    <td>
                                        <a class="d-flex gap-2 align-items-center btn btn-light text-black-75 border-dark border-opacity-10 p-2 px-lg-3" data-toggle="tooltip" data-bs-placement="top" title="Ver conta" onclick="modalForm(`contas`,`'.($conta_p->get('id')).'`,``, loadContas)" style="cursor: pointer !important; text-decoration: none"><i class="ti ti-eye"></i></a>
                                    </td>
                                    <td>
                                        <a class="d-flex gap-2 align-items-center btn btn-light text-black-75 border-dark border-opacity-10 p-2 px-lg-3" onclick="removeObj(`Conta`, `'.$conta_p->get('id').'`, `div_contas`, `loadContas`)" data-toggle="tooltip" data-bs-placement="top" title="Remover conta"style="cursor: pointer !important; text-decoration: none"><i class="ti ti-trash text-danger" ></i></a>
                                    </td>
                                    <td>';
                                    if(in_array($conta_p->get('status'), [1, 2])){
                                        $html .='
                                            <a class="d-flex gap-2 align-items-center btn btn-light text-black-75 border-dark border-opacity-10 p-2 px-lg-3" href="javascript:;" onclick="baixarContas(`'.$conta_p->get('id').'`,`'.$conta_p->get('tipo').'`)" data-bs-toggle="modal" data-bs-target="#baixaModal" data-toggle="tooltip" data-bs-placement="top" title="Dar baixa" ><i class="ti fs-5 ti-copy-check"></i></a>
                                        </td>';
                                    }
                                    $html .='
                                    </tr>';
                                }
                            }   
                        $html .= '
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-6 mb-3">
                <div class="card mx-2">
                    <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <strong>Contas a Receber</strong>
                    </div>
                    <div class="d-flex gap-2">';
                        if(count($contas_receber) && $dar_baixa_receber){
                            $html .= '
                            <a class="d-flex gap-2 align-items-center btn btn-light text-black-75 border-dark border-opacity-10 p-2 px-lg-3" href="javascript:;" onclick="baixarContas()" data-bs-toggle="modal" data-bs-target="#baixaModal" data-toggle="tooltip" data-bs-placement="top" title="Dar baixa em todas as contas" ><i class="ti fs-5 ti-copy-check"></i></a>';
                        }
                        $html .='
                        <button type="button" onclick="modalForm(`contas`,`0`,`/id_projeto/'.$id_projeto.'/tipo/2`, loadContas)" data-toggle="tooltip" data-bs-placement="top" title="Nova conta"  class="d-flex align-items-center btn btn-primary text-black-75 border-dark border-opacity-10 p-2 px-lg-3"><i class="ti ti-plus"></i></button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="col-sm-3">Origem</th>
                                    <th class="col-sm-3">Valor</th>
                                    <th class="col-sm-3">Status</th>
                                    <th class="col-sm-3">Vencimento</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>';
                        if(!count($contas_receber)){
                            $html .= '
                            <tr>
                                <td colspan="7" class="text-center">Nenhuma conta encontrada</td>
                            </tr>';
                        }else{
                        foreach($contas_receber as $conta_r){
                            $total_receber += $conta_r->get('valor');
                            $html .= '<input type="hidden" id="conta_'.$conta_r->get('id').'" '.(in_array($conta_r->get('status'), [1, 2]) ? 'name="contas[]"' : '' ).' value="'.$conta_r->get('id').'">';
                            $html .='
                        <tr id="tr-'.$conta_r->getTableName().$conta_r->get('id').'">
                            <td>'.$conta_r->getCategoria()->get('nome').'</td>
                            <td>'.Utils::parseMoney($conta_r->get('valor')).'</td>
                            <td>'.Conta::$nmStatus[$conta_r->get('status')].'</td>
                            <td>
                                <span class="d-flex align-items-center">'.Utils::dateFormat($conta_r->get('vencimento'), 'd/m/Y').'
                                </span>
                            </td>
                            <td><a class="d-flex gap-2 align-items-center btn btn-light text-black-75 border-dark border-opacity-10 p-2 px-lg-3" data-toggle="tooltip" data-bs-placement="top" title="Ver conta" onclick="modalForm(`contas`,`'.($conta_r->get('id')).'`,``, loadContas)" style="cursor: pointer !important; text-decoration: none"><i class="ti ti-eye"></i></a></td>
                            <td>
                                <a class="d-flex gap-2 align-items-center btn btn-light text-black-75 border-dark border-opacity-10 p-2 px-lg-3" onclick="removeObj(`Conta`, `'.$conta_r->get('id').'`, `div_contas`, `loadContas`)" data-toggle="tooltip" data-bs-placement="top" title="Remover conta"style="cursor: pointer !important; text-decoration: none"><i class="ti ti-trash text-danger" ></i></a>
                            </td>
                            <td>';
                            if(in_array($conta_r->get('status'), [1, 2])){
                            $html .= '
                                <a class="d-flex gap-2 align-items-center btn btn-light text-black-75 border-dark border-opacity-10 p-2 px-lg-3" href="javascript:;" onclick="baixarContas(`'.$conta_r->get('id').'`,`'.$conta_r->get('tipo').'`)" data-bs-toggle="modal" data-bs-target="#baixaModal" data-toggle="tooltip" data-bs-placement="top" title="Dar baixa" ><i class="ti fs-5 ti-copy-check"></i></a>';
                            }
                                $html.='
                            </td>
                        </tr>';
                        } 
                        }
                        $html .='
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>';

        $sql = 
        "SELECT id_categoria, COUNT(id) AS qtd_contas, SUM(valor) AS total_gasto 
        FROM contas 
        WHERE id_projeto = {$id_projeto} AND status IN {$where} 
        GROUP BY id_categoria 
        ORDER BY id_categoria";

      
        $objCategoria = null;
        $categorias_pagar = array();
        $categorias_receber = array();
        $rs2 = $conn->prepareStatement($sql)->executeReader();

        while($rs2->next()){
            $objCategoria = Categoria::load($rs2->getInt('id_categoria'));
            if($objCategoria->get('tipo') == 1){
                $categorias_pagar[] = [
                    'nome' => $objCategoria->get('nome'),
                    'contas' => $rs2->getInt('qtd_contas'),
                    'valor' =>  $rs2->getInt('total_gasto')
                ]; 
            }elseif($objCategoria->get('tipo') == 2){
                $categorias_receber[] = [
                    'nome' => $objCategoria->get('nome'),
                    'contas' => $rs2->getInt('qtd_contas'),
                    'valor' =>  $rs2->getInt('total_gasto')
                ]; 
            }
        }

        $html .= '
            <div class="row">
                <div class="col-xl-6">
                    <div class="card mx-3">
                        <div class="card-header d-flex justify-content-center">
                            <span><strong>Contas a pagar por categoria</strong></span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <td><strong>Conta</strong></td>
                                    <td class="text-center"><strong>Quantidade</strong></td>
                                    <td class="text-center"><strong>Valor (R$)</strong></td>
                                </tr>
                            </thead>
                            <tbody>
            ';
            if(!count($categorias_pagar)){
                $html .= '
                <tr>
                    <td colspan="6" class="text-center">Nenhuma conta encontrada</td>
                </tr>
                ';
            }else{
        
                foreach($categorias_pagar as $k => $v){
                    $html .= '
                            <tr>
                            <td>'.$v['nome'].'</td>
                            <td class="text-center">'.$v['contas'].'</td>
                            <td class="text-center">'.Utils::parseMoney($v['valor']).'</td>
                            </tr>
                    ';
                }
            }
        $html .= '
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>';
        
        $html .= '
            <div class="col-xl-6">
                <div class="card mx-3">
                    <div class="card-header d-flex justify-content-center">
                        <span><strong>Contas a receber por categoria</strong></span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <td><strong>Conta</strong></td>
                                <td class="text-center"><strong>Quantidade</strong></td>
                                <td class="text-center"><strong>Valor (R$)</strong></td>
                            </tr>
                        </thead>
                        <tbody>
            ';
                    if(!count($categorias_receber)){
                        $html .= '
                        <tr>
                            <td colspan="6" class="text-center">Nenhuma conta encontrada</td>
                        </tr>
                        ';
                    }else{
                        foreach($categorias_receber as $k => $v){
                            $html .= '
                                    <tr>
                                    <td>'.$v['nome'].'</td>
                                    <td class="text-center">'.$v['contas'].'</td>
                                    <td class="text-center">'.Utils::parseMoney($v['valor']).'</td>
                                    </tr>
                            ';
                        }
                    }
            $html .= '
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>';
    $objProjeto = Projeto::load($id_projeto);
    Utils::jsonResponse("", true, ['html' => $html, 'total_pagar' => Utils::parseMoney($total_pagar), 'total_receber' => Utils::parseMoney($total_receber), 'saldo' => Utils::parseMoney($objProjeto->get('valor') - $total_pagar)]);

}elseif($action == 'load-movimentacoes'){
    Utils::ajaxHeader();
    
    $id_projeto = $request->getInt('id_projeto');
    $tipo = $request->getInt('tipo');

    $where = $tipo != 0 ? " AND tipo = {$tipo}" : "";
    
    $rs = Movimentacao::search([
        's' => 'id, tipo',
        'w' => "(id_projeto = {$id_projeto} OR  id_conta IN (SELECT id FROM contas WHERE id_projeto = {$id_projeto})) {$where} ",
        'o' => 'data',
    ]);

    while($rs->next()){
        $movimentacoes[] = Movimentacao::load($rs->getInt('id'));
    }
    $html = '';

    $html .='
        <div class="card w-100 mx-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Movimentações</strong>
                <button type="button" onclick="modalForm(`movimentacoes`,`0`,`/id_projeto/'.$id_projeto.''.($tipo != 0 ? "/tipo/".$tipo :"").'`, (resp)=>{ loadMovimentacoes('.$tipo.'); })" data-toggle="tooltip" data-bs-placement="top" title="Nova movimentação" class="btn btn-sm btn-primary ms-auto"><i class="ti ti-plus"></i></button>
            </div>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="col-sm-2">Data</th>
                            <th class="col-sm-2">Total (R$)</th>
                            <th class="col-sm-4">Descrição</th>
                            <th class="col-sm-3">Status</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>';

                    if(!count($movimentacoes)){
                        $html .= '
                            <tr>
                                <td colspan="6" class="text-center">Nenhuma movimentação encontrada</td>
                            </tr>';
                    }else{
                        foreach($movimentacoes as $movimentacao){
                            $valor_total = $movimentacao->get('valor') + $movimentacao->get('juro')  + $movimentacao->get('multa') - $movimentacao->get('desconto');

                            if($movimentacao->get('tipo') == 1){
                                $total_saida += $valor_total;
                            }elseif($movimentacao->get('tipo') == 2){
                                $total_entrada += $valor_total;
                            }

                            $html .='
                            <tr id="tr-'.$movimentacao->getTableName().$movimentacao->get('id').'">
                                <td>'.Utils::dateFormat($movimentacao->get('data'), 'd/m/Y').'</td>
                                <td>'.Utils::parseMoney($valor_total).'</td>
                                <td><span class="'.($movimentacao->get('tipo') == 1 ? 'text-danger' : 'text-success').' fw-bold">'.Movimentacao::$nmTipo[$movimentacao->get('tipo')].'</span> - '.Utils::subText($movimentacao->get('obs'), 200).'</td>
                                <td>'.Movimentacao::$nmStatus[$movimentacao->get('status')].'</td>
                                <td>
                                    <a class="btn btn-sm" data-toggle="tooltip" data-bs-placement="top" title="Ver conta" onclick="modalForm(`movimentacoes`,`'.($movimentacao->get('id')).'`,``, (loadMovimentacoes))" style="cursor: pointer !important; text-decoration: none"><i class="ti ti-eye"></i></a>
                                </td>
                                <td>
                                    <a onclick="removeObj(`Movimentacao`, `'.$movimentacao->get('id').'`, `div_movimentacoes`, `loadMovimentacoes`)" data-toggle="tooltip" data-bs-placement="top" class="pt-2" title="Remover movimentação" style="cursor: pointer !important; text-decoration: none"><i class="ti ti-trash text-danger" ></i></a>
                                </td>
                            </tr>';
                            }
                        }
                        $html .='
                    </tbody>
                    </table>
                </div>
            </div>
        </div>';

    $objProjeto = Projeto::load($id_projeto);
    Utils::jsonResponse("", true, ['html' => $html, 'total_entrada' => $total_entrada, 'total_saida' => $total_saida, 'saldo' => Utils::parseMoney($objProjeto->get('valor') - $total_saida)]);

}elseif($action == 'remover'){

    Utils::ajaxHeader();

    $classe = $request->get('classe');

    $id = $request->get('id');

    if(!$classe::exists("id={$id}")){
        Utils::jsonResponse("Objeto não encontrado", false);
    }

    $ret = '';
    $obj = new $classe;
    $ret = 'tr-'.$obj->getTableName().$id;

    Flex::dbDelete($classe::load($id), "id = {$id}");

    Utils::jsonResponse("{$classe} removido(a) com sucesso", true, ['ret' => $ret]);
    
}elseif($action != ''){
    if(Projeto::exists('id='.(int)$action)){
        $view['projeto'] = Projeto::load((int)$action);
        $saldo = $view['projeto']->get('valor');
    }
    
}

$view['end_scripts'] .= ' loadContas();';
$view['end_scripts'] .= ' loadMovimentacoes();';
$view['end_scripts'] .= ' loadHistorico();';