<nav class="position-fixed bottom-0 bg-dark text-white w-100 d-block d-sm-none" style="z-index: 5;" id="navFooter">
    <div class="py-3 px-5">
        <div class="px-md-5 d-flex justify-content-between gap-3">

            <?php if ($view['hasHeader']) { ?>
                
                <button class="btn btn-danger rounded-pill btn-delete-selected" data-toggle="tooltip" onclick="deletaRegitrosSelecionados('<?= $view['modulo'] ?>');" data-placement="top" title="Apagar selecionado(s)" style="display:none;" >
                    <i class="ti ti-trash"></i> <span class="d-none d-md-inline-block">Apagar selecionados</span>
                </button>
                <?php if($view['list-filter'] != ''){ ?>
                <button type="button" onclick="$('#modalFilter').modal('show')" class="btn btn-dark rounded-pill">
                    <i class="ti ti-filter"></i> <span class="d-none d-md-inline-block">Filtro</span>
                </button>
                <?php } ?>
                <button type="button" onclick="modalForm('<?=$view['modulo']?>',0,'',function(){ tableList('<?=$view['modulo']?>', window.location.search.substr(1), 'resultados', false); }); return false;" class="btn btn-dark text-white rounded-pill">
                    <i class="ti ti-plus"></i> <span class="d-none d-md-inline-block">Adicionar</span>
                </button>

                <div class="d-flex align-items-center justify-content-center justify-content-md-end">
                <div class="btn-group dropup">
                    <a href="javascript:;" class="text-decoration-none dropdown-toggle text-white pe-3" data-bs-toggle="dropdown" data-target="#dropdownList" aria-label="Open User Dropdown" aria-expanded="false">
                        <?php if($objSession->get('img') != ''){ ?>
                            
                            <div class="user-profile">
                                <img src="<?=$objSession->getImage()?>" alt="perfil" class="w-100 h-100">
                            </div>

                        <?php }else{ ?>

                            <i class="ti ti-user-circle fs-1"></i>

                        <?php } ?>
                    </a>
                    <ul class="dropdown-menu bg-dark text-white text-center">
                        <li><a class="dropdown-item p-3 text-white">Bem vindo, <br><strong><?=$objSession->get('nome')?></strong></a></li>
                        <li>
                            <a class="dropdown-item p-3 text-white d-flex text-white align-items-center justify-content-center gap-2" target="_blank" href="javascript:;" aria-label="ver perfil" data-bs-toggle="modal" data-bs-target="#perfil">
                                <i class="ti ti-user"></i>
                                Ver perfil
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex text-white align-items-center justify-content-center gap-2" href="javascript:;" aria-label="Sair" onclick="logout()">
                                <i class="ti ti-logout"></i>
                                Sair
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <?php }else if($request->get('module') == '' || $request->get('module') == 'home'){?>

                <a class="btn btn-dark" target="_blank" href="javascript:;" aria-label="ver perfil" data-bs-toggle="modal" data-bs-target="#perfil"><i class="ti ti-user-circle"></i> Ver perfil</a>

                <a class="btn btn-dark" href="javascript:;" aria-label="Sair" onclick="logout()">
                    <i class="ti ti-logout"></i>
                    Sair
                </a>

            <?php } ?>
        </div>
    </div>
</nav>