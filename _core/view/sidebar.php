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
            <a href="" class="h4 fw-bold lh-1 m-0 text-white text-decoration-none"><?=$Config->get('nome') != '' ? $Config->get('nome') : 'Sistema'?></a>
        </div>
           
    </div>

    <div class="px-3 pb-3" id="searchMenu">
        <input type="text" class="form-control" id="search-sidebar" placeholder="Pesquisar no menu" onkeyup="searchMenu(this.value);" />
    </div>

    <ul class="dropdown-menu border-0 show w-100 bg-transparent rounded-0 d-flex flex-column flex-fill overflow-auto" id="menu-sidebar">
        <?php include dirname(__FILE__) . "/component.menu.php"; ?>
        <li class="dropdown-item"><a class="d-flex gap-2" onclick="logout()"><i class="ti ti-logout fs-3"></i> <span class="me-auto">Sair</a></li>
    </ul>

</nav>