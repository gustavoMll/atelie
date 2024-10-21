<?php

$view['list-filter'] = $Modules['class']::searchForm($request);

$module = $request->get('module');
$action = $request->get('action');
$view['title']  = $Modules['nome'];
$view['hasHeader'] =  true;
$objClass = new $Modules['class']();
$view['tab-adicional'] = $objClass->getExtraTab();
$view['content-tab-adicional'] = $objClass->getExtraTabContent();
$view['end_scripts'] .= $objClass->getExtraFunction();

if($action == 'editar' && $request->getInt('id') > 0){
  $id_reg = $request->getInt('id');
  $view['end_scripts'] .= "modalForm(`{$module}`, {$id_reg}, ``, function(){ getLine(`{$module}`, {$id_reg}); setHref(`{$module}`); });".PHP_EOL; 
}elseif($action == 'adicionar'){
  $view['end_scripts'] .= "modalForm(`{$module}`, 0, ``, function(){ tableList(`{$module}`, window.location.search.substr(1), 'resultados', false); });";
}