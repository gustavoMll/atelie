<nav class="position-relative d-none d-sm-block" style="z-index: 5;" id="navHeader">
    <div class="py-4 px-5 bg-white border border-primary border-opacity-10">
        <div class="px-3 px-md-5 d-flex justify-content-between gap-3">
            <a href="#" class="d-block position-absolute text-decoration-none" aria-label="Open Collapse" data-toggle="sidebarCollapse">
                <i class="ti ti-menu-2 fs-2"></i>
            </a>
            <?php if ($view['hasHeader']) {
                include dirname(__FILE__) . "/component.pageheader.php";
            }else if($request->get('module') == '' || $request->get('module') == 'home'){?>
                <h2 class="fw-bold text-primary mb-0">Dashboard</h2>
            <?php } ?>
            <div class="d-flex align-items-center justify-content-center justify-content-md-end perfil-dropdown">
                <div class="flex-dropdown text-md-end mx-md-0 d-flex flex-column align-items-center">
                    <div class="position-relative d-flex align-items-center gap-1">
                        
                        <?php if($objSession->get('img') != ''){ ?>
                            
                            <div class="user-profile">
                                <img src="<?=$objSession->getImage()?>" alt="perfil" class="w-100 h-100">
                            </div>

                        <?php }else{ ?>

                            <i class="ti ti-user-circle fs-1"></i>

                        <?php } ?>
                        <i class="ti ti-caret-down dropdown-icon"></i>

                        <a href="#" class="flex-dropdown-toggle stretched-link" aria-label="Open User Dropdown" data-toggle="flex-dropdown" data-target="#dropdownList"></a>

                    </div>
                    <ul id="dropdownList" class="bg-white">
                        <li>Bem vindo, <strong><?=$objSession->get('nome')?></strong></li>
                        <li>
                            <a class="d-flex align-items-center justify-content-center gap-2" target="_blank" href="javascript:;" aria-label="ver perfil" data-bs-toggle="modal" data-bs-target="#perfil">
                                <i class="ti ti-user"></i>
                                Ver perfil
                            </a>
                        </li>
                        <li>
                            <a class="d-flex align-items-center justify-content-center gap-2" href="javascript:;" aria-label="Sair" onclick="logout()">
                                <i class="ti ti-logout"></i>
                                Sair
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>

<nav class="position-relative d-block d-sm-none" style="z-index: 5;" id="navSmHeader">
    <div class="py-3 px-5">
        <div class="px-3 px-md-5 d-flex justify-content-center justify-content-sm-start gap-3">
            <a href="#" class="d-block position-absolute text-decoration-none" aria-label="Open Collapse" data-toggle="sidebarCollapse">
                <i class="ti ti-menu-2 fs-1"></i>
            </a>
            <?php if ($view['hasHeader']) {
                include dirname(__FILE__) . "/component.pageheader.php";
            }else if($request->get('module') == '' || $request->get('module') == 'home'){?>
                <h2 class="fw-bold text-primary">Dashboard</h2>
            <?php } ?>
        </div>
    </div>
</nav>
<script>
    function logout() {
        const title = 'Logout';
        blockUi();

        function logoutFinish(resp) {
            unblockUi();
            if (resp.readyState == 4) {
                MessageBox.error(resp.responseText, title)

            } else if (resp.success) {
                MessageBox.success(resp.message, title);
                setTimeout(function() {
                    location = '<?= __PATH__ ?>';
                }, 1000);

            } else {
                MessageBox.error(resp.message, title);
            }
        }

        $.get({
            url: `<?= __PATH__ ?>login/out`,
            dataType: "json",
            success: logoutFinish,
            error: logoutFinish
        });
        return false;
    }
</script>