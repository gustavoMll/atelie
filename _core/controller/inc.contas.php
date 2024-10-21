<?php

$paramAdd = 'tipo='.$tipoConta;

$dti = $request->query('inicio');
$dtf = $request->query('fim');

$view['contas'] = array();

$view['situacoes'] = array(1,2);
if($request->query('situacoes') !='' ){
	$view['situacoes'] = explode(',',Utils::Replace('/[^0-9,]/','',$request->query('situacoes')));
}

$paramAdd .= " AND status IN (";

$i = 1;
foreach($view['situacoes'] as $k => $v){
	$paramAdd .= $v;
	if($i < count($view['situacoes'])){
		$paramAdd .= ', ';
	}
	$i++;
}

$paramAdd .= ')';

$view['dti'] = $dti;
$view['dtf'] = $dtf;

if(Utils::dateValid($dti)) {
    $dti = Utils::dateFormat($dti,'Y-m-d');
	$paramAdd .= " and vencimento >= '{$dti}'";
	$view['dti'] = Utils::dateFormat($dti,'d/m/Y');
}
if(Utils::dateValid($dtf)) {
    $dtf = Utils::dateFormat($dtf,'Y-m-d');
	$paramAdd .= " and vencimento <= '{$dtf}'";
	$view['dtf'] = Utils::dateFormat($dtf,'d/m/Y');
}

$id_pessoa = (int) $request->query('id_pessoa');
$view['id_pessoa'] = $id_pessoa;
if($id_pessoa > 0) {
	$paramAdd .= " AND id_projeto IN (SELECT id FROM projetos WHERE id_cliente = {$id_pessoa})";
	$view['pessoa'] = Pessoa::load($id_pessoa);
}else{
	$view['pessoa'] = new Pessoa();
}

$rs = Conta::search([
    's' => 'id',
    'w' => "{$paramAdd}",
    'o' => 'vencimento'
]);

while($rs->next()){
    $view['contas'][] = Conta::load($rs->getInt('id'));
}


