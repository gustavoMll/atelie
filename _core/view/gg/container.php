<?php if ($view['module'] == '404.php') {
    include dirname(__FILE__) . "/module." . $view['module'];
} else { ?>
    <div class="d-flex align-items-stretch min-h-100 overflow-x-hidden">
        <?php if(file_exists(__DIR__ . "/sidebar.php")){
            include __DIR__ . "/sidebar.php";
        } ?>
        <main id="content">
            <?php if(file_exists(__DIR__ . "/header.php")){
                include __DIR__ . "/header.php";
            } ?>
            <div class="p-3">
                <div class="main-content p-4 mb-md-0">
                
                    <?php include dirname(__FILE__) . "/module." . $view['module']; ?>
                
                </div>
            </div>
        </main>
    </div>
<?php } ?>

<?php include dirname(__FILE__) . '/overlays.php' ?>