<?php 

$view['title'] = $view['title'];
$view['alugueis'] = $view['itens-aluguel'] = array();
if($request->get('action') == 'coletar-aluguel'){
    Utils::ajaxHeader();
    
    $id = $request->getInt('id');
    if(!Aluguel::exists("id = {$id}")){
        Utils::jsonResponse("Aluguel inválido", false);
    }

    $data = $request->get('data');
    if($data == ''){
        Utils::jsonResponse("Data não informada", false);
    }
    $parts = explode('-', $data);

    $obj = Aluguel::load($id);
    $valor = $request->get('valor');
    if($valor > $obj->get('valor_restante')){
        Utils::jsonResponse("Valor inválido", false);
    }     

    $obj->set('valor_restante', (float)$obj->get('valor_restante') - (float)$valor);
    $obj->set('dt_coleta', $parts[2] . '-' .$parts[1] . '-'. $parts[0]);
    $obj->set('status', Aluguel::$status_coletado);
    $obj->save();

    Utils::jsonResponse("Aluguel coletado com sucesso", true);
    
}elseif($request->get('action') == 'montar-pdf'){
    Utils::ajaxHeader();
    $id_aluguel = $request->getInt('id');
    if (Aluguel::exists("id={$id_aluguel}")) {
        $aluguel = Aluguel::load($id_aluguel);
        $pdfPath = $aluguel->montarContrato();
        if (file_exists($pdfPath)) {
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="contrato_'.$aluguel->get('id').'.pdf"');
            readfile($pdfPath);
            exit;
        } else {
            Utils::jsonResponse(false, "Erro ao gerar o PDF.");
        }
    } 
    Utils::jsonResponse(false, "Aluguel não encontrado.");
    
}elseif($request->get('action') == ''){
    $rs = Aluguel::search([
        's' => 'id',
        'w' => "status = 1",
        'o' => 'dt_coleta'
    ]);

    while($rs->next()){
        $obj = Aluguel::load($rs->getInt('id'));
        $view['alugueis'][] = $obj;

        $rs2 = ItemAluguel::search([
            's' => 'id',
            'w' => 'id_aluguel='.$obj->get('id')
        ]);

        while($rs2->next()){
            $view['itens-aluguel'][$obj->get('id')][] = ItemAluguel::load($rs->getInt('id'));
        }
    }
}






