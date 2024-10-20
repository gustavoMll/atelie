<?php 

$view['title'] = $view['title'];
$view['alugueis'] = $view['itens-aluguel'] = array();

if($request->get('action') == 'devolver-aluguel'){
    Utils::ajaxHeader();

    $id = $request->getInt('id');
    if(!Aluguel::exists("id={$id}")){
        Utils::jsonResponse("Aluguel inválido", false);
    }

    $data = $request->get('data');
    if($data == ''){
        Utils::jsonResponse("Data inválida", false);
    }
    $parts = explode('-', $data);

    $obj = Aluguel::load($id);
    $obj->set('dt_entrega', $parts[2].'-'.$parts[1].'-'.$parts[0]);
    $obj->set('status', Aluguel::$status_devolvido);
    $obj->save();

    $rs = ItemAluguel::search([
        's' => 'id',
        'w' => "id_aluguel = {$obj->get('id')}",
    ]);

    while($rs->next()){
        $objIA = ItemAluguel::load($rs->getInt('id'));
        if($objIA->get('tipo_item') == 1){
            $objA = Acessorio::load($objIA->get('id_item'));
            $objA->set('qtd_disp', $objA->get('qtd_disp') + $objIA->get('qtd'));
            $objA->save();
        }
    }

    Utils::jsonResponse("Aluguel devolvido com sucesso", true);

}else{
    $rs = Aluguel::search([
        's' => 'id',
        'w' => "status = 2",
        'o' => 'dt_prazo'
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





