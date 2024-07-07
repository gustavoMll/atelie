<nav class="position-relative" id="navHeader">

    <div class="d-flex gap-2 gap-md-3 align-items-center flex-wrap mb-3">

        <a href="#" onclick="storeConfigSidebar();" class="lh-1" aria-label="Open Collapse" data-toggle="sidebarCollapse"><i class="ti ti-menu-2 fs-1"></i></a>
        <script>
            function storeConfigSidebar(){
                localStorage.setItem('sidebarCollapse', $(`#sidebar`).hasClass('collapse') ? '' : 'collapse');
            }
            
            if(localStorage.getItem('sidebarCollapse') == 'collapse'){
                document.getElementById(`sidebar`).classList.add('collapse');
                document.getElementById(`content`).classList.add('m-collapse-menu');
            }

        </script>
            
        <?php
        if ($view['hasHeader']) {
            include dirname(__FILE__) . "/component.pageheader.php";
        } else {
            if($view['title']!= '') 
                echo '<h2 class="mb-0 h4 lh-1">'.$view['title'].'</h2>';
        }
        ?>

        <?php if(($request->get('module')== 'contas-a-receber') || ($request->get('module')== 'contas-a-pagar')){?>
            
            <div class="ms-auto d-flex align-items-center gap-1 gap-md-2">

                <div><button id="btnSearchBase" class="d-flex gap-2 align-items-center btn btn-light text-black-75 border-dark border-opacity-10 p-2 px-lg-3" type="button" data-bs-toggle="modal" data-bs-target="#filtroModal"><i class="ti fs-5 ti-filter"></i> <span class="d-none d-lg-inline">Filtrar</span> <span class="d-none d-md-inline-block opacity-50 fw-normal small">F3</span></button></div>

                <div class="d-none d-md-flex"><a class="d-flex gap-2 align-items-center btn btn-light text-black-75 border-dark border-opacity-10 p-2 px-lg-3" href="javascript:;" data-bs-toggle="modal" data-bs-target="#baixaModal"><i class="ti fs-5 ti-copy-check"></i> <span class="d-none d-lg-inline">Baixar</span></a></div>

                <?php if ($tipoConta == 2) { ?>
                <div class="d-none d-md-flex"><a class="d-flex gap-2 align-items-center btn btn-light text-black-75 border-dark border-opacity-10 p-2 px-lg-3" href="javascript:;" onclick="openModalReenvioEmail();"><i class="ti fs-5 ti-mail-forward"></i> <span class="d-none d-lg-inline">Reenviar</span></a></div>
                <?php } ?>

                <div class="d-none d-md-flex"><a href="<?= $_SERVER['REQUEST_URI'] . '&print=true' ?>" target="_blank" class="gap-2 align-items-center btn btn-light text-black-75 border-dark border-opacity-10 p-2 px-lg-3"><i class="ti fs-5 ti-printer"></i>  <span class="d-none d-md-inline-block">Imprimir</span></a></div>

                <div><button id="btnAddBase" type="button" onclick="modalForm('contas',0,'/tipo/<?= $tipoConta ?>',function(){ location.reload(); }); return false;" class="p-2 px-lg-3 btn btn-secondary fw-bold text-white">
                    <i class="ti fs-5 ti-plus"></i> <span class="d-none d-md-inline-block">Adicionar</span> <span class="d-none d-md-inline-block opacity-50 fw-normal small">F2</span>
                </button></div>


            </div>

        <?php } ?>

        

        <?php if($request->get('module')== 'extrato-banco-inter'){?>

            <div class="ms-auto d-none d-lg-flex align-items-center gap-1 gap-md-2">
                
                <?php if($request->get('action') != 'anual'){ ?>

                <div class="d-flex gap-3 justify-content-center">
                    <?=($view['anterior']!=''?'<a href="'.$view['anterior'].'"><i class="ti ti-arrow-left"></i></a>':'')?>
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false" style="margin-right: 10px;">
                        <?=$view['titulo']?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuLink">
                        <li><a class="dropdown-item" href="<?=__PATH__.$request->get('module').'/view/dia/'.date('Y-m-d')?>">Hoje</a></li>
                        <li><a class="dropdown-item" href="<?=__PATH__.$request->get('module').'/view/dia/'.date('Y-m-d',strtotime('-1 day'))?>">Ontem</a></li>
                        <li><a class="dropdown-item" href="<?=__PATH__.$request->get('module').'/view/inicio/'.date('Y-m-d',strtotime('last Sunday')).'/fim/'.date('Y-m-d',strtotime('last Sunday + 7 days'))?>">Esta semana</a></li>
                        <li><a class="dropdown-item" href="<?=__PATH__.$request->get('module').'/view/mes/'.date('Y-m')?>">Este m&ecirc;s</a></li>
                        <li><a class="dropdown-item" href="javascript:;" onclick="$('#over-periodo').modal('show');">Escolher per&iacute;odo</a></li>
                    </ul>
                    <?=($view['proximo']!=''?'<a href="'.$view['proximo'].'"><i class="ti ti-arrow-right"></i></a>':'')?>
                </div>

                <?php } ?>
            </div>

        <?php } ?>


        <?php if($request->get('module')== 'relatorio-centros'){?>

            <div class="ms-auto d-flex gap-3 align-items-center" role="tablist">
                <!--
                <select class="form-select"> 
                    <option>Selecione um centro</option>
                    <option>TODOS</option>
                    <option>OPERACIONAL</option>
                    <option>ADMINISTRATIVO</option>
                </select>
                <div class="dropdown">
                    <a href="#" role="button" data-bs-toggle="dropdown" aria-expanded="true" class="d-flex gap-2 align-items-center btn btn-light text-black-75 border-dark border-opacity-10 p-2 px-lg-3 text-nowrap dropdown-toggle">Esse mês</a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="" onclick="">Hoje</a>
                        <a class="dropdown-item" href="" onclick="">Essa semana</a>
                        <a class="dropdown-item" href="" onclick="">Esse mês</a>
                        <a class="dropdown-item" href="" onclick="">Esse ano</a>
                        <a class="dropdown-item" href="" onclick="">Todo o período</a>
                        <hr class="dropdown-divider">
                        <form id="formFiltraAtendimento" onsubmit="" class="px-3 py-1 d-flex gap-1 flex-column">
                            <small class="opacity-50 text-uppercase">Período</small>
                            <input name="" id="" type="date" class="form-control form-control-sm" value="2024-04-18">
                            <input name="" id="" type="date" class="form-control form-control-sm" value="2024-04-18">
                            <button class="btn btn-sm btn-outline-primary">Filtrar</button>
                        </form>
                    </div>
                </div> -->
                <div class="d-flex gap-3 justify-content-center d-none d-md-inline-flex">
                    <a href="<?=$url?>?ano=<?=($ano-1)?>"><i class="ti ti-arrow-left"></i></a>            
                    <div class="dropdown">
                        <a class="dropdown-toggle text-decoration-none" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                            <?=$ano?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuLink">
                            <li><a class="dropdown-item" href="javascript:;">Esse mês</a></li>
                            <li><a class="dropdown-item" href="javascript:;">Esse ano</a></li>
                        </ul>
                    </div>
                    <a href="<?=$url?>?ano=<?=($ano+1)?>"><i class="ti ti-arrow-right"></i></a>
                </div>
            </div>

        <?php } ?>


        <?php if($request->get('module')== ''){?>

            <div class="dropdown ms-4">
                <a href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" class="text-decoration-none dropdown-toggle">Hoje</a>
                <div class="dropdown-menu" style="">
                    <a class="dropdown-item" href="" onclick="">Hoje</a>
                    <a class="dropdown-item" href="" onclick="">Essa semana</a>
                    <a class="dropdown-item" href="" onclick="">Esse mês</a>
                    <a class="dropdown-item" href="" onclick="">Esse ano</a>
                    <a class="dropdown-item" href="" onclick="">Todo o período</a>
                    <hr class="dropdown-divider">
                    <form id="formFiltraAtendimento" onsubmit="" class="px-3 py-1 d-flex gap-1 flex-column">
                        <small class="opacity-50 text-uppercase">Período</small>
                        <input name="" id="" type="date" class="form-control form-control-sm" value="2024-04-18">
                        <input name="" id="" type="date" class="form-control form-control-sm" value="2024-04-18">
                        <button class="btn btn-sm btn-outline-primary">Filtrar</button>
                    </form>
                </div>
            </div>
        <?php } ?>

    </div>
</nav>
<!-- 
<nav class="position-relative d-block d-sm-none" id="navSmHeader">

    <div class="py-3 px-5">

        <div class="px-3 px-md-5 d-flex justify-content-center justify-content-sm-start gap-3">

            <a href="#" class="d-block position-absolute" aria-label="Open Collapse" data-toggle="sidebarCollapse">
                <i class="ti ti-menu-2 fs-1"></i>
            </a>

            <?php if ($view['hasHeader']) {
                include dirname(__FILE__) . "/component.pageheader.php";
            }else{

                switch ($request->get('module')) {
                    case 'configuracoes':
                        echo '<h2 class="mb-0 h4 mb-0 fw-bold text-primary">Configurações Gerais</h2>';
                        break;
                    case '':
                    case 'home':
                        echo '<h2 class="mb-0 h4 mb-0 fw-bold text-primary">Dashboard</h2>';
                        break;
                    case 'contaspr':
                        echo '<h2 class="mb-0 h4 mb-0 fw-bold text-primary">Contas</h2>';
                        break;
                    case 'contas-receber':
                        echo '<h2 class="mb-0 h4 mb-0 fw-bold text-primary">Contas a receber</h2>';
                        break;
                    case 'contas-pagar':
                        echo '<h2 class="mb-0 h4 mb-0 fw-bold text-primary">Contas a pagar</h2>';
                        break;
                    case 'movimentacao-financeira':
                        echo '<div class="d-flex gap-3">
                        <h2 class="mb-0 h4 mb-0 fw-bold text-primary">Movimentação financeira</h2>
                        </div>';
                        break;
                    case 'acompanhamento-financeiro':
                        echo '<h2 class="mb-0 h4 mb-0 fw-bold text-primary">Acompanhamento financeiro</h2>';
                        break;
                    case 'fluxo-caixa':
                        echo '<h2 class="mb-0 h4 mb-0 fw-bold text-primary">Fluxo de caixa</h2>';
                        break;
                    case 'relatorio-vendedores':
                        echo '<h2 class="mb-0 h4 mb-0 fw-bold text-primary">Relatório de vendedores</h2>';
                        break;
                    case 'pagamento-banco-inter':
                        echo '<h2 class="mb-0 h4 mb-0 fw-bold text-primary">Sistema de Pagamento Integrado ao banco inter</h2>';
                        break;
                    case 'extrato-banco-inter':
                        echo '<h2 class="mb-0 h4 mb-0 fw-bold text-primary">Extrato Banco inter</h2>';
                        break;
                    case 'financeiro':
                        echo '<h2 class="mb-0 h4 mb-0 fw-bold text-primary">Financeiro</h2>';
                        break;
                }

            } ?>
        </div>
    </div>
</nav> -->
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