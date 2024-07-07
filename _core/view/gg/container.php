<?php if ($view['module'] == '404.php') {
    include dirname(__FILE__) . "/module." . $view['module'];
} else { ?>
        <?php if(file_exists(__DIR__ . "/sidebar.php")){
            include __DIR__ . "/sidebar.php";
        } ?>
        <main id="content" class="container-fluid py-3 px-md-5 pb-4">
            <?php if(file_exists(__DIR__ . "/header.php")){
                include __DIR__ . "/header.php";
            } ?>
                
            <?php include dirname(__FILE__) . "/module." . $view['module']; ?>
            
        </main>
<?php } ?>

<?php include dirname(__FILE__) . '/overlays.php' ?>