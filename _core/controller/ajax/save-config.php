<?php

Utils::ajaxHeader();

foreach ($_POST as $key => $value) {
    GG::saveConfig($key, htmlentities($value,ENT_QUOTES,'utf-8'));
}

Utils::jsonResponse('Configura&ccedil;&otilde;es salvas com sucesso', true);
