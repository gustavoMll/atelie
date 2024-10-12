<?php 

$view['title'] = $view['title'];
$view['alugueis'] = $view['itens-aluguel'] = array();

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





