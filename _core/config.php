<?php

global 
    $EnderecoSite, $Dominio, $DBHost, $DBUser, $DBPassWord, $DBName, $AppName, 
    $DBDriver, $Sessao, $Prefix, $EmailHost, $EmailPort, $EmailSMTPSecure, $CpanelUser,
    $EmailSMTPAuth, $EmailUsername, $EmailPassword, $EmailTipo, $Config, $Estados, $NmSN,
    $Modules, $DBCONN, $DBPREFIX, $DBPort, $xmlConfig, $objSession, $FlexId, $Language,
    $DBFPHost, $DBFPUser, $DBFPPassWord, $DBFPName, $Charset, $QtdRegistros, $DBCharset,
    $EmailGCID, $EmailGCSecret;

$DBCONN             = null;
$AppName            = getenv('APP_NAME');
$Dominio            = getenv('APP_DOMAIN');
$EnderecoSite       = "https://".$Dominio;
$CpanelUser         = getenv('CPANEL_USER');
$FlexId             = getenv('FLEX_ID');
$Charset            = getenv('APP_CHARSET');

$DBDriver           = getenv('DB_CONNECTION');
$DBCharset          = getenv('DB_CHARSET');
$DBHost             = getenv('DB_HOST');
$DBPort             = getenv('DB_PORT');
$DBName             = getenv('DB_DATABASE');
$DBUser             = getenv('DB_USERNAME');
$DBPassWord         = getenv('DB_PASSWORD');
$Prefix             = getenv('DB_PREFIX');

$EmailTipo          = getenv('MAIL_DRIVER');
$EmailHost          = getenv('MAIL_HOST');
$EmailPort          = getenv('MAIL_PORT');
$EmailSMTPAuth      = getenv('MAIL_AUTH')=='1';
$EmailSMTPSecure    = getenv('MAIL_ENCRYPTION');
$EmailUsername      = getenv('MAIL_USERNAME');
$EmailPassword      = getenv('MAIL_PASSWORD');
$EmailGCID          = getenv('MAIL_GOOGLE_CLIENT_ID');
$EmailGCSecret      = getenv('MAIL_GOOGLE_CLIENT_SECRET');

$Language           = getenv('LANG');

$DBPREFIX           = '';

$Sessao             = md5("@FP@".__DIR__);
$Modules            = array();

$QtdRegistros = array(25, 50, 100);

$NmSN = array(
    1 => 'Sim',
    2 => 'N&atilde;o',
);

$NmSexo = array(
    1 => 'Masculino',
    2 => 'Feminino',
);

$Estados = array(
    'AC' => 'Acre',
    'AL' => 'Alagoas',
    'AP' => 'Amap&aacute;',
    'AM' => 'Amazonas',
    'BA' => 'Bahia',
    'CE' => 'Cear&aacute;',
    'DF' => 'Distrito Federal',
    'ES' => 'Esp&iacute;rito Santo',
    'GO' => 'Goi&aacute;s',
    'MA' => 'Maranh&atilde;o',
    'MT' => 'Mato Grosso',
    'MS' => 'Mato Grosso do Sul',
    'MG' => 'Minas Gerais',
    'PA' => 'Par&aacute;',
    'PB' => 'Para&iacute;ba',
    'PR' => 'Paran&aacute;',
    'PE' => 'Pernambuco',
    'PI' => 'Piau&iacute;',
    'RJ' => 'Rio de Janeiro',
    'RN' => 'Rio Grande do Norte',
    'RS' => 'Rio Grande do Sul',
    'RO' => 'Rond&ocirc;nia',
    'RR' => 'Roraima',
    'SC' => 'Santa Catarina',
    'SP' => 'S&atilde;o Paulo',
    'SE' => 'Sergipe',
    'TO' => 'Tocantins',
);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('America/Sao_Paulo');
header('Content-type: text/html; charset='.$Charset);
/*
session_set_cookie_params([
    'lifetime' => 600,
    'path' => '/',
    'domain' => $_SERVER['SERVER_NAME'],
    'secure' => true,
    'httponly' => true,
    'samesite' => 'lax',
]);
*/