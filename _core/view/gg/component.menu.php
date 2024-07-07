
<li class="dropdown-item"><a href="<?=__PATH__?>" class="d-flex gap-2"><i class="ti ti-layout-dashboard fs-3"></i> <span class="me-auto">Dashboard</a></li>
    <?php if($objSession->hasPermition('pessoas')){ ?>
        <li class="dropdown-item"><a href="<?= __PATH__ ?>pessoas" class="d-flex gap-2"><i class="ti ti-users fs-3"></i> <span class="me-auto">Pessoas</span></a></li>
    <?php } ?>

    <?php /* if($objSession->hasPermition('projetos')){ ?>
        <li class="dropdown-item"><a href="<?=__PATH__ ?>projetos" class="d-flex gap-2"><i class="ti ti-presentation-analytics fs-3"></i> <span class="me-auto">Projetos</span></a></li>
    <?php } */ ?>
    

    <?php $hasPerm = false; foreach(['recorrencias','propostas'] as $k){ if($objSession->hasPermition($k)){$hasPerm=true; break;} } if($hasPerm){ ?>
    <li class="dropdown-item">
        <a href="" class="dropdown-toggle gap-2" data-bs-toggle="dropdown"><i class="ti ti-headset fs-3"></i> <span class="me-auto">Comercial</span></a>
        <ul class="dropdown-menu <?=(in_array($request->get('module'), ['recorrencias', 'propostas', 'consultores']) ? ' show' : '')?>">

        <?php if($objSession->hasPermition('recorrencias')){ ?>
            <li class="dropdown-item"><a <?=($request->get('module') == 'recorrencias' ? ' class="text-white"' : '')?>href="<?= __PATH__ ?>recorrencias">Recorr&ecirc;ncias</a></li>
        <?php } ?>
        
        <?php if($objSession->hasPermition('propostas')){ ?>
            <li class="dropdown-item"><a <?=($request->get('module') == 'propostas' ? ' class="text-white"' : '')?> href="<?= __PATH__ ?>propostas">Propostas</a></li>
        <?php } ?>
        
        <?php if($objSession->hasPermition('consultores')){ ?>
            <li class="dropdown-item"><a <?=($request->get('module') == 'consultores' ? ' class="text-white"' : '')?> href="<?= __PATH__ ?>consultores">Consultores</a></li>
        <?php } ?>
        </ul>
    </li>
    <?php } ?>


    <?php $hasPerm = false; foreach(['contas-a-receber','contas-a-pagar', 'movimentacao-financeira','pagamento-banco-inter','extrato-banco-inter','negociacao-financeira','nfs'] as $k){ if($objSession->hasPermition($k)){$hasPerm=true; break;} } if($hasPerm){ ?>
    
    <li class="dropdown-item">
        <a href="" class="dropdown-toggle gap-2" data-bs-toggle="dropdown"><i class="ti ti-wallet fs-3"></i> <span class="me-auto">Financeiro</span></a>

        <ul class="dropdown-menu <?=(in_array($request->get('module'), ['extrato-banco-inter', 'contas-a-receber', 'contas-a-pagar', 'movimentacao-financeira', 'nfs', 'negociacao-financeira', 'pagamento-banco-inter', 'cheques']) ? ' show' : '')?>">
    
        <?php 
        if(file_exists($defaultPath.'uploads/Inter_API_Certificado.crt')){ ?>

        <?php if($objSession->hasPermition('extrato-banco-inter')){ ?>
            <li class="dropdown-item"><a <?=($request->get('module') == 'extrato-banco-inter' ? ' class="text-white"' : '')?> href="<?= __PATH__ ?>extrato-banco-inter">Extrato Banco Inter</a></li>
        <?php }
        } ?>
        
                <?php if($objSession->hasPermition('cheques')){ ?>
                    <li class="dropdown-item"><a <?=($request->get('module') == 'cheques' ? ' class="text-white"' : '')?> href="<?= __PATH__ ?>cheques">Cheques</a></li>
                <?php } ?>
        
        <?php if($objSession->hasPermition('contas-a-receber')){ ?>
            <li class="dropdown-item"><a <?=($request->get('module') == 'contas-a-receber' ? ' class="text-white"' : '')?> href="<?= __PATH__ ?>contas-a-receber">Contas a receber</a></li>
        <?php } ?>
        
        <?php if($objSession->hasPermition('contas-a-pagar')){ ?>
            <li class="dropdown-item"><a <?=($request->get('module') == 'contas-a-pagar' ? ' class="text-white"' : '')?> href="<?= __PATH__ ?>contas-a-pagar">Contas a pagar</a></li>
        <?php } ?>
        
        <?php if($objSession->hasPermition('movimentacao-financeira')){ ?>
            <li class="dropdown-item"><a <?=($request->get('module') == 'movimentacao-financeira' ? ' class="text-white"' : '')?> href="<?= __PATH__ ?>movimentacao-financeira">Movimentação financeira</a></li>
        <?php } ?>
        
        <?php if($objSession->hasPermition('nfs')){ ?>
            <li class="dropdown-item"><a <?=($request->get('module') == 'nfs' ? ' class="text-white"' : '')?> href="<?= __PATH__ ?>nfs">Notas Fiscais de Servi&ccedil;o</a></li>
        <?php } ?>
        
        <?php if($objSession->hasPermition('negociacao-financeira')){ ?>
            <li class="dropdown-item"><a <?=($request->get('module') == 'negociacao-financeira' ? ' class="text-white"' : '')?> href="<?= __PATH__ ?>negociacao-financeira">Negocia&ccedil;&atilde;o Financeira</a></li>
        <?php } ?>
        
        <?php if(file_exists($defaultPath.'uploads/Inter_API_Certificado.crt')){ ?>

        <?php if($objSession->hasPermition('pagamento-banco-inter')){ ?>
            <li class="dropdown-item"><a <?=($request->get('module') == 'pagamento-banco-inter' ? ' class="text-white"' : '')?> href="<?= __PATH__ ?>pagamento-banco-inter">Pagamento Banco Inter</a></li>
        <?php } ?>
        
        <?php } ?>
        </ul>
    </li> 

    <?php } ?> 

    <?php $hasPerm = false; foreach(['relatorio-centros','acompanhamento-financeiro','relatorio-financeiro','relatorio-consultores', 'relatorio-aniversariantes','relatorio-horas'] as $k){ if($objSession->hasPermition($k)){$hasPerm=true; break;} } if($hasPerm){ ?>

    <li class="dropdown-item">
        <a href="" class="dropdown-toggle gap-2" data-bs-toggle="dropdown"><i class="ti ti-chart-line fs-3"></i> <span class="me-auto">Relatórios</a>
    
        <ul class="dropdown-menu <?=(in_array($request->get('module'), ['acompanhamento-financeiro', 'relatorio-financeiro', 'relatorio-centros', 'relatorio-aniversariantes', 'emailsagendados', 'relatorio-consultores']) ? ' show' : '')?>">

        <?php if($objSession->hasPermition('acompanhamento-financeiro')){ ?>
            <li class="dropdown-item"><a <?=($request->get('module') == 'acompanhamento-financeiro' ? ' class="text-white"' : '')?> href="<?= __PATH__ ?>acompanhamento-financeiro">Acompanhamento financeiro</a></li>
        <?php } ?>
        
        <?php if($objSession->hasPermition('relatorio-financeiro')){ ?>
            <li class="dropdown-item"><a <?=($request->get('module') == 'relatorio-financeiro' ? ' class="text-white"' : '')?> href="<?= __PATH__ ?>relatorio-financeiro">Relatório financeiro</a></li>
        <?php } ?>

        <?php if($objSession->hasPermition('relatorio-centros')){ ?>
            <li class="dropdown-item"><a <?=($request->get('module') == 'relatorio-centros' ? ' class="text-white"' : '')?> href="<?= __PATH__ ?>relatorio-centros">Centros de Custo</a></li>
        <?php } ?>
        
        <?php if($objSession->hasPermition('relatorio-aniversariantes')){ ?>
            <li class="dropdown-item"><a <?=($request->get('module') == 'relatorio-aniversariantes' ? ' class="text-white"' : '')?> href="<?= __PATH__ ?>relatorio-aniversariantes">Aniversariantes</a></li>
        <?php } ?>
        
        <?php if($objSession->hasPermition('emailsagendados')){ ?>
            <li class="dropdown-item"><a <?=($request->get('module') == 'emailsagendados' ? ' class="text-white"' : '')?> href="<?= __PATH__ ?>emailsagendados">E-mails Agendados</a></li>
        <?php } ?>
        
        <?php if($objSession->hasPermition('relatorio-consultores')){ ?>
            <li class="dropdown-item"><a <?=($request->get('module') == 'relatorio-consultores' ? ' class="text-white"' : '')?> href="<?= __PATH__ ?>relatorio-consultores">Relatório consultores</a></li>
        <?php } ?>
        
        </ul>
    </li>
    <?php } ?> 

    <?php if(count($view['menus']) > 0){ ?>
        <li class="dropdown-item">
            <a href="" class="dropdown-toggle gap-2" data-bs-toggle="dropdown"><i class="ti ti-settings-cog fs-3"></i> <span class="me-auto">Configurações</span></a>
            <ul class="dropdown-menu <?=(in_array($request->get('module'), array_keys($view['menus'])) ? ' show' : '')?>">
            <?php foreach($view['menus'] as $menu => $item){ ?>
                <li class="dropdown-item"><a class="nav-link d-flex align-items-center <?=($request->get('module') == $menu? " text-white": '')?>" href="<?=__PATH__.$menu.($menu == 'centroscusto' ? '?id_pai=0' : '')?>">
                    <!-- <i class="<?=$item['icon']?>"></i> -->
                    <?=$item['name']?>
                </a></li>
            <?php } ?>
            </ul>
        </li>
    <?php } ?>
    
    <?php $hasPerm = false; foreach(['configuracoes','usuarios', 'grupos'] as $k){ if($objSession->hasPermition($k)){$hasPerm=true; break;} } if($hasPerm){ ?>

    <li class="dropdown-item mt-lg-auto">
        <a href="" class="dropdown-toggle gap-2" data-bs-toggle="dropdown"><i class="ti ti-settings fs-3"></i> <span class="me-auto">Administração</span></a>
        <ul class="dropdown-menu <?=(in_array($request->get('module'), ['configuracoes', 'grupos', 'usuarios']) ? ' show' : '')?>">
    
        <?php if($objSession->hasPermition('configuracoes')){ ?>
            <li class="dropdown-item"><a <?=($request->get('module') == 'configuracoes' ? ' class="text-white"' : '')?> href="<?= __PATH__ ?>configuracoes">Configurações</a></li>
        <?php } ?>
        
        <?php if($objSession->hasPermition('usuarios')){ ?>
            <li class="dropdown-item"><a <?=($request->get('module') == 'usuarios' ? ' class="text-white"' : '')?> href="<?= __PATH__ ?>usuarios">Usu&aacute;rios</a></li>
        <?php } ?>
        </ul>
    </li>
    <?php } ?>

    
