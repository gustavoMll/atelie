

<li class="dropdown-item"><a href="<?=__PATH__?>proximas-coletas" class="d-flex gap-2"><i class="ti ti-calendar-minus fs-3"></i> <span class="me-auto">Pr&oacute;ximas Coletas</a></li>

<li class="dropdown-item"><a href="<?=__PATH__?>proximas-devolucoes" class="d-flex gap-2"><i class="ti ti-calendar-plus fs-3"></i> <span class="me-auto">Pr&oacute;ximas Devolu&ccedil;&otilde;es</a></li>

<li class="dropdown-item"><a href="<?=__PATH__?>relatorio-alugueis" class="d-flex gap-2"><i class="ti ti-brand-cashapp fs-3"></i> <span class="me-auto">Relat&oacute;rio de Alugueis</a></li>

<li class="dropdown-item"><a href="<?=__PATH__?>alugueis" class="d-flex gap-2"><i class="ti ti-plus fs-3"></i> <span class="me-auto">Alugueis</a></li>

<li class="dropdown-item"><a href="<?=__PATH__?>clientes" class="d-flex gap-2"><i class="ti ti-users fs-3"></i> <span class="me-auto">Clientes</a></li>

<li class="dropdown-item"><a href="<?=__PATH__?>acessorios" class="d-flex gap-2"><i class="ti ti-hanger-2 fs-3"></i> <span class="me-auto">Acess&oacute;rios</a></li>

<li class="dropdown-item"><a href="<?=__PATH__?>fantasias" class="d-flex gap-2"><i class="ti ti-hanger fs-3"></i> <span class="me-auto">Fantasias</a></li>

<li class="dropdown-item"><a href="<?=__PATH__?>acessorios-quadrilha" class="d-flex gap-2"><i class="ti ti-brand-redhat fs-3"></i> <span class="me-auto">Acess&oacute;rios de Quadrilha</a></li>

<li class="dropdown-item"><a href="<?=__PATH__?>fantasias-quadrilha" class="d-flex gap-2"><i class="ti ti-shirt fs-3"></i> <span class="me-auto">Fantasias de Quadrilha</a></li>

<?php /*
uasort($view['menus'], function ($a, $b) {
    if ($a['ordem'] == 0) return 1;
    if ($b['ordem'] == 0) return -1;
    return $a['ordem'] <=> $b['ordem'];
});

foreach($view['menus'] as $menu => $item){ ?>
    <li class="dropdown-item"><a class="nav-link d-flex align-items-center <?=($request->get('module') == $menu? " text-white": '')?> gap-2" href="<?=__PATH__.$menu?>">
        <i class="<?=$item['icon']?> fs-3"></i> 
        <?=$item['name']?>
    </a></li>
<?php } */?>

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


