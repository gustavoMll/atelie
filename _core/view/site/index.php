<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <title>Traço Engenharia - Construindo qualidade de vida</title>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="keywords" content="Traço engenharia" />
        <meta name="description" content="Traço engenharia" />
        <meta name="author" content="LEVSISTEMAS" />
        <meta name="reply-to" content="" />
        <meta name="robots" content="index,follow" />
        

        <meta name="verify-v1" content="" />
        <link rel="shortcut icon" href="<?= __BASEPATH__ ?>img/favicon.png">
        
        <link href="<?=__BASEPATH__?>css/framework.gg4.min.css?v=<?=filemtime($defaultPath.'css/framework.gg4.min.css')?>" rel="stylesheet">   
        
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <style>
            #principal{
                background: url('<?=__BASEPATH__?>img/background.jpeg') no-repeat center center fixed;
                background-size: cover;
            }
            .social {
                margin-top: 2rem;
                padding-top: 2rem;
                border-top: 1px solid #EEE;
                display: flex;
                gap: 1rem }

            .social i { 
                display: inline-block;
                position: relative;
                width: 24px; 
                height: 24px }
        </style>
    </head>

    <body class="d-flex flex-column">
        <main id="principal" class="p-0 min-vh-100 d-flex align-items-center" style="overflow: overlay;">
            <div class="container d-flex flex-column align-items-center justify-content-center p-0">
             <?php 
             include dirname(__FILE__) . "/module." . $view['module'];
             ?>   
            </div>
        </main>
        
    </body>
</html>