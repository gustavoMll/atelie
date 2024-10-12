

<li class="dropdown-item"><a href="<?=__PATH__?>" class="d-flex gap-2"><i class="ti ti-calendar-minus fs-3"></i> <span class="me-auto">Pr&oacute;ximas Coletas</a></li>

<li class="dropdown-item"><a href="<?=__PATH__?>proximas-devolucoes" class="d-flex gap-2"><i class="ti ti-calendar-plus fs-3"></i> <span class="me-auto">Pr&oacute;ximas Devolu&ccedil;&otilde;es</a></li>

<?php foreach($view['menus'] as $menu => $item){ ?>
    <li class="dropdown-item"><a class="nav-link d-flex align-items-center <?=($request->get('module') == $menu? " text-white": '')?> gap-2" href="<?=__PATH__.$menu?>">
        <i class="<?=$item['icon']?> fs-3"></i> 
        <?=$item['name']?>
    </a></li>
<?php } ?>




<?php $hasPerm = false; foreach(['configuracoes','usuarios', 'grupos'] as $k){ if($objSession->hasPermition($k)){$hasPerm=true; break;} } if($hasPerm){ ?>

<li class="dropdown-item mt-lg-auto">
    <a href="" class="dropdown-toggle gap-2" data-bs-toggle="dropdown"><i class="ti ti-settings fs-3"></i> <span class="me-auto">Administração</span></a>
    <ul class="dropdown-menu <?=(in_array($request->get('module'), ['configuracoes', 'grupos', 'usuarios']) ? ' show' : '')?>">

    <?php /*if($objSession->hasPermition('configuracoes')){ ?>
        <li class="dropdown-item"><a <?=($request->get('module') == 'configuracoes' ? ' class="text-white"' : '')?> href="<?= __PATH__ ?>configuracoes">Configurações</a></li>
    <?php } */?>
    
    <?php if($objSession->hasPermition('usuarios')){ ?>
        <li class="dropdown-item"><a <?=($request->get('module') == 'usuarios' ? ' class="text-white"' : '')?> href="<?= __PATH__ ?>usuarios">Usu&aacute;rios</a></li>
    <?php } ?>
    </ul>
</li>
<?php } ?>


