<?php 

Utils::ajaxHeader();

$obj = new ItemAluguel();

$obj->restaurarQtdItens();

Utils::jsonResponse("Itens atualizados com sucesso!", true);
