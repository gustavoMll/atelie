<?php

global $defaultPath, $predefinedPath, $corePath, $mPath, $vPath, $cPath, $request, $view, $conn, $systemPath, $homePath;
$predefinedPath = array(
    ''=> array('folder'=>'', 'path' => ''),
);
$cPath      = __DIR__.'/controller/';
$mPath      = __DIR__.'/model/';
$vPath      = __DIR__.'/view/';
$systemPath = __DIR__.'/../';

if(!file_exists(__DIR__.'/.env'))
die('<h1>Erro fatal</h1><p>Você precisa criar um arquivo com o nome ".env" dentro da pasta _core com as configurações. Utilize o .env.example como base.</p>');
$envVars = parse_ini_file(__DIR__.'/.env');
foreach($envVars as $key => $value) putenv("{$key}={$value}");

ob_start();
require __DIR__.'/autoload.php';
require __DIR__.'/config.php';
session_start();

$view['module'] = "404.php";
$view['page_class'] = 'inner';
$view['end_scripts'] = '';

$serverPath = str_replace('index.php','',$_SERVER['SCRIPT_NAME']);
$url = Utils::replace("#\?.+#i",'',Utils::replace("#^($serverPath)#i",'',$_SERVER['REQUEST_URI']));
$_SERVER['REQUEST_URI'] = htmlspecialchars(str_replace("'",'',$_SERVER['REQUEST_URI']));
$pUrl = explode('/',$url);
if(array_key_exists($pUrl[0], $predefinedPath)){
    define("__PATH__", $serverPath.$predefinedPath[$pUrl[0]]['path']);
    $folderPath = $predefinedPath[$pUrl[0]]['folder'];
    $pagePath = $predefinedPath[$pUrl[0]]['path'];
}else{
    define("__PATH__", $serverPath);
    $folderPath = $predefinedPath['']['folder'];
    $pagePath = $predefinedPath['']['path'];
}
define("__BASEPATH__", $serverPath);
define("__SYSTEMPATH__", $AppPath);
$param = $pagePath != '' ? Utils::replace("#^{$pagePath}?#i",'', $url) : $url;

$request = new Request($param);
$conn = new Connection();
$Config = new Config();

require $cPath.$folderPath.'core.php';
require $vPath.$folderPath.'index.php';


$conn->closeConnection();

