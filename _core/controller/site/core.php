<?php
global $view;
$view['menus'] = array();
$view['title']  = $Config->get('nome-site');
$view['description']  = $Config->get('meta-desc');
$view['keywords']  = $Config->get('keywords');
$view['module'] = $request->get('module');
$view['acao']   = $request->get('action');
$view['canonical'] = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

if($view['module'] == 'gerar-sitemap'){
    $base = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].__PATH__;
    $sitemap = $defaultPath.'sitemap.xml';
    if(file_exists($sitemap)) unlink($sitemap);
    
    $sm  = '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL;
    $sm .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.PHP_EOL;
    $sm .= '<url><loc>'.$base.'</loc></url>'.PHP_EOL;
    $sm .= '</urlset>';
    file_put_contents($sitemap, $sm);

    $robotsPath = $defaultPath.'robots.txt';
    if(file_exists($robotsPath)) unlink($robotsPath);
    $robots  = 'User-agent: *'.PHP_EOL;
    $robots .= 'Allow: '.__PATH__.PHP_EOL;
    $robots .= 'Disallow: '.__PATH__.'gg/'.PHP_EOL;
    file_put_contents($robotsPath, $robots);
    
    echo json_encode(array(
        'code' => 200,
        'message' => 'Sitemap generated successifuly'
    ));
    exit;

}elseif (file_exists(dirname(__FILE__) . "/module.{$request->get('module')}.php")) {

    $view['module'] = "{$request->get('module')}.php";
    
    include dirname(__FILE__) . "/module." . $view['module'];    
}else {
    if (in_array($request->get('module'),array('','home'))) {
        $view['module'] = 'home.php';
        $view['page_class'] = 'home';   
    }else{
        $view['module'] = 'home.php';
    }
}


