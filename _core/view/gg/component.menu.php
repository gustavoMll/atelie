<style>
    .dropdown-menu-transparent {
        background-color: var(--lev-dark);
        border: 1px solid #fff;
        box-shadow: none;
    }
    .dropdown-item:hover {
        background-color: rgba(0, 0, 0, 0.1);
    }

    .dropdown-menu-end {
        left: 100%;
        right: auto;
        top: 0;
        margin-top: 0;
        transform: translateX(-100%);
    }

</style>
    
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center" href="<?=__BASEPATH__?>" target="_blank">
            <i class="ti ti-world fs-3"></i>
            Ver site
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center <?=($request->get('module') == '' ? "active": '')?>" href="<?=__PATH__?>">
            <i class="ti ti-home fs-3"></i>
            In&iacute;cio
        </a>
    </li>
    
    <?php 
    if(count($view['menus']) > 0){ ?>
        <?php foreach($view['menus'] as $menu => $item){ ?>
            <li class="nav-item"><a class="nav-link d-flex align-items-center <?=($request->get('module') == $menu? "active": '')?>" href="<?=__PATH__.$menu?>">
                <i class="<?=$item['icon']?> fs-3"></i>
                <?=$item['name']?>
            </a></li>
        <?php } ?>
    <?php } ?>



    <?php if($objSession->hasPermition('permissoes') || $objSession->hasPermition('usuarios')){ ?>
        <li class="text-secondary text-uppercase small nav-item divider fw-bold">Ger&ecirc;ncia</li>
        <?php if($objSession->hasPermition('permissoes')){ ?>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center <?=($request->get('module') == 'permissoes' ? " active" : '')?>" href="<?=__PATH__?>permissoes">
                    <i class="ti ti-lock fs-3"></i>
                    Permiss&otilde;es
                </a>
            </li>
        <?php } ?>
        <?php if($objSession->hasPermition('usuarios')){ ?>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center <?=($request->get('module') == 'usuarios' ? " active" : '')?>" href="<?=__PATH__?>usuarios">
                    <i class="ti ti-user fs-3"></i>
                    Usu&aacute;rios
                </a>
            </li>
        <?php } ?>
    <?php } ?>


    <?php if($objSession->hasPermition('over.metatags') || $objSession->hasPermition('over.contato') || $objSession->hasPermition('over.parametros')){ ?>
        <?php if($objSession->hasPermition('over.metatags')){ ?>
            <li class="nav-item d-none">
                <a class="nav-link d-flex align-items-center" href="#metatags" data-bs-toggle="modal" data-bs-target="#metatags">
                    <i class="ti ti-tag fs-3"></i>
                    Meta tags
                </a>
            </li>
        <?php } ?>
        <?php if($objSession->hasPermition('over.contato')){ ?>
            <li class="nav-item">
                <a class="nav-link d-none d-flex align-items-center" href="#contato" data-bs-toggle="modal" data-bs-target="#contato">
                    <i class="ti ti-address-book fs-3"></i>
                    Contato
                </a>
            </li>
        <?php } ?>
        <?php if($objSession->hasPermition('over.parametros')){ ?>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center" href="#parametros" data-bs-toggle="modal" data-bs-target="#parametros">
                    <i class="ti ti-dots fs-3"></i>
                    Par&acirc;metros Gerais
                </a>
            </li>
        <?php } ?>
    <?php } ?>
    