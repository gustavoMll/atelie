<script>
    function searchMenu(text){
        if(text == ''){
            $(`#menu-sidebar .dropdown-menu`).removeClass('show');
        }else{
            $(`#menu-sidebar .dropdown-menu`).addClass('show');
        }
        
        $('#menu-sidebar li').each(function(i, item){
            if (removeDiacritics($(item).text()).toUpperCase().indexOf(removeDiacritics(text.toUpperCase())) > -1) {
                $(item).css('display','');
            } else {
                $(item).css('display','none');
            }
        });
    }
</script>
    
<nav id="sidebar" class="d-flex flex-column h-100 position-fixed start-0 scroll-xs-y">

    <div class="d-flex align-items-center p-3" id="headerSidebar">

        <div class="d-flex align-items-center gap-2 me-auto" id="brandMenu" href="<?= __PATH__ ?>">
            <a href="#" data-toggle="sidebarCollapse" class="d-inline-block d-md-none text-white lh-1"><i class="ti ti-menu-2 fs-1"></i></a>
            <a href="" class="h4 fw-bold lh-1 m-0 text-white text-decoration-none"><?=$Config->get('nome') != '' ? $Config->get('nome') : 'Sistema'?></a>
        </div>
        
        <a href="javascript:;" aria-label="ver perfil" data-bs-toggle="modal" data-bs-target="#perfil" class="ratio ratio-1x1 rounded-circle" style="width:32px"><img src="<?=($objSession->get('img') != '' ? $objSession->getImage() : __BASEPATH__.'img/default-image.png')?>" alt="perfil" class="img-fluid"></a>
           
    </div>

    <div class="px-3 pb-3" id="searchMenu">
        <input type="text" class="form-control" id="search-sidebar" placeholder="Pesquisar no menu" onkeyup="searchMenu(this.value);" />
    </div>

    <ul class="dropdown-menu border-0 show w-100 bg-transparent rounded-0 d-flex flex-column flex-fill overflow-auto" id="menu-sidebar">
        <?php include dirname(__FILE__) . "/component.menu.php"; ?>
        <li class="dropdown-item"><a href="<?=__PATH__?>" class="d-flex gap-2" onclick="logout()"><i class="ti ti-logout fs-3"></i> <span class="me-auto">Sair</a></li>
    </ul>

</nav>