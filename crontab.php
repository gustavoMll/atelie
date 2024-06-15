<?php
global $corePath,$defaultPath;
$defaultPath = __DIR__."/";
$corePath = $defaultPath.'_core/';
require $corePath.'vendor/autoload.php';

$serverPath = str_replace('crontab.php','',$_SERVER['SCRIPT_NAME']);
define("__BASEPATH__", "/");

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

$cPath      = $corePath.'controller/';
$mPath      = $corePath.'model/';
$vPath      = $corePath.'view/';

require $corePath.'autoload.php';
require $corePath.'config.php';

//Cron Jobs
Mail::sendScheduled(10);

exit('Cron executado com sucesso.');
