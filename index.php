<?php
global $corePath,$defaultPath, $homePath;
$homePath = __DIR__;
$defaultPath = $homePath."/";
$corePath = $defaultPath.'_core/';
require $corePath.'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable($corePath);
$dotenv->load();

$whoops = new \Whoops\Run();
if (strtolower(substr(getenv('APP_ENVIRONMENT'),0,4))!='prod') {
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
} else {
    $whoops->pushHandler(function($e){
        echo $e;
    });
}
$whoops->register();

require $corePath.'autoload.php';
require $corePath.'bootstrap.php';
