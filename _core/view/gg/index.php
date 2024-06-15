<!DOCTYPE html>
<html lang="<?=$GLOBALS['Language']?>">
    <head>
        <title><?=$GLOBALS['AppName']?> - <?= $Config->get('nome-site') ?></title>
        <meta charset="<?=$GLOBALS['Charset']?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="LEVSISTEMAS" />
        <meta name="reply-to" content="contato@levsistemas.com.br" />
        <meta name="robots" content="index,follow" />
        <meta name="verify-v1" content="" />
        <link rel="shortcut icon" href="https://www.levsistemas.com.br/favicon.png">
        <!-- Latest compiled and minified CSS -->
        <link href="<?=__BASEPATH__?>css/framework.gg4.min.css?v=<?=filemtime($defaultPath.'css/framework.gg4.min.css')?>" rel="stylesheet">  
        <link href="<?=__BASEPATH__?>css/gg.css?v=<?=filemtime($defaultPath.'css/gg.css')?>" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.5/dist/sweetalert2.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" />
        
        
    </head>
    <body class="<?= $view['page_class'] ?>">
        <?php
        if ($view['logged']) {
            include("container.php");
            include("footer.php");
        } else {
            include dirname(__FILE__) . "/module." . $view['module'];
        }
        ?>
        
        <script> var __PATH__ = '<?= __PATH__ ?>';</script>
        <script> var __BASEPATH__ = '<?= __BASEPATH__ ?>';</script>

        <script src="<?= __BASEPATH__ ?>js/framework.gg4.min.js?v=<?=filemtime($defaultPath.'js/framework.gg4.min.js')?>"></script>
        <script src="<?= __BASEPATH__ ?>js/common.js?v=<?=filemtime($defaultPath.'js/common.js')?>"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        

        <?php if ($view['logged']) { ?> 
            <script src="<?= __BASEPATH__ ?>js/ckeditor.js?v=<?=filemtime($defaultPath.'js/ckeditor.js')?>"></script>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/plupload/3.1.5/jquery.plupload.queue/css/jquery.plupload.queue.min.css" integrity="sha512-50UY9VY37/VxML0pGNJb59uufYoNCfrnYb81jx6AswTD5mRhdnXfBeyA6uxOfygxRZqj7jCjDjtIXRmTlOc48w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
            <script src="https://cdnjs.cloudflare.com/ajax/libs/plupload/3.1.5/plupload.full.min.js" integrity="sha512-yLlgKhLJjLhTYMuClLJ8GGEzwSCn/uwigfXug5Wf2uU5UdOtA8WRSMJHJcZ+mHgHmNY+lDc/Sfp86IT9hve0Rg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/plupload/3.1.5/jquery.plupload.queue/jquery.plupload.queue.min.js" integrity="sha512-Fn6cBLKAk/IXJZvGyCNGqS4otOtBh9MV7g9paw1ltw3W00Z7HIehOYXYeVWNsyd9PyohmC3RjKA3v25/xEEizQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/plupload/3.1.5/i18n/pt_BR.min.js" integrity="sha512-Zdz+FFZRdUpHPsSXUHvRmlTS1MK0TFAn7OJeYOB9R8EfLvvEp8nMSfVZSILS48mvyc+RKXwA+PhSArRGfvGosA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

            <script src="<?= __BASEPATH__ ?>js/gg.js?v=<?=filemtime($defaultPath.'js/gg.js')?>"></script>
            
        <?php } ?>
            
        <?php if($view['end_scripts']!=''){ ?>
            <script>
            $(function() {
                <?=$view['end_scripts']?>
            });
            </script>
        <?php } ?>
    </body>
</html>