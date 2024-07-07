<div class="p-0">
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="geral-tab" data-bs-toggle="tab" data-bs-target="#geral-tab-pane" type="button" role="tab" aria-controls="geral-tab-pane" aria-selected="true">Geral</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="comercial-tab" data-bs-toggle="tab" data-bs-target="#comercial-tab-pane" type="button" role="tab" aria-controls="comercial-tab-pane" aria-selected="false">Comercial</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="financeiro-tab" data-bs-toggle="tab" data-bs-target="#financeiro-tab-pane" type="button" role="tab" aria-controls="financeiro-tab-pane" aria-selected="false">Financeiro</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="zapi-tab" data-bs-toggle="tab" data-bs-target="#zapi-tab-pane" type="button" role="tab" aria-controls="zapi-tab-pane" aria-selected="false">Z-API</button>
        </li>
    </ul>
    <form class="tab-content" id="form-config-geral" method="post" onsubmit="saveConfig('geral'); return false;">
        <div class="tab-pane fade show active" id="geral-tab-pane" role="tabpanel" aria-labelledby="geral-tab" tabindex="0">
            <div class="row">
                <div class="col-sm-6 mt-3">
                    <div class="form-floating">
                        <input name="nome" id="nome" maxlength="70" type="text" placeholder="seu dado aqui" class="form-control" value="<?=$Config->get('nome')?>"/>
                        <label for="">Razão da Empresa</label>
                    </div>
                </div>
                <div class="col-sm-3 mt-3">
                    <div class="form-floating">
                        <input type="text" name="documento" id="ctdocumento" class="form-control cpfcnpj" value="<?= $Config->get('documento') ?>" placeholder="seu dado aqui">
                        <label for="ctdocumento" class="form-label">CPF/CNPJ</label>
                    </div>
                </div>
                <div class="col-sm-3 mt-3">
                    <div class="form-floating">
                        <input name="email" id="email" type="email" placeholder="seu dado aqui" class="form-control" value="<?=$Config->get('email')?>"/>
                        <label for="">E-mail</label>
                    </div>
                </div>
                <div class="col-xl-3 col-md-4 mt-3">
                    <div class="form-floating">
                        <input type="text" name="cep" id="ctcep" class="form-control cep" value="<?= $Config->get('cep') ?>" placeholder="seu dado aqui">
                        <label for="ctcep" class="form-label">CEP</label>
                    </div>
                </div>

                <div class="col-xl-7 col-md-5 mt-3">
                    <div class="form-floating">
                        <input type="text" name="endereco" id="ctendereco" class="form-control" value="<?= $Config->get('endereco') ?>" placeholder="seu dado aqui">
                        <label for="ctendereco" class="form-label">Rua</label>
                    </div>
                </div>

                <div class="col-xl-2 col-md-3 mt-3">
                    <div class="form-floating">
                        <input type="number" name="numero" id="ctnumero" class="form-control" value="<?= $Config->get('numero') ?>" placeholder="seu dado aqui">
                        <label for="ctnumero" class="form-label">N&uacute;mero</label>
                    </div>
                </div>

                <div class="col-xl-3 col-md-4 mt-3">
                    <div class="form-floating">
                        <input type="text" name="complemento" id="ctcomplemento" class="form-control" value="<?= $Config->get('complemento') ?>" placeholder="seu dado aqui">
                        <label for="ctcomplemento" class="form-label">Complemento</label>
                    </div>
                </div>

                <div class="col-xl-3 col-md-8 mt-3">
                    <div class="form-floating">
                        <input type="text" name="bairro" id="ctbairro" class="form-control" value="<?= $Config->get('bairro') ?>" placeholder="seu dado aqui">
                        <label for="ctbairro" class="form-label">Bairro</label>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mt-3">
                    <div class="form-floating">
                        <input type="text" name="cidade" id="ctcidade" class="form-control" value="<?= $Config->get('cidade') ?>" placeholder="seu dado aqui">
                        <label for="ctcidade" class="form-label">Cidade</label>
                    </div>
                </div>

                <div class="col-sm-3 mt-3">
                    <div class="form-floating">
                        <select class="form-select" name="estado" id="ctestado">
                            <?php foreach ($GLOBALS['Estados'] as $key => $value) { ?>
                                <option value="<?= $key ?>" <?= ($Config->get('estado') == $key ? " selected " : "") ?>><?= $value ?></option>
                            <?php } ?>
                        </select>
                        <label for="ctestado" class="form-label">Estado</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="comercial-tab-pane" role="tabpanel" aria-labelledby="comercial-tab" tabindex="0">
            <div class="row">
                <div class="col-sm-6 mt-3">
                    <label for="">Texto Padr&atilde;o para proposta comercial</label>
                    <div class="form-floating">
                        <textarea name="proposta"  id="proposta" type="text" placeholder="seu dado aqui" class="form-control ckeditor"><?=$Config->get('proposta')?></textarea>
                    </div>
                </div>
                <div class="col-sm-6 mt-3">
                    <label for="">Texto Padr&atilde;o para follow-up</label>
                    <div class="form-floating">
                        <textarea name="followup"  id="followup" type="text" placeholder="seu dado aqui" class="form-control ckeditor"><?=$Config->get('followup')?></textarea>
                    </div>
                </div>
                <div class="col-sm-12 mt-3">
                    <label for="">Contrato</label>
                    <div class="form-floating">
                        <textarea name="contrato"  id="contrato" type="text" placeholder="seu dado aqui" class="form-control ckeditor"><?=$Config->get('contrato')?></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="financeiro-tab-pane" role="tabpanel" aria-labelledby="financeiro-tab" tabindex="0">
            <div class="row">
                <div class="col-sm-6 mt-3">
                    <div class="input-group">
                        <div class="form-floating">
                            <input name="juros_parcelamento" id="juros_parcelamento" type="text" placeholder="seu dado aqui" class="form-control money text-end" value="<?=$Config->get('juros_parcelamento')?>"/>
                            <label for="">Juros de Parcelamento</label>
                        </div>
                        <span class="input-group-text" id="basic-addon2">%</span>
                    </div>
                </div>
                <div class="col-sm-6 mt-3">
                    <div class="input-group">
                        <div class="form-floating">
                            <input name="multa_atraso" id="multa_atraso" type="text" placeholder="seu dado aqui" class="form-control money text-end" value="<?=$Config->get('multa_atraso')?>"/>
                            <label for="">Multa por Atraso</label>
                        </div>
                        <span class="input-group-text" id="basic-addon2">%</span>
                    </div>
                </div>
                <div class="col-sm-6 mt-3">
                    <div class="input-group">
                        <div class="form-floating">
                            <input name="juros_atraso" id="juros_atraso" type="text" placeholder="seu dado aqui" class="form-control money text-end" value="<?=$Config->get('juros_atraso')?>"/>
                            <label for="">Juros por atraso (ao dia)</label>
                        </div>
                        <span class="input-group-text" id="basic-addon2">%</span>
                    </div>
                </div>
                <div class="col-sm-6 mt-3">
                    <div class="input-group">
                        <div class="form-floating">
                            <input name="faturas_intervalo" id="faturas_intervalo" type="text" placeholder="seu dado aqui" class="form-control money text-end" value="<?=$Config->get('faturas_intervalo')?>"/>
                            <label for="">Antecedência geração faturas</label>
                        </div>
                        <span class="input-group-text" id="basic-addon2">%</span>
                    </div>
                </div>
                <div class="col-sm-12 mt-3">
                    <h4>Mandar avisos de fatura por email</h4>
                </div>
                <div class="col-sm-3 mb-3">
                    <label for="">Quando criada</label><br>
                    <div class="btn-group" data-bs-toggle="buttons" >
                        <label class="btn btn-primary <?=($Config->get('aviso_new')==1?' active':'')?>"><input type="radio" name="aviso_new" id="avisocriado_option1"<?=($Config->get('aviso_new')==1?' checked':'')?> value="1"/> Sim</label>
                        <label class="btn btn-primary <?=($Config->get('aviso_new')!=1?' active':'')?>"><input type="radio" name="aviso_new" id="avisocriado_option2" <?=($Config->get('aviso_new')!=1?' checked':'')?> value="0"/> N&atilde;o</label>
                    </div>
                </div>
                <div class="col-sm-3 mb-3">
                    <label for="">2 dias antes</label><br>
                    <div class="btn-group" data-bs-toggle="buttons">
                        <label class="btn btn-primary <?=($Config->get('aviso_near')==1?' active':'')?>"><input type="radio" name="aviso_near" id="2diasantes_option1"<?=($Config->get('aviso_near')==1?' checked':'')?> value="1" /> Sim</label>
                        <label class="btn btn-primary <?=($Config->get('aviso_near')!=1?' active':'')?>"><input type="radio" name="aviso_near" id="2diasantes_option2" <?=($Config->get('aviso_near')!=1?' checked':'')?> value="0" /> N&atilde;o</label>
                    </div>
                </div>
                <div class="col-sm-3 mb-3">
                    <label for="">No dia</label><br>
                    <div class="btn-group" data-bs-toggle="buttons">
                        <label class="btn btn-primary <?=($Config->get('aviso_inday')==1?' active':'')?>"><input type="radio" name="aviso_inday" id="nodia_option1"<?=($Config->get('aviso_inday')==1?' checked':'')?> value="1" /> Sim</label>
                        <label class="btn btn-primary <?=($Config->get('aviso_inday')!=1?' active':'')?>"><input type="radio" name="aviso_inday" id="nodia_option2" <?=($Config->get('aviso_inday')!=1?' checked':'')?> value="0" /> N&atilde;o</label>
                    </div>
                </div>
                <div class="col-sm-3 mb-3">
                    <label for="">Em atraso</label><br>
                    <div class="btn-group" data-bs-toggle="buttons">
                        <label class="btn btn-primary <?=($Config->get('aviso_late')==1?' active':'')?>"><input type="radio" name="aviso_late" id="ematraso_option1"<?=($Config->get('aviso_late')==1?' checked':'')?> value="1" /> Sim</label>
                        <label class="btn btn-primary <?=($Config->get('aviso_late')!=1?' active':'')?>"><input type="radio" name="aviso_late" id="ematraso_option2" <?=($Config->get('aviso_late')!=1?' checked':'')?> value="0" /> N&atilde;o</label>
                    </div>
                </div>

                <div class="col-sm-4 mt-3">
                    <div class="input-group">
                        <div class="form-floating">
                            <input name="aviso_late_days" id="aviso_late_days" type="text" placeholder="seu dado aqui" class="form-control text-end" value="<?= $Config->get('aviso_late_days') ?>"/>
                            <label for="">Avisar atraso a cada</label>
                        </div>
                        <span class="input-group-text" id="basic-addon2">dias</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="zapi-tab-pane" role="tabpanel" aria-labelledby="zapi-tab" tabindex="0">
            <div class="row">

                <div class="col-sm-4 mt-3">
                    <div class="form-floating">
                        <input type="text" name="zapi_id" class="form-control" placeholder="seu dado aqui" value="<?= $Config->get('zapi_id') ?>">
                        <label for="">ID da inst&acirc;ncia</label>
                    </div>
                </div>
            
                <div class="col-sm-4 mt-3">
                    <div class="form-floating">
                        <input type="text" name="zapi_token" class="form-control" placeholder="seu dado aqui" value="<?= $Config->get('zapi_token') ?>">
                        <label for="">Token da inst&acirc;ncia</label>
                    </div>
                </div>
                <div class="col-sm-4 mt-3">
                    <div class="form-floating">
                        <input type="text" name="zapi_tokenseg" class="form-control" placeholder="seu dado aqui" value="<?= $Config->get('zapi_tokenseg')?>">
                        <label for="">Token de Seguran&ccedil;a</label>
                    </div>
                </div>

                <div class="col-sm-4 mt-3">
                    <div class="panel panel-default">
                        <div class="panel-heading">Integra&ccedil;&atilde;o Z-api</div>
                        <div class="panel-body">Situa&ccedil;&atilde;o: 
                        <?php
                        if($Config->get('zapi_id') == '' || $Config->get('zapi_token') == ''){
                            echo '<strong class="text-danger">N&atilde;o Configurado</strong>';
                        }else{
                            $zapi = new ZApi($Config->get('zapi_id'), $Config->get('zapi_token'), $Config->get('zapi_tokenseg'));
                            if($zapi->isConencted()){
                                echo '
                                <strong class="text-success">Conectado</strong> <a href="'.__PATH__.$request->get('module').'/desconectar-zapi">Desconectar</a>';
                            }else{
                                echo '
                                <strong class="text-danger">Desconectado</strong>
                                <p class="small">Leia o QRCode abaixo:</p>
                                <img src="'.$zapi->qrcodeImage().'" width="100%" />
                                <p class="small">Ap&oacute;s realizar a leitura do QRCode basta atualizar a p&aacute;gina para verificar se a integra&ccedil;&atilde;o funcionou corretamente.</p>
                                ';
                            }
                        }
                        ?>
                        </div>
                    </div>
                </div>

                <div class="col-sm-8 mt-3">
                    <div class="form-floating">
                        <textarea style="height: 80px" name="mensagem_aniversario" class="form-control"><?=$Config->get('mensagem_aniversario')?></textarea>
                        <label for="">Mensagem Aniversariante</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end mt-3">
            <button type="submit" class="btn btn-success">Salvar</button>
        </div>
    </form>
</div>