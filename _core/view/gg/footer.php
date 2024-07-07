<!-- <nav class="position-fixed bottom-0 bg-dark text-white w-100 d-block d-sm-none" style="z-index: 5;" id="navFooter">
    <div class="py-3 px-5">
        <div class="px-md-5 d-flex justify-content-between gap-3">

        <?php if ($view['hasHeader']) { ?>
                
                <button class="btn btn-danger rounded-pill btn-delete-selected" data-bs-toggle="tooltip" onclick="deletaRegitrosSelecionados('<?= $view['modulo'] ?>');" data-bs-placement="right" title="Apagar selecionado(s)" style="display:none;" >
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
                    <a href="javascript:;" class="dropdown-toggle text-white pe-3" data-bs-toggle="dropdown" data-target="#dropdownList" aria-label="Open User Dropdown" aria-expanded="false">
                        <?php if($objSession->get('img') != ''){ ?>
                            
                            <div class="user-profile">
                                <img src="<?=$objSession->getImage()?>" alt="perfil" class="img-fluid">
                            </div>

                        <?php }else{ ?>

                            <i class="ti ti-user-circle fs-1 pe-1"></i>

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
                                <i class="ti ti-door-exit"></i>
                                Sair
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <?php }else{?>

                <a class="btn btn-dark" target="_blank" href="javascript:;" aria-label="ver perfil" data-bs-toggle="modal" data-bs-target="#perfil"><i class="ti ti-user-circle"></i> Ver perfil</a>

                <?php if($request->get('module') == 'movimentacao-financeira'){ ?>
                    <button type="button" onclick="modalForm(`movimentacoes`,0); return false;" class="btn btn-dark text-white rounded-pill">
                        <i class="ti ti-plus"></i> <span class="d-none d-md-inline-block">Adicionar</span>
                    </button>
                <?php } ?>

                <a class="btn btn-dark" href="javascript:;" aria-label="Sair" onclick="logout()">
                    <i class="ti ti-door-exit"></i>
                    Sair
                </a>

            <?php } ?>
        </div>
    </div>
</nav> -->

<?php if($request->get('module')=='propostas'){ ?>
<!-- Modal -->
<div class="modal fade" id="over-followup" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="frm-followup" method="post" onsubmit="doFollowUp(); return false;">
        <div class="modal-header">
            <h5 class="modal-title small text-uppercase" id="myModalLabel">Bora dar FollowUP!!!</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-sm-6 mb-3 required">
                <div class="form-floating">
                    <input type="text" name="para" id="para" class="form-control" placeholder="Digite o remetente" value="" />
                    <label for="para">Para</label>
                </div>
            </div>
            <div class="col-sm-6 mb-3 required">
                <div class="form-floating">
                    <input type="text" name="assunto" id="assunto" placeholder="Digite o assunto" class="form-control"/>
                    <label for="assunto">Assunto do Email</label>
                </div>
            </div>
            <div class="form-group col-sm-12 mb-3 required">
              <label for="mensagem">Texto do Email</label>
              <textarea name="mensagem" id="mensagem" class="form-control ckeditor"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-dark" data-bs-dismiss="modal"><i class="ti ti-x"></i> Fechar</button>
            <button class="btn btn-secondary text-white"><i class="ti ti-mail-forward"></i> Enviar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php }