<?php

Utils::ajaxHeader();

$module = $request->get('class');

if (!file_exists(__DIR__. "/../config.{$module}.php")) {
    Utils::jsonResponse('M&oacute;dulo n&atilde;o instalado. Contate o suporte t&eacute;cnico.');
}

if(!$objSession->hasPermition($module,'sel')){
    Utils::jsonResponse("Desculpe, voc&ecirc; n&atilde;o possui permiss&atilde;o para realizar esta opera&ccedil;&atilde;o neste m&oacute;dulo.");
}

include(__DIR__. "/../config.{$module}.php");
$objClass = new $Modules['class']();


$registros = 0;
$paramAdd = $Modules['class']::filter($request);
$order = $request->query('order', $Modules['ordenacao']);

$rs = $Modules['class']::search([
    's' => 'count(id) qtd', 
    'w' => $paramAdd
]);
$rs->next();

$registros = $rs->getInt('qtd');
if($registros > 0){
    $por_pagina = $request->query('offset') != '' ? (int) $request->query('offset') : 25;
    $paginas = ($registros > 0 ? ceil($registros/$por_pagina) : 0);
    $pagina = (int) $request->query('page');
    if($pagina <= 0 || $pagina > $paginas){
        $pagina = 1;
    }
    $inicio = ($pagina-1) * $por_pagina;
    
    $rs = $Modules['class']::search([
        's' => implode(',',$objClass->getPk()), 
        'w' => $paramAdd, 
        'o' => $order, 
        'l' => "{$inicio},{$por_pagina}"
    ]);
    echo $Modules['class']::getTable($rs);

    if($paginas > 1){
        $queryString = Utils::replace('/&?page=[0-9]+/','',$_SERVER['QUERY_STRING']);
        $queryString = Utils::replace('/&?selector=[^&]+/','', $queryString);
        $queryString = Utils::replace('/&?changePath=[^&]+/','', $queryString);

        $maxPages = 5;
        $inicio = $pagina - $maxPages;
        $fim = $pagina + $maxPages;
        
        if($inicio < 1) $inicio = 1;
        if($fim > $paginas) $fim = $paginas;

        $func = "tableList(`{$module}`, `".($queryString == '' ? '' : $queryString.'&')."page=#PG`, `{$request->query('selector')}`, ".($request->query('changePath') != '' ? 'true' : 'false').")";

        echo '<nav aria-label="List pagination"><ul class="pagination justify-content-center">';

        if($paginas > ($maxPages*2)+1) echo '<li class="page-item"><a href="#pagination" class="page-link h-100" onclick="return '.str_replace('#PG',1,$func).'"><i class="ti ti-chevron-left-pipe"></i></a></li>';
        if($pagina > 1) echo '<li class="page-item"><a href="#pagination" class="page-link h-100" onclick="return '.str_replace('#PG',$pagina-1,$func).'"><i class="ti ti-chevron-left"></i></a></li>';
        if($pagina - $maxPages > 1) echo '<li class="page-item disabled"><a href="#pagination" class="page-link" onclick="return false">...</a></li>';

        for($i=$inicio; $i<=$fim; $i++){
            echo '<li class="page-item '.($i==$pagina?'active':'').'"><a href="#pagination" class="page-link" onclick="return '.($i==$pagina?'false': str_replace('#PG',$i,$func)).'">'.$i.'</a></li>';
        }

        if($pagina + $maxPages < $paginas) echo '<li class="page-item disabled"><a href="#pagination" class="page-link h-100" onclick="return false">...</a></li>';
        if($pagina < $paginas) echo '<li class="page-item"><a href="#pagination" class="page-link h-100" onclick="return '.str_replace('#PG',$pagina+1,$func).'"><i class="ti ti-chevron-right"></i></a></li>';
        if($paginas > ($maxPages*2)+1) echo '<li class="page-item"><a href="#pagination" class="page-link h-100" onclick="return '.str_replace('#PG',$paginas,$func).'"><i class="ti ti-chevron-right-pipe"></i></a></li>';

        echo '</ul></nav>';
    }
}else{
    echo '
        <div class="d-flex flex-column align-items-center gap-5 mb-0">
            <i class="icon w-320px">'.str_replace('PREFIX',md5(microtime()),file_get_contents($defaultPath.'img/svg/empty-image.svg')).'</i>
            <div class="text-center">
                <h3 class="text-gray-600 mb-0">Nenhum registro</h3>
                <h2 class="text-secondary fw-bold h1">encontrado</h2>
            </div>
        </div>
    ';
}

$html = ob_get_contents();
ob_end_clean();

Utils::jsonResponse('',true, [
    'html' => $html,
    'qtd' => $registros,
]);
