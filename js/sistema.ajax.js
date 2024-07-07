function newsList(id, page) {
   
    $('#morenews').html('<li>Aguarde, buscando...</li>');
    var url = __PATH__ + 'ajax/news-list/id/' + id + '/page/' + page;
    $.ajax({
        url: url,
        type: 'GET',
        dataType: "json",
        success: function(data, textStatus, jqXHR) {
            if (data['ok']) {
                $('#morenews').html(data['data']);
            } else {
                $('#morenews').html(data['data']);
            }
        },
        error: function(jqXHR, textStatus, errorThrown)
        {
            $('#morenews').html('<li>JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown+'</li>');
        }
    });
}

function blockUi(){
    $.blockUI({ message: '<img src="//www.doren.com.br/css/img/loading.gif" width="20px" /> Aguarde...',
        css: {
            border: 'none',
            padding: '15px',
            backgroundColor: '#000',
            '-webkit-border-radius': '10px',
            '-moz-border-radius': '10px',
            opacity: .5,
            color: '#fff'
        }
    });
}

function unblockUi(){
    $.unblockUI();
}

function blockOnSubmit(){
    $.blockUI({ message: '<img src="//www.doren.com.br/css/img/loading.gif" width="20px" /> <span id="progressPercent">0%</span>',
        css: {
            border: 'none',
            padding: '15px',
            backgroundColor: '#000',
            '-webkit-border-radius': '10px',
            '-moz-border-radius': '10px',
            opacity: .5,
            color: '#fff'
        }
    });
}


function showErrorAlert(title, message){
    BootstrapDialog.show({
        title: title,
        message: message,
        type: BootstrapDialog.TYPE_DANGER
    });
}

function showAddForm(module, callback) {
    callback = (callback || '');
    openPageModal(__PATH__ + 'ajax/add/class/' + module, module, '', callback);
}

function showEditForm(module, id, callback) {
    callback = (callback || '');
    openPageModal(__PATH__ + 'ajax/edit/class/' + module + '/id/' + id, module, id, callback);
}

function openPageModal(url, module, id, callback) {
    blockUi();
    var d = new Date();
    var n = d.getTime();

    callback = (callback || '');
    var $message = $('<div class="row"></div>');
    var $title = 'Formul&aacute;rio';
    var $permitido = false;
    $.ajax({
        url: url,
        type: 'GET',
        dataType: "json",
        success: function(data) {
            if (data['ok']) {
                $message.append(data['data']);
                $title = data['title'];
                $permitido = data['permitido'];
            } else {
                $message.append(data['data']);
            }

        },
        error: function(jqXHR, textStatus, errorThrown)
        {
            showErrorAlert('Erro no salvamento do registro', 'JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown);
            $message.append('JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown);
        }
    }).done(function() {
        var bdForm = null;
        BootstrapDialog.show({
            closable: true,
            closeByBackdrop: false,
            closeByKeyboard: false,
            title: $title,
            onshow: function(dialog) {
                dialog.getModalDialog().addClass('modal-lg');

            },
            onshown: function(dialog) {
                if(id=='') $('#btnDel'+n).prop('disabled',true);
                fieldFunctions();
                CKEDITOR.config.allowedContent = true;
                dialog.getModalContent().find('.ckeditor').each(function(key, textarea) {
                    var editor = CKEDITOR.replace(textarea.id);
                });
                unblockUi();
                dialog.getModalContent().find("form:not(.filter) :input:visible:enabled:first").focus();
                bdForm = dialog.getModalContent().find('form:first');

                
            },
            onhide: function(dialog) {
                dialog.getModalContent().find('.ckeditor').each(function(key, textarea) {
                    CKEDITOR.instances[textarea.id].destroy()
                });
            },
            type: 'type-default',
            message: $message,
            buttons: [{
                    label: 'Salvar',
                    cssClass: 'btn-success pull-right',
                    action: function(dialog) {
                        if($permitido){
                            var formId = bdForm.attr('id');
                            var $button = this; // 'this' here is a jQuery object that wrapping the <button> DOM element.

                            if (validaForm($$(formId))) {
                                for (instance in CKEDITOR.instances)
                                    CKEDITOR.instances[instance].updateElement();

                                var v_url = __PATH__ + 'ajax/save/class/' + module + '/id/' + id;

                                $button.disable();
                                $button.spin();
                                dialog.setClosable(false);

                                $('#' + formId).ajaxSubmit({
                                    url: v_url,
                                    type: "POST",
                                    dataType: "json",
                                    beforeSend: function() {
                                        blockOnSubmit();
                                        $('#progressPercent').html('0%');
                                    },
                                    uploadProgress: function(event, position, total, percentComplete) {
                                        $('#progressPercent').html(percentComplete + '%');
                                    },
                                    success: function(data, textStatus, jqXHR)
                                    {
                                        unblockUi();
                                        if (data['ok']) {
                                            dialog.close();

                                            if(data['callback'] !== undefined){
                                                callback = data['callback'];
                                            }

                                            if(callback==''){
                                                reloadWithParameter('msg', 'success');
                                            }else{
                                                tAlert(data['data']);
                                                eval(callback);
                                            }

                                        } else {

                                            showErrorAlert('Erro no salvamento do registro', data['data']);
                                        }
                                        $button.enable();
                                        $button.stopSpin();
                                        dialog.setClosable(true);
                                    },
                                    error: function(jqXHR, textStatus, errorThrown)
                                    {
                                        unblockUi();
                                        showErrorAlert('Erro no salvamento do registro', 'JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown);
                                        $button.enable();
                                        $button.stopSpin();
                                        dialog.setClosable(true);
                                    }
                                });
                            }

                        }else{
                            showErrorAlert('Error','Desculpe, voc&ecirc; n&atilde;o possui permiss&atilde;o realizar esta opera&ccedil;&atilde;o.');
                        }
                    }
                },{
                    label: 'Cancelar',
                    cssClass: 'btn-link pull-right',
                    action: function(dialog) {
                        dialog.close();
                    }
                },{
                    label: 'Apagar',
                    id: 'btnDel'+n,
                    cssClass: 'btn-link',
                    action: function(dialog) {
                        delFormAction(module, id, callback, true);
                    }
                }]
        });
    });
}

function saveFormAction(module, id, form_id) {
    var formId = (form_id || 'form-dados');
    if (validaForm($$(formId))) {
        $("#btnSave").html('<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Aguarde...');
        $("#btnSave").attr("disabled", true);

        for (instance in CKEDITOR.instances)
            CKEDITOR.instances[instance].updateElement();

        var v_url = __PATH__ + 'ajax/save/class/' + module + '/id/' + id;

        $('#' + formId).ajaxSubmit({
            url: v_url,
            type: "POST",
            dataType: "json",
            beforeSend: function() {
                blockOnSubmit();
                $('#progressPercent').html('0%');
            },
            uploadProgress: function(event, position, total, percentComplete) {
                $('#progressPercent').html(percentComplete + '%');
            },
            success: function(data, textStatus, jqXHR)
            {
                unblockUi();
                if (data['ok']) {
                    $(".close").click();
                    if (formId == 'form-perfil') {
                        tAlert('<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert">&times;</a>Perfil atualizado com sucesso!</div>');
                    } else {
                        reloadWithParameter('msg', 'success');
                    }

                } else {
                    showErrorAlert('Erro no salvamento do registro', data['data']);
                    
                }
                $("#btnSave").removeAttr('disabled');
                $("#btnSave").html('Salvar');

            },
            error: function(jqXHR, textStatus, errorThrown)
            {
                unblockUi();
                showErrorAlert('Erro no salvamento do registro', 'JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown);
                
                $("#btnSave").removeAttr('disabled');
                $("#btnSave").html('Salvar');
            }
        });
    }
    return false;
}

function savePerfil(id) {  
    var formId = 'form-perfil';
    if (validaForm($$(formId))) {
        $("#btnSave").html('<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Aguarde...');
        $("#btnSave").attr("disabled", true);

        var v_url = __PATH__ + 'ajax/save-perfil/id/' + id;

        $('#' + formId).ajaxSubmit({
            url: v_url,
            type: "POST",
            dataType: "json",
            beforeSend: function() {
                blockOnSubmit();
                $('#progressPercent').html('0%');
            },
            uploadProgress: function(event, position, total, percentComplete) {
                $('#progressPercent').html(percentComplete + '%');
            },
            success: function(data, textStatus, jqXHR)
            {
                unblockUi();
                if (data['ok']) {
                    $(".close").click();
                    tAlert('<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert">&times;</a>Perfil atualizado com sucesso!</div>');
                } else {
                    showErrorAlert('Erro no salvamento do registro', data['data']);
                    
                }
                $("#btnSave").removeAttr('disabled');
                $("#btnSave").html('Salvar');

            },
            error: function(jqXHR, textStatus, errorThrown)
            {
                unblockUi();
                showErrorAlert('Erro no salvamento do registro', 'JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown);
                
                $("#btnSave").removeAttr('disabled');
                $("#btnSave").html('Salvar');
            }
        });
    }
    return false;
}

function saveFormXml(id) {
    var formId = 'form-config-' + id;
    if (validaForm($$(formId))) {
        $("#btnSave-" + id).html('<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Aguarde...');
        $("#btnSave-" + id).attr("disabled", true);

        var v_url = __PATH__ + 'ajax/save-xml/';

        $('#' + formId).ajaxSubmit({
            url: v_url,
            type: "POST",
            dataType: "json",
            beforeSend: function() {
                blockOnSubmit();
                $('#progressPercent').html('0%');
            },
            uploadProgress: function(event, position, total, percentComplete) {
                $('#progressPercent').html(percentComplete + '%');
            },
            success: function(data, textStatus, jqXHR)
            {
                unblockUi();

                if (data['ok']) {
                    $("#btnClose-" + id).click();
                    tAlert(data['data']);

                } else {

                    showErrorAlert('Erro no salvamento do registro', data['data']);
                    
                }
                $("#btnSave-" + id).removeAttr('disabled');
                $("#btnSave-" + id).html('Salvar');

            },
            error: function(jqXHR, textStatus, errorThrown)
            {
                unblockUi();
                showErrorAlert('Ocorreu um erro', 'JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown);
                
                $("#btnSave-" + id).removeAttr('disabled');
                $("#btnSave-" + id).html('Salvar');

            }
        });
    }
    return false;
}

function delFormAction(module, id, callback, closeDialog) {
    callback = (callback || '');
    closeDialog = (closeDialog || true);
    BootstrapDialog.show({
        title: 'Confirma&ccedil;&atilde;o',
        message: 'Deseja realmente excluir o(s) registro(s) selecionado(s)?',
        buttons: [{
                label: 'N&atilde;o',
                action: function(dialog) {
                    dialog.close();
                }
            }, {
                label: 'Sim',
                cssClass: 'btn-primary',
                action: function(dialog) {
                    var $button = this; // 'this' here is a jQuery object that wrapping the <button> DOM element.
                    $button.disable();
                    $button.spin();
                    dialog.setClosable(false);

                    var v_url = __PATH__ + 'ajax/del/class/' + module + '/id/' + id;
                    $.ajax({
                        url: v_url,
                        type: "POST",
                        dataType: "json",
                        success: function(data, textStatus, jqXHR)
                        {
                            if (data['ok']) {
                                if(closeDialog) 
                                    dialog.close();
                                if(callback==''){
                                    reloadWithParameter('msg', 'dsuccess');
                                }else{
                                    tAlert(data['data']);
                                    eval(callback);
                                }
                                
                            } else {
                                showErrorAlert('Erro na exclus&atilde;o do registro', data['data']);
                            }
                            $button.enable();
                            $button.stopSpin();
                            dialog.setClosable(true);
                        },
                        error: function(jqXHR, textStatus, errorThrown)
                        {
                            showErrorAlert('Ocorreu um erro.', 'JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown);
                            $button.enable();
                            $button.stopSpin();
                            dialog.setClosable(true);
                        }
                    });

                    


                }
            }]
    });

    return false;
}


function changeStatus(module, id, to) {
    var nHtml = '<a href="javascript:;" onclick="javascript: changeStatus(\'' + module + '\',' + id + ', ' + (to == 1 ? '0' : '1') + ')"><span class="glyphicon glyphicon-ok-sign ' + (to == 1 ? '' : 'd') + 'pub" title="' + (to == 1 ? 'Desativar' : 'Ativar') + '"></span></a>';
    var url = __PATH__ + 'ajax/change-active/class/' + module + '/id/' + id + '/to/' + to;
    $.ajax({
        url: url,
        type: 'GET',
        dataType: "json",
        success: function(data, textStatus, jqXHR) {
            if (data['ok']) {
                $('#status' + id).html(nHtml);
            } else {
                tAlert(data['data'], 10000);
            }
        },
        error: function(jqXHR, textStatus, errorThrown)
        {
            showErrorAlert('Ocorreu um erro.', 'JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown);
        }
    });
}


function deleteImage(module, id) {
    BootstrapDialog.show({
        title: 'Confirma&ccedil;&atilde;o',
        message: 'Deseja realmente excluir a imagem?',
        buttons: [{
                label: 'N&atilde;o',
                action: function(dialog) {
                    dialog.close();
                }
            }, {
                label: 'Sim',
                cssClass: 'btn-primary',
                action: function(dialog) {
                    var $button = this; // 'this' here is a jQuery object that wrapping the <button> DOM element.
                    $button.disable();
                    $button.spin();
                    dialog.setClosable(false);

                    var url = __PATH__ + 'ajax/delete-image/class/' + module + '/id/' + id;
                    var htmlb = $('#link_del').html();
                    $('#link_del').html('Aguarde, deletando imagem...');
                    $.ajax({
                        url: url,
                        type: 'GET',
                        dataType: "json",
                        success: function(data, textStatus, jqXHR) {
                            if (data['ok']) {
                                dialog.close();
                                $('#imagem_atual').fadeOut('slow');
                            } else {
                                $button.enable();
                                $button.stopSpin();
                                dialog.setClosable(true);
                            }
                            tAlert(data['data']);

                        },
                        error: function(jqXHR, textStatus, errorThrown)
                        {
                            showErrorAlert('Ocorreu um erro.', 'JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown);

                            $button.enable();
                            $button.stopSpin();
                            dialog.setClosable(true);
                        }
                    });

                }
            }]
    });

    return false;
}

function deleteFile(file, retorno) {
    var retorno = retorno || 'arquivo_atual';
    BootstrapDialog.show({
        title: 'Confirma&ccedil;&atilde;o',
        message: 'Deseja realmente excluir o arquivo?',
        buttons: [{
                label: 'N&atilde;o',
                action: function(dialog) {
                    dialog.close();
                }
            }, {
                label: 'Sim',
                cssClass: 'btn-primary',
                action: function(dialog) {
                    var $button = this; // 'this' here is a jQuery object that wrapping the <button> DOM element.
                    $button.disable();
                    $button.spin();
                    dialog.setClosable(false);
                    
                    var url = __PATH__ + 'ajax/delete-file/file/' + file;
                    var htmlb = $('#link_del').html();
                    $('#link_del').html('Aguarde, deletando arquivo...');
                    $.ajax({
                        url: url,
                        type: 'GET',
                        dataType: "json",
                        success: function(data, textStatus, jqXHR) {
                            if (data['ok']) {
                                dialog.close();
                                $('#retorno').fadeOut('slow');
                            } else {
                                $button.enable();
                                $button.stopSpin();
                                dialog.setClosable(true);
                                $('#link_del').html(htmlb);
                            }
                            tAlert(data['data']);

                        },
                        error: function(jqXHR, textStatus, errorThrown)
                        {
                            showErrorAlert('Ocorreu um erro.', 'JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown);

                            $button.enable();
                            $button.stopSpin();
                            dialog.setClosable(true);
                            $('#link_del').html(htmlb);
                        }
                    });

                }
            }]
    });

    return false;
}


function openFormEditEmail(user, uso, quota, perc) {
    $('#form-edit').find("input, textarea").val("");
    $('#euser').val(user);
    $('#equota').val(quota);
    $('#equota_b').val(quota);
    var icon = (perc < 50 ? '' : (perc < 80 ? 'progress-bar-warning' : 'progress-bar-danger'));
    var str = '';
    str += '<div class="panel-heading">Uso da cota deste e-mail<small class="pull-right"><strong>' + uso + ' MB de ' + (quota > 0 ? quota + ' MB' : 'Ilimitado') + '</strong></small></div>';
    str += '<div class="panel-body">';
    str += '<div class="progress progress-lg">';
    str += '<div class="progress-bar ' + icon + '" role="progressbar" aria-valuenow="' + perc + '" aria-valuemin="0" aria-valuemax="100" style="width: ' + perc + '%">' + uso + ' MB</div>';
    str += '</div>';
    str += '</div>';
    $('#usoEmail').html(str);
    $('#edit').modal('toggle');

}

function saveFormEmail(id) {
    var formId = 'form-' + id;
    if (validaForm($$(formId))) {
        $("#btnSave-" + id).html('<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Aguarde...');
        $("#btnSave-" + id).attr("disabled", true);

        var v_url = __PATH__ + 'ajax/save-email/tipo/' + id;

        $('#' + formId).ajaxSubmit({
            url: v_url,
            type: "POST",
            dataType: "json",
            beforeSend: function() {
                blockOnSubmit();
                $('#progressPercent').html('0%');
            },
            uploadProgress: function(event, position, total, percentComplete) {
                $('#progressPercent').html(percentComplete + '%');
            },
            success: function(data, textStatus, jqXHR)
            {
                unblockUi();

                if (data['ok']) {
                    $("#btnClose-" + id).click();
                    tAlert(data['data']);

                } else {
                    showErrorAlert('Erro no salvamento do registro', data['data']);
                }
                $("#btnSave-" + id).removeAttr('disabled');
                $("#btnSave-" + id).html('Salvar');

            },
            error: function(jqXHR, textStatus, errorThrown)
            {
                unblockUi();
                showErrorAlert('Ocorreu um erro.', 'JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown);
                $("#btnSave-" + id).removeAttr('disabled');
                $("#btnSave-" + id).html('Salvar');
            }
        });
    }
    return false;
}

function deleteEmail(user) {
    BootstrapDialog.show({
        title: 'Confirma&ccedil;&atilde;o',
        message: 'Deseja realmente excluir o email?',
        buttons: [{
                label: 'N&atilde;o',
                action: function(dialog) {
                    dialog.close();
                }
            }, {
                label: 'Sim',
                cssClass: 'btn-primary',
                action: function(dialog) {
                    var $button = this; // 'this' here is a jQuery object that wrapping the <button> DOM element.
                    $button.disable();
                    $button.spin();
                    dialog.setClosable(false);

                    var url = __PATH__ + 'ajax/delete-email/user/' + user;
                    var htmlb = $('#link_del').html();
                    $('#link_del').html('Aguarde, deletando email...');
                    $.ajax({
                        url: url,
                        type: 'GET',
                        dataType: "json",
                        success: function(data, textStatus, jqXHR) {
                            if (data['ok']) {
                                dialog.close();
                            } else {
                                $button.enable();
                                $button.stopSpin();
                                dialog.setClosable(true);
                            }
                            tAlert(data['data']);

                        },
                        error: function(jqXHR, textStatus, errorThrown)
                        {
                            showErrorAlert('Ocorreu um erro.', 'JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown);
                
                            $button.enable();
                            $button.stopSpin();
                            dialog.setClosable(true);
                        }
                    });

                }
            }]
    });

    return false;
}


function saveOrder(module, posicao) {
    var ordem = '';
    $('.position' + posicao + ' .well').each(function(i, obj) {
        ordem += (ordem == '' ? '' : '|') + $(this).attr('data-id');
    });
    $('#retorno' + posicao).html('<smal>Aguarde, salvando ordem...</smal>');
    var url = __PATH__ + 'ajax/change-order/class/' + module + '/position/' + posicao + '/order/' + ordem;
    $.ajax({
        url: url,
        type: 'GET',
        dataType: "json",
        success: function(data, textStatus, jqXHR) {
            if (data['ok']) {
                $('#retorno' + posicao).html(data['data']);

                //alterando a ordem dos n&uacute;meros
                var i = 1;
                $('.position' + posicao + ' .well').each(function(o, obj) {
                    $(this).find('.ordem').html((i++) + '&ordm;');
                });

            } else {
                alert(data['data'], 10000);
            }
        },
        error: function(jqXHR, textStatus, errorThrown)
        {
            showErrorAlert('Ocorreu um erro.', 'JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown);
                
            $("#btnSave").removeAttr('disabled');
            $("#btnSave").html('Salvar');
        }
    });
    return false;
}

function saveOrderNews() {
    var v_url = __PATH__ + 'ajax/change-order-news/';
    $('#form-order').ajaxSubmit({
        url: v_url,
        type: "POST",
        dataType: "json",
        beforeSend: function() {
            blockOnSubmit();
            $('#progressPercent').html('0%');
        },
        uploadProgress: function(event, position, total, percentComplete) {
            $('#progressPercent').html(percentComplete + '%');
        },
        success: function(data, textStatus, jqXHR)
        {
            unblockUi();

            if (data['ok']) {
                tAlert(data['data']);

            } else {
                showErrorAlert('Erro no salvamento da ordem', data['data']);
            }            
        },
        error: function(jqXHR, textStatus, errorThrown)
        {
            unblockUi();
            showErrorAlert('Ocorreu um erro.', 'JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown);
        }
    });
    return false;
}


function openFastAdd(module, updateField, params, callback, savebutton) {
    blockUi(); 
    callback = (callback || '');
    if (typeof(savebutton) == "undefined"){
        savebutton = true;
    }
    
    var url = __PATH__ + 'ajax/fast-add/class/' + module+params;
    var $message = $('<div class="row"></div>');
    var $title = 'Formul&aacute;rio';
    $.ajax({
        url: url,
        type: 'GET',
        dataType: "json",
        success: function(data) {
            if (data['ok']) {
                $message.append(data['data']);
                $title = data['title'];
            } else {
                $message.append(data['data']);
            }

        },
        error: function(jqXHR, textStatus, errorThrown)
        {
            unblockUi();
            showErrorAlert('Ocorreu um erro.', 'JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown);
        }

    }).done(function() {

        BootstrapDialog.show({
            closable: true,
            closeByBackdrop: false,
            closeByKeyboard: false,
            title: $title,
            type: 'type-default',
            message: $message,
            onshown: function(dialog) {
                dialog.getModalDialog().addClass('modal-lg');
                fieldFunctions();
                CKEDITOR.config.allowedContent = true;
                dialog.getModalContent().find('.ckeditor').each(function(key, textarea) {
                    var editor = CKEDITOR.replace(textarea.id);
                }); 
                unblockUi();
                dialog.getModalContent().find("form:not(.filter) :input:visible:enabled:first").focus();
            },
            onhide: function(dialog) {
                dialog.getModalContent().find('.ckeditor').each(function(key, textarea) {
                    CKEDITOR.instances[textarea.id].destroy();
                });
            },
            buttons: [{
                    label: 'Salvar',
                    cssClass: 'btn-success pull-right',
                    action: function(dialog) {
                        
                        if(!savebutton){
                            dialog.close();
                        }else{
                            var $button = this; // 'this' here is a jQuery object that wrapping the <button> DOM element.
                            $button.disable();
                            $button.spin();
                            dialog.setClosable(false);

                            var v_url = __PATH__ + 'ajax/save/class/' + module;
                            if (validaForm($$('fast-' + module))) {
                                dialog.getModalContent().find('.ckeditor').each(function(key, textarea) {
                                    CKEDITOR.instances[textarea.id].updateElement();
                                });
                                
                                $('#fast-' + module).ajaxSubmit({
                                    url: v_url,
                                    type: "POST",
                                    dataType: "json",
                                    beforeSend: function() {
                                        blockOnSubmit();
                                        $('#progressPercent').html('0%');
                                    },
                                    uploadProgress: function(event, position, total, percentComplete) {
                                        $('#progressPercent').html(percentComplete + '%');
                                    },
                                    success: function(data, textStatus, jqXHR)
                                    {
                                        unblockUi();
                                        if (data['ok']) {
                                            tAlert("<div class=\"alert alert-success\"><a href=\"#\" class=\"close\" data-dismiss=\"alert\">&times;</a>Registro salvo com sucesso!</div>");
                                            var urlupdate = __PATH__ + 'ajax/update-field/json/false/class/' + module + params;
                                            if(urlupdate!='')
                                                $('#' + updateField).load(urlupdate);
                                            dialog.close();
                                        } else {

                                            showErrorAlert('Erro no salvamento do registro', data['data']);
                                            
                                            $button.enable();
                                            $button.stopSpin();
                                            dialog.setClosable(true);
                                        }
                                        
                                        if(callback != ''){
                                            eval(callback);
                                        }
                                    },
                                    error: function(jqXHR, textStatus, errorThrown)
                                    {
                                        unblockUi();
                                        showErrorAlert('Ocorreu um erro.', 'JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown);
                    
                                        $button.enable();
                                        $button.stopSpin();
                                        dialog.setClosable(true);
                                    }
                                });

                            } else {
                                $button.enable();
                                $button.stopSpin();
                                dialog.setClosable(true);
                            }
                        }
                    }
                },{
                    label: 'Cancelar',
                    cssClass: 'pull-right',
                    action: function(dialogRef) {
                        dialogRef.close();
                    }
                }]
        });


    });
}


function openFastEdit(module, id, updateField, params, callback) {
    blockUi(); 
    callback = (callback || '');
    var url = __PATH__ + 'ajax/fast-edit/class/' + module+'/id/'+id+params;
    var $message = $('<div class="row"></div>');
    var $title = 'Formul&aacute;rio';
    var $permitido = false;
    $.ajax({
        url: url,
        type: 'GET',
        dataType: "json",
        success: function(data) {
            if (data['ok']) {
                $message.append(data['data']);
                $title = data['title'];
                $permitido = data['permitido'];
            } else {
                $message.append(data['data']);
            }

        },
        error: function(jqXHR, textStatus, errorThrown)
        {

            $message.append('JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown);
        }

    }).done(function() {

        BootstrapDialog.show({
            closable: true,
            closeByBackdrop: false,
            closeByKeyboard: false,
            title: $title,
            type: 'type-default',
            message: $message,
            onshown: function(dialog) {
                dialog.getModalDialog().addClass('modal-lg');
                fieldFunctions();
                CKEDITOR.config.allowedContent = true;
                dialog.getModalContent().find('.ckeditor').each(function(key, textarea) {
                    var editor = CKEDITOR.replace(textarea.id);
                });
                unblockUi();
                dialog.getModalContent().find("form:not(.filter) :input:visible:enabled:first").focus();
            },
            onhide: function(dialog) {
                dialog.getModalContent().find('.ckeditor').each(function(key, textarea) {
                    CKEDITOR.instances[textarea.id].destroy();
                });
            },
            buttons: [{
                    label: 'Salvar',
                    cssClass: 'btn-success pull-right',
                    action: function(dialog) {
                        if($permitido){
                            var $button = this; // 'this' here is a jQuery object that wrapping the <button> DOM element.
                            $button.disable();
                            $button.spin();
                            dialog.setClosable(false);

                            $form = dialog.getModalContent().find("form");
                            var formid = $($form).attr("id");
                            var v_url = __PATH__ + 'ajax/save/class/' + module+'/id/'+id;
                            if(!$form){
                                alert('Formul&aacute;rio n&atilde;o encontrado')
                            }else if (validaForm($$(formid))) {
                                dialog.getModalContent().find('.ckeditor').each(function(key, textarea) {
                                    CKEDITOR.instances[textarea.id].updateElement();
                                });
                                $($form).ajaxSubmit({
                                    url: v_url,
                                    type: "POST",
                                    dataType: "json",
                                    beforeSend: function() {
                                        blockOnSubmit();
                                        $('#progressPercent').html('0%');
                                    },
                                    uploadProgress: function(event, position, total, percentComplete) {
                                        $('#progressPercent').html(percentComplete + '%');
                                    },
                                    success: function(data, textStatus, jqXHR)
                                    {
                                        unblockUi();
                                        if (data['ok']) {
                                            tAlert("<div class=\"alert alert-success\"><a href=\"#\" class=\"close\" data-dismiss=\"alert\">&times;</a>Registro salvo com sucesso!</div>");
                                            if(updateField!=''){
                                                getTableList(updateField, module+params);
                                            }
                                            dialog.close();
                                        } else {

                                            showErrorAlert('Erro no salvamento do registro', data['data']);

                                            $button.enable();
                                            $button.stopSpin();
                                            dialog.setClosable(true);
                                        }

                                        if(callback != ''){
                                            eval(callback);
                                        }
                                    },
                                    error: function(jqXHR, textStatus, errorThrown)
                                    {
                                        unblockUi();
                                        showErrorAlert('Ocorreu um erro.', 'JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown);

                                        $button.enable();
                                        $button.stopSpin();
                                        dialog.setClosable(true);
                                    }
                                });

                            } else {
                                $button.enable();
                                $button.stopSpin();
                                dialog.setClosable(true);
                            }
                        }else{
                            showErrorAlert('Error','Desculpe, voc&ecirc; n&atilde;o possui permiss&atilde;o realizar esta opera&ccedil;&atilde;o.');
                        }
                    }
                    
                },{
                    label: 'Cancelar',
                    cssClass: 'pull-right',
                    action: function(dialogRef) {
                        dialogRef.close();
                    }
                },{
                    label: 'Apagar',
                    id: 'btnDel',
                    cssClass: 'btn-link',
                    action: function(dialog) {
                        delFormAction(module, id, callback, true);
                    }
                }]
        });


    });
}


function galleryList() {
    var module = $$('uploader-table').value;
    var id = ($$('uploader-id').value==0 ? $$('uploader-tid').value : $$('uploader-id').value);
    
    $('#gallery').html('Aguarde, buscando imagens...');
    var url = __PATH__ + 'ajax/gallery-list/class/' + module + '/id/' + id;
    $.ajax({
        url: url,
        type: 'GET',
        dataType: "json",
        success: function(data, textStatus, jqXHR) {
            if (data['ok']) {
                $('#gallery').html(data['data']);
            } else {
                showErrorAlert('Ocorreu um erro.', data['data']);
            }
        },
        error: function(jqXHR, textStatus, errorThrown)
        {
            showErrorAlert('Ocorreu um erro.', 'JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown);
        }
    });
}


function galleryDeleteImage(id, id_tipo) {
    BootstrapDialog.show({
        title: 'Confirma&ccedil;&atilde;o',
        message: 'Deseja realmente excluir a imagem?',
        buttons: [{
                label: 'N&atilde;o',
                action: function(dialog) {
                    dialog.close();
                }
            }, {
                label: 'Sim',
                cssClass: 'btn-primary',
                action: function(dialog) {
                    var $button = this; // 'this' here is a jQuery object that wrapping the <button> DOM element.
                    $button.disable();
                    $button.spin();
                    dialog.setClosable(false);

                    var url = __PATH__ + 'ajax/gallery-delete-image/id/' + id+'/id_tipo/'+id_tipo;
                    var htmlb = $('#img'+id).html();
                    $('#img'+id).html('Aguarde, deletando imagem...');
                    $.ajax({
                        url: url,
                        type: 'GET',
                        dataType: "json",
                        success: function(data, textStatus, jqXHR) {
                            if (data['ok']) {
                                dialog.close();
                                $('#img'+id).fadeOut('slow');
                            } else {
                                $button.enable();
                                $button.stopSpin();
                                dialog.setClosable(true);
                            }
                            tAlert(data['data']);

                        },
                        error: function(jqXHR, textStatus, errorThrown)
                        {
                            showErrorAlert('Ocorreu um erro.', 'JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown);
                
                            $button.enable();
                            $button.stopSpin();
                            dialog.setClosable(true);
                        }
                    });

                }
            }]
    });

    return false;
}


function fileEdit(id, id_tipo) {
    blockUi(); 
    
    var url = __PATH__ + 'ajax/file-edit/id/' + id+'/id_tipo/'+id_tipo;
    var $message = $('<div class="row"></div>');
    var $title = 'Formul&aacute;rio';
    var module = 'files';
    $.ajax({
        url: url,
        type: 'GET',
        dataType: "json",
        success: function(data) {
            if (data['ok']) {
                $message.append(data['data']);
                $title = data['title'];
            } else {
                $message.append(data['data']);
            }

        },
        error: function(jqXHR, textStatus, errorThrown)
        {
            showErrorAlert('Ocorreu um erro.', 'JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown);
                
        }

    }).done(function() {
        BootstrapDialog.show({
            closable: true,
            closeByBackdrop: false,
            closeByKeyboard: false,
            title: $title,
            type: 'type-default',
            message: $message,
            onshown: function(dialog) {
                unblockUi();
            },
            buttons: [ {
                    label: 'Salvar',
                    cssClass: 'btn-success pull-right',
                    action: function(dialog) {
                        var $button = this; // 'this' here is a jQuery object that wrapping the <button> DOM element.
                        $button.disable();
                        $button.spin();
                        dialog.setClosable(false);

                        var v_url = __PATH__ + 'ajax/file-save/class/' + module+'/id/'+id;
                        if (validaForm($$('fast-' + module))) {
                            $('#fast-' + module).ajaxSubmit({
                                url: v_url,
                                type: "POST",
                                dataType: "json",
                                success: function(data, textStatus, jqXHR)
                                {
                                    if (data['ok']) {
                                        tAlert("<div class=\"alert alert-success\"><a href=\"#\" class=\"close\" data-dismiss=\"alert\">&times;</a>Registro salvo com sucesso!</div>");
                                        fileList();
                                        dialog.close();
                                    } else {

                                       showErrorAlert('Erro no salvamento do registro', data['data']);

                                        $button.enable();
                                        $button.stopSpin();
                                        dialog.setClosable(true);
                                    }


                                },
                                error: function(jqXHR, textStatus, errorThrown)
                                {
                                    showErrorAlert('Ocorreu um erro.', 'JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown);
                

                                    $button.enable();
                                    $button.stopSpin();
                                    dialog.setClosable(true);
                                }
                            });

                        } else {
                            $button.enable();
                            $button.stopSpin();
                            dialog.setClosable(true);
                        }
                    }
                },{
                    label: 'Cancelar',
                    cssClass: 'pull-right',
                    action: function(dialogRef) {
                        dialogRef.close();
                    }
                }]
        });


    });
}

function fileList() {
    var module = $$('fuploader-table').value;
    var id = ($$('fuploader-id').value==0 ? $$('fuploader-tid').value : $$('fuploader-id').value);
    
    $('#upfiles').html('Aguarde, buscando imagens...');
    var url = __PATH__ + 'ajax/file-list/class/' + module + '/id/' + id;
    $.ajax({
        url: url,
        type: 'GET',
        dataType: "json",
        success: function(data, textStatus, jqXHR) {
            if (data['ok']) {
                $('#upfiles').html(data['data']);
            } else {
                showErrorAlert('Ocorreu um erro.', data['data']);
            }
        },
        error: function(jqXHR, textStatus, errorThrown)
        {
            showErrorAlert('Ocorreu um erro.', 'JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown);
        }
    });
}


function fileDelete(id, id_tipo) {
    BootstrapDialog.show({
        title: 'Confirma&ccedil;&atilde;o',
        message: 'Deseja realmente excluir este arquivo?',
        buttons: [{
                label: 'N&atilde;o',
                action: function(dialog) {
                    dialog.close();
                }
            }, {
                label: 'Sim',
                cssClass: 'btn-primary',
                action: function(dialog) {
                    var $button = this; // 'this' here is a jQuery object that wrapping the <button> DOM element.
                    $button.disable();
                    $button.spin();
                    dialog.setClosable(false);

                    var url = __PATH__ + 'ajax/file-delete/id/' + id+'/id_tipo/'+id_tipo;
                    var htmlb = $('#file'+id).html();
                    $('#file'+id).html('Aguarde, deletando imagem...');
                    $.ajax({
                        url: url,
                        type: 'GET',
                        dataType: "json",
                        success: function(data, textStatus, jqXHR) {
                            if (data['ok']) {
                                dialog.close();
                                $('#file'+id).fadeOut('slow');
                            } else {
                                $button.enable();
                                $button.stopSpin();
                                dialog.setClosable(true);
                            }
                            tAlert(data['data']);

                        },
                        error: function(jqXHR, textStatus, errorThrown)
                        {
                            showErrorAlert('Ocorreu um erro.', 'JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown);
                
                            $button.enable();
                            $button.stopSpin();
                            dialog.setClosable(true);
                        }
                    });

                }
            }]
    });

    return false;
}


function fileEdit(id, id_tipo) {
    blockUi(); 
    
    var url = __PATH__ + 'ajax/file-edit/id/' + id+'/id_tipo/'+id_tipo;
    var $message = $('<div class="row"></div>');
    var $title = 'Formul&aacute;rio';
    var module = 'files';
    $.ajax({
        url: url,
        type: 'GET',
        dataType: "json",
        success: function(data) {
            if (data['ok']) {
                $message.append(data['data']);
                $title = data['title'];
            } else {
                $message.append(data['data']);
            }

        },
        error: function(jqXHR, textStatus, errorThrown)
        {
            showErrorAlert('Ocorreu um erro.', 'JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown);
                
        }

    }).done(function() {
        BootstrapDialog.show({
            closable: true,
            closeByBackdrop: false,
            closeByKeyboard: false,
            title: $title,
            type: 'type-default',
            message: $message,
            onshown: function(dialog) {
                unblockUi();
            },
            buttons: [{
                    label: 'Salvar',
                    cssClass: 'btn-success pull-right',
                    action: function(dialog) {
                        var $button = this; // 'this' here is a jQuery object that wrapping the <button> DOM element.
                        $button.disable();
                        $button.spin();
                        dialog.setClosable(false);

                        var v_url = __PATH__ + 'ajax/file-save/class/' + module+'/id/'+id;
                        if (validaForm($$('fast-' + module))) {
                            $('#fast-' + module).ajaxSubmit({
                                url: v_url,
                                type: "POST",
                                dataType: "json",
                                success: function(data, textStatus, jqXHR)
                                {
                                    if (data['ok']) {
                                        tAlert("<div class=\"alert alert-success\"><a href=\"#\" class=\"close\" data-dismiss=\"alert\">&times;</a>Registro salvo com sucesso!</div>");
                                        fileList();
                                        dialog.close();
                                    } else {

                                       showErrorAlert('Erro no salvamento do registro', data['data']);

                                        $button.enable();
                                        $button.stopSpin();
                                        dialog.setClosable(true);
                                    }


                                },
                                error: function(jqXHR, textStatus, errorThrown)
                                {
                                    showErrorAlert('Ocorreu um erro.', 'JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown);
                

                                    $button.enable();
                                    $button.stopSpin();
                                    dialog.setClosable(true);
                                }
                            });

                        } else {
                            $button.enable();
                            $button.stopSpin();
                            dialog.setClosable(true);
                        }
                    }
                },{
                    label: 'Cancelar',
                    cssClass: 'pull-right',
                    action: function(dialogRef) {
                        dialogRef.close();
                    }
                }]
        });


    });
}





    function log() {
        var str = "";
 
        plupload.each(arguments, function(arg) {
            var row = "";
 
            if (typeof(arg) != "string") {
                plupload.each(arg, function(value, key) {
                    // Convert items in File objects to human readable form
                    if (arg instanceof plupload.File) {
                        // Convert status to human readable
                        switch (value) {
                            case plupload.QUEUED:
                                value = 'QUEUED';
                                break;
 
                            case plupload.UPLOADING:
                                value = 'UPLOADING';
                                break;
 
                            case plupload.FAILED:
                                value = 'FAILED';
                                break;
 
                            case plupload.DONE:
                                value = 'DONE';
                                break;
                        }
                    }
 
                    if (typeof(value) != "function") {
                        row += (row ? ', ' : '') + key + '=' + value;
                    }
                });
 
                str += row + " ";
            } else {
                str += arg + " ";
            }
        });
 
        var log = $('#log');
        log.append(str + "\n");
        log.scrollTop(log[0].scrollHeight);
    }


function novoComment(task, fieldcoment, pai, tipo){
    var v_url = __PATH__ + 'ajax/add-comment';
    var descricao = '';
    if (CKEDITOR.instances[fieldcoment]) {
        CKEDITOR.instances[fieldcoment].updateElement();
        descricao = CKEDITOR.instances[fieldcoment].getData();
    }else{
        descricao = $('#'+fieldcoment).val();
    }
    if(descricao != ''){
        $.ajax({
            url: v_url,
            type: "POST",
            data: { task: task, pai: pai, tipo: tipo, descricao:descricao },
            dataType: "json",
            beforeSend: function() {
                blockOnSubmit();
                $('#progressPercent').html('0%');
            },
            uploadProgress: function(event, position, total, percentComplete) {
                $('#progressPercent').html(percentComplete + '%');
            },
            success: function(data, textStatus, jqXHR)
            {
                unblockUi();
                if (data['ok']) {
                    if (CKEDITOR.instances[fieldcoment])
                        CKEDITOR.instances[fieldcoment].setData('');
                    tAlert(data['data']);
                    listComment(task);
                } else {
                    showErrorAlert('Erro no salvamento do registro', data['data']);

                }

            },
            error: function(jqXHR, textStatus, errorThrown)
            {
                unblockUi();
                showErrorAlert('Erro no salvamento do registro', 'JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown);
            }
        });
    }else{
        showErrorAlert('Dados inv&aacute;lidos', 'Descri&ccedil;&atilde;o n&atilde;o informada.');
    }
    return false;
}

function deleteComment(task, id) {
    BootstrapDialog.show({
        title: 'Confirma&ccedil;&atilde;o',
        message: 'Deseja realmente excluir o coment&aacute;rio?',
        buttons: [{
                label: 'N&atilde;o',
                action: function(dialog) {
                    dialog.close();
                }
            }, {
                label: 'Sim',
                cssClass: 'btn-primary',
                action: function(dialog) {
                    var $button = this; // 'this' here is a jQuery object that wrapping the <button> DOM element.
                    $button.disable();
                    $button.spin();
                    dialog.setClosable(false);

                    var url = __PATH__ + 'ajax/del-comment/id/' + id;
                    var htmlb = $('#link_del').html();
                    $('#link_del').html('Aguarde, deletando coment&aacute;rio...');
                    $.ajax({
                        url: url,
                        type: 'GET',
                        dataType: "json",
                        success: function(data, textStatus, jqXHR) {
                            if (data['ok']) {
                                listComment(task);
                                dialog.close();
                            } else {
                                $button.enable();
                                $button.stopSpin();
                                dialog.setClosable(true);
                            }
                            tAlert(data['data']);

                        },
                        error: function(jqXHR, textStatus, errorThrown)
                        {
                            showErrorAlert('Ocorreu um erro.', 'JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown);

                            $button.enable();
                            $button.stopSpin();
                            dialog.setClosable(true);
                        }
                    });

                }
            }]
    });

    return false;
}

function listComment(id){
    var updateField = 'txtComentarios';
    $('#' + updateField).html('<p>Aguarde, buscando...</p>');
    var urlupdate = __PATH__ + 'ajax/list-comment/json/false/id/'+id;
    $('#' + updateField).load(urlupdate);
}


function novoCommentTec(task, fieldcoment, pai, tipo){
    var v_url = __PATH__ + 'ajax/add-comment-tec';
    var descricao = '';
    if (CKEDITOR.instances[fieldcoment]) {
        CKEDITOR.instances[fieldcoment].updateElement();
        descricao = CKEDITOR.instances[fieldcoment].getData();
    }else{
        descricao = $('#'+fieldcoment).val();
    }
    if(descricao != ''){
        $('#formComentTec').ajaxSubmit({
            url: v_url,
            type: "POST",
            /*data: { task: task, pai: pai, tipo: tipo, descricao:descricao },*/
            dataType: "json",
            beforeSend: function() {
                blockOnSubmit();
                $('#progressPercent').html('0%');
            },
            uploadProgress: function(event, position, total, percentComplete) {
                $('#progressPercent').html(percentComplete + '%');
            },
            success: function(data, textStatus, jqXHR)
            {
                unblockUi();
                if (data['ok']) {
                    if (CKEDITOR.instances[fieldcoment])
                        CKEDITOR.instances[fieldcoment].setData('');
                    tAlert(data['data']);
                    listCommentTec(task);
                } else {
                    showErrorAlert('Erro no salvamento do registro', data['data']);

                }

            },
            error: function(jqXHR, textStatus, errorThrown)
            {
                unblockUi();
                showErrorAlert('Erro no salvamento do registro', 'JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown);
            }
        });
    }else{
        showErrorAlert('Dados inv&aacute;lidos', 'Descri&ccedil;&atilde;o n&atilde;o informada.');
    }
    return false;
}

function deleteCommentTec(task, id) {
    BootstrapDialog.show({
        title: 'Confirma&ccedil;&atilde;o',
        message: 'Deseja realmente excluir o coment&aacute;rio?',
        buttons: [{
                label: 'N&atilde;o',
                action: function(dialog) {
                    dialog.close();
                }
            }, {
                label: 'Sim',
                cssClass: 'btn-primary',
                action: function(dialog) {
                    var $button = this; // 'this' here is a jQuery object that wrapping the <button> DOM element.
                    $button.disable();
                    $button.spin();
                    dialog.setClosable(false);

                    var url = __PATH__ + 'ajax/del-comment-tec/id/' + id;
                    var htmlb = $('#link_del').html();
                    $('#link_del').html('Aguarde, deletando coment&aacute;rio...');
                    $.ajax({
                        url: url,
                        type: 'GET',
                        dataType: "json",
                        success: function(data, textStatus, jqXHR) {
                            if (data['ok']) {
                                listCommentTec(task);
                                dialog.close();
                            } else {
                                $button.enable();
                                $button.stopSpin();
                                dialog.setClosable(true);
                            }
                            tAlert(data['data']);

                        },
                        error: function(jqXHR, textStatus, errorThrown)
                        {
                            showErrorAlert('Ocorreu um erro.', 'JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown);

                            $button.enable();
                            $button.stopSpin();
                            dialog.setClosable(true);
                        }
                    });

                }
            }]
    });

    return false;
}

function listCommentTec(id){
    var updateField = 'txtComentariosTec';
    $('#' + updateField).html('<p>Aguarde, buscando...</p>');
    var urlupdate = __PATH__ + 'ajax/list-comment-tec/json/false/id/'+id;
    $('#' + updateField).load(urlupdate);
}


function getTableList(updateField, module){
    $('#' + updateField).html('<p>Aguarde, buscando...</p>');
    var urlupdate = __PATH__ + 'ajax/get-table-list/json/false/class/'+module;
    $.get( urlupdate, function( data ) {
        $('#' + updateField).html( data );
    }).always(function() {
        $('[data-toggle="tooltip"]').tooltip();
    });

    
}


function updateAutoCompleteField(module, filtro, idField, nameField){
    var v_url = __PATH__ + 'ajax/update-autocomplete-field/class/' + module+'/filtro/'+filtro;
    $.ajax({
        url: v_url,
        type: "POST",
        dataType: "json",
        success: function(data, textStatus, jqXHR)
        {
            if (data['ok']) {
                $('#'+idField).val(data['id']);
                $('#'+nameField).val(data['nome']);
            } else {
                showErrorAlert('Erro na busca das informa&ccedil;&otilde;es', data['data']);
            }
        },
        error: function(jqXHR, textStatus, errorThrown)
        {
            showErrorAlert('Ocorreu um erro.', 'JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown);
        }
    });

    
}

function getEndereco(idField, nameField) {
    function getDataFromApi(cep){
        $.getJSON( "https://viacep.com.br/ws/" + cep.replace(/[^0-9]/g,'')+"/json/", function( data ) {
            if (data) {
                $("#endereco").val(unescape(data.logradouro));
                $("#bairro").val(unescape(data.bairro));
                if($("#id_cidade")) $("#id_cidade").val(unescape(data.ibge));
                $("#cidade").val(unescape(data.localidade));
                $("#estado option").each(function() {
                    if($(this).val() == unescape(data.uf)) {
                      $(this).attr('selected', 'selected');            
                    }                        
                });
                if($(`#selectCidades`).length > 0){
                    getCidades($(`#estado`).val(),data.ibge);
                }
                
            }
        });
    }

    if ($.trim($("#cep").val()) != "") {
        if($("#endereco").val() == ''){
            getDataFromApi($("#cep").val());
        }else{
            BootstrapDialog.show({
                title: 'Confirma&ccedil;&atilde;o',
                message: 'Deseja substituir o cep existe?',
                buttons: [{
                        label: 'N&atilde;o',
                        action: function(dialog) {
                            dialog.close();
                        }
                    }, {
                        label: 'Sim',
                        cssClass: 'btn-primary',
                        action: function(dialog) {
                            getDataFromApi($("#cep").val());
                            dialog.close();
                        }
                    }]
            });
        }



        
    }
}


function finalizarTask(id) {
    BootstrapDialog.show({
        title: 'Confirma&ccedil;&atilde;o',
        message: 'Deseja realmente finalizar esta tarefa?',
        buttons: [{
                label: 'N&atilde;o',
                action: function(dialog) {
                    dialog.close();
                }
            }, {
                label: 'Sim',
                cssClass: 'btn-primary',
                action: function(dialog) {
                    var $button = this; // 'this' here is a jQuery object that wrapping the <button> DOM element.
                    $button.disable();
                    $button.spin();
                    dialog.setClosable(false);
                    
                    var url = __PATH__ + 'ajax/finaliza-task/id/' + id;
                    $.ajax({
                        url: url,
                        type: 'GET',
                        dataType: "json",
                        success: function(data, textStatus, jqXHR) {
                            if (data['ok']) {
                                dialog.close();
                                $('#progress'+id).attr('aria-valuenow',100);
                                $('#progress'+id).css('width','100%');
                                $('#progress'+id).html('100%');
                                $('#btn-finalizar').hide('slow');
                            } else {
                                $button.enable();
                                $button.stopSpin();
                                dialog.setClosable(true);
                            }
                            tAlert(data['data']);

                        },
                        error: function(jqXHR, textStatus, errorThrown)
                        {
                            showErrorAlert('Ocorreu um erro.', 'JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown);
                
                            $button.enable();
                            $button.stopSpin();
                            dialog.setClosable(true);
                        }
                    });
                }
            }]
    });

    return false;
}   


function entregarTask(id) {
    BootstrapDialog.show({
        title: 'Confirma&ccedil;&atilde;o',
        message: 'Deseja realmente entregar esta tarefa?',
        buttons: [{
                label: 'N&atilde;o',
                action: function(dialog) {
                    dialog.close();
                }
            }, {
                label: 'Sim',
                cssClass: 'btn-primary',
                action: function(dialog) {
                    var $button = this; // 'this' here is a jQuery object that wrapping the <button> DOM element.
                    $button.disable();
                    $button.spin();
                    dialog.setClosable(false);
                    
                    var url = __PATH__ + 'ajax/entrega-task/id/' + id;
                    $.ajax({
                        url: url,
                        type: 'GET',
                        dataType: "json",
                        success: function(data, textStatus, jqXHR) {
                            if (data['ok']) {
                                dialog.close();
                                $('#progress'+id).attr('aria-valuenow',100);
                                $('#progress'+id).css('width','100%');
                                $('#progress'+id).html('100%');
                                $('#btn-entregar').hide('slow');
                                
                            } else {
                                $button.enable();
                                $button.stopSpin();
                                dialog.setClosable(true);
                            }
                            tAlert(data['data']);

                        },
                        error: function(jqXHR, textStatus, errorThrown)
                        {
                            showErrorAlert('Ocorreu um erro.', 'JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown);
                
                            $button.enable();
                            $button.stopSpin();
                            dialog.setClosable(true);
                        }
                    });
                }
            }]
    });

    return false;
}   

function iniciarTask(id,status) {
    BootstrapDialog.show({
        title: 'Confirma&ccedil;&atilde;o',
        message: 'Deseja iniciar esta tarefa?',
        buttons: [{
                label: 'N&atilde;o',
                action: function(dialog) {
                    dialog.close();
                }
            }, {
                label: 'Sim',
                cssClass: 'btn-primary',
                action: function(dialog) {
                    var $button = this; // 'this' here is a jQuery object that wrapping the <button> DOM element.
                    $button.disable();
                    $button.spin();
                    dialog.setClosable(false);
                    
                    var url = __PATH__ + 'ajax/inicia-task/id/' + id+'/status/'+status;
                    $.ajax({
                        url: url,
                        type: 'GET',
                        dataType: "json",
                        success: function(data, textStatus, jqXHR) {
                            if (data['ok']) {
                                dialog.close();
                                location = __PATH__+'view-task/'+id
                                
                            } else {
                                $button.enable();
                                $button.stopSpin();
                                dialog.setClosable(true);
                            }
                            tAlert(data['data']);

                        },
                        error: function(jqXHR, textStatus, errorThrown)
                        {
                            showErrorAlert('Ocorreu um erro.', 'JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown);
                
                            $button.enable();
                            $button.stopSpin();
                            dialog.setClosable(true);
                        }
                    });
                }
            }]
    });

    return false;
}   


function getNextTask() {
    BootstrapDialog.show({
        title: 'Confirma&ccedil;&atilde;o',
        message: 'Deseja realmente iniciar o atendimento de mais um chamado?',
        buttons: [{
                label: 'N&atilde;o',
                action: function(dialog) {
                    dialog.close();
                }
            }, {
                label: 'Sim',
                cssClass: 'btn-primary',
                action: function(dialog) {
                    var $button = this; // 'this' here is a jQuery object that wrapping the <button> DOM element.
                    $button.disable();
                    $button.spin();
                    dialog.setClosable(false);
                    
                    var url = __PATH__ + 'ajax/get-next-task/';
                    $.ajax({
                        url: url,
                        type: 'GET',
                        dataType: "json",
                        success: function(data, textStatus, jqXHR) {
                            
                            if (data['ok']) {
                                location = __PATH__+'view-task/'+data['id']
                                
                            }
                            tAlert(data['data']);
                            $button.enable();
                            $button.stopSpin();
                            dialog.close();
                                                        

                        },
                        error: function(jqXHR, textStatus, errorThrown)
                        {
                            showErrorAlert('Ocorreu um erro.', 'JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown);
                
                            $button.enable();
                            $button.stopSpin();
                            dialog.setClosable(true);
                        }
                    });
                }
            }]
    });

    return false;
}

function retornaFilaTask(id) {
    BootstrapDialog.show({
        title: 'Confirma&ccedil;&atilde;o',
        message: 'Deseja realmente retornar esta tarefa para a fila?',
        buttons: [{
                label: 'N&atilde;o',
                action: function(dialog) {
                    dialog.close();
                }
            }, {
                label: 'Sim',
                cssClass: 'btn-primary',
                action: function(dialog) {
                    var $button = this; // 'this' here is a jQuery object that wrapping the <button> DOM element.
                    $button.disable();
                    $button.spin();
                    dialog.setClosable(false);
                    
                    var url = __PATH__ + 'ajax/retorna-fila-task/id/' + id;
                    $.ajax({
                        url: url,
                        type: 'GET',
                        dataType: "json",
                        success: function(data, textStatus, jqXHR) {
                            if (data['ok']) {
                                dialog.close();
                                location = __PATH__+'tasks/fila'
                                
                            } else {
                                $button.enable();
                                $button.stopSpin();
                                dialog.setClosable(true);
                            }
                            tAlert(data['data']);

                        },
                        error: function(jqXHR, textStatus, errorThrown)
                        {
                            showErrorAlert('Ocorreu um erro.', 'JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown);
                
                            $button.enable();
                            $button.stopSpin();
                            dialog.setClosable(true);
                        }
                    });
                }
            }]
    });

    return false;
}   

function alteraProgressoTask(id, progresso) {
    BootstrapDialog.show({
        title: 'Confirma&ccedil;&atilde;o',
        message: 'Deseja realmente alterar o progresso desta tarefa?',
        buttons: [{
                label: 'N&atilde;o',
                action: function(dialog) {
                    dialog.close();
                }
            }, {
                label: 'Sim',
                cssClass: 'btn-primary',
                action: function(dialog) {
                    var $button = this; // 'this' here is a jQuery object that wrapping the <button> DOM element.
                    $button.disable();
                    $button.spin();
                    dialog.setClosable(false);
                    
                    var url = __PATH__ + 'ajax/altera-progresso-task/id/' + id+'/progresso/'+progresso;
                    $.ajax({
                        url: url,
                        type: 'GET',
                        dataType: "json",
                        success: function(data, textStatus, jqXHR) {
                            if (data['ok']) {
                                dialog.close();
                                $('#progress'+id).attr('aria-valuenow',progresso);
                                $('#progress'+id).css('width',progresso+'%');
                                $('#progress'+id).html(progresso+'%');
                                $('.progress').show('slow');
                                $('#editProgress').hide('slow');
                                
                            } else {
                                $button.enable();
                                $button.stopSpin();
                                dialog.setClosable(true);
                            }
                            tAlert(data['data']);

                        },
                        error: function(jqXHR, textStatus, errorThrown)
                        {
                            showErrorAlert('Ocorreu um erro.', 'JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown);
                
                            $button.enable();
                            $button.stopSpin();
                            dialog.setClosable(true);
                        }
                    });
                }
            }]
    });

    return false;
}   


function alteraResponsavelTask(id, responsavel) {
    BootstrapDialog.show({
        title: 'Confirma&ccedil;&atilde;o',
        message: 'Deseja realmente alterar o respons&aacute;vel desta tarefa?',
        buttons: [{
                label: 'N&atilde;o',
                action: function(dialog) {
                    dialog.close();
                }
            }, {
                label: 'Sim',
                cssClass: 'btn-primary',
                action: function(dialog) {
                    var $button = this; // 'this' here is a jQuery object that wrapping the <button> DOM element.
                    $button.disable();
                    $button.spin();
                    dialog.setClosable(false);
                    
                    var url = __PATH__ + 'ajax/altera-responsavel-task/id/' + id+'/responsavel/'+responsavel;
                    $.ajax({
                        url: url,
                        type: 'GET',
                        dataType: "json",
                        success: function(data, textStatus, jqXHR) {
                            if (data['ok']) {
                                dialog.close();
                                $('#p-responsavel').html($('#nresponsavel option:selected').text());
                                
                            } else {
                                $button.enable();
                                $button.stopSpin();
                                dialog.setClosable(true);
                            }
                            tAlert(data['data']);

                        },
                        error: function(jqXHR, textStatus, errorThrown)
                        {
                            showErrorAlert('Ocorreu um erro.', 'JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown);
                
                            $button.enable();
                            $button.stopSpin();
                            dialog.setClosable(true);
                        }
                    });
                }
            }]
    });

    return false;
}   

function alteraGroupTask(id, group) {
    
    for (instance in CKEDITOR.instances)
        CKEDITOR.instances[instance].updateElement();
    
    var comment = $('#comentariotransf').val();
    
    if(comment.length < 10){
        showErrorAlert('Ocorreu um erro.', '&Eacute; necess&aacute;rio um coment&aacute;rio com no m&iacute;nimo 10 caracteres para realizar a transfer&ecirc;ncia de grupo.');
    }else if(group == 0){
        showErrorAlert('Ocorreu um erro.', '&Eacute; necess&aacute;rio selecionar um grupo para realizar a transfer&ecirc;ncia.');
    }else{
        BootstrapDialog.show({
            title: 'Confirma&ccedil;&atilde;o',
            message: 'Deseja realmente alterar o grupo desta tarefa?',
            buttons: [{
                    label: 'N&atilde;o',
                    action: function(dialog) {
                        dialog.close();
                    }
                }, {
                    label: 'Sim',
                    cssClass: 'btn-primary',
                    action: function(dialog) {
                        var $button = this; // 'this' here is a jQuery object that wrapping the <button> DOM element.
                        $button.disable();
                        $button.spin();
                        dialog.setClosable(false);

                        var url = __PATH__ + 'ajax/altera-group-task/id/' + id+'/group/'+group;
                        $.ajax({
                            url: url,
                            data: {comment: comment},
                            method: 'POST',
                            dataType: "json",
                            success: function(data, textStatus, jqXHR) {
                                if (data['ok']) {
                                    dialog.close();
                                    location = __PATH__+'tasks/list/status/d5';
                                } else {
                                    $button.enable();
                                    $button.stopSpin();
                                    dialog.setClosable(true);
                                    tAlert(data['data']);
                                }
                                

                            },
                            error: function(jqXHR, textStatus, errorThrown)
                            {
                                showErrorAlert('Ocorreu um erro.', 'JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown);

                                $button.enable();
                                $button.stopSpin();
                                dialog.setClosable(true);
                            }
                        });
                    }
                }]
        });
    }
    return false;
}   

function alteraStatusTask(id, status) {
    
    for (instance in CKEDITOR.instances)
        CKEDITOR.instances[instance].updateElement();
    
    var comment = $('#comentariotransf').val();
    var id_catalog = 0;
    var id_category = 0;
    if(comment.length < 10){
        showErrorAlert('Ocorreu um erro.', '&Eacute; necess&aacute;rio um coment&aacute;rio com no m&iacute;nimo 10 caracteres para realizar a transfer&ecirc;ncia de situa&ccedil;&atilde;o.');
    }else if($('#select_catalog').val()==1 && parseInt($('#id_catalog').val()) == 0){
        showErrorAlert('Ocorreu um erro.', '&Eacute; necess&aacute;rio selecionar uma op&ccedil;&atilde;o do cat&aacute;logo para realizar a transfer&ecirc;ncia de situa&ccedil;&atilde;o.');
    }else if($('#do_reclass').val()==1 && parseInt($('#id_category').val()) == 0){
        showErrorAlert('Ocorreu um erro.', '&Eacute; necess&aacute;rio selecionar uma categoria de reclassifica&ccedil;&atilde;;o para realizar a altera&ccedil;&atilde;o de situa&ccedil;&atilde;o.');
    }else if(status == 0){
        showErrorAlert('Ocorreu um erro.', '&Eacute; necess&aacute;rio selecionar um status para realizar a transfer&ecirc;ncia.');
    }else{
        
        if($('#select_catalog').val()==1){
            id_catalog = parseInt($('#id_catalog').val());
        }
        if($('#do_reclass').val()==1){
            id_category = parseInt($('#id_category').val());
        }
        
        BootstrapDialog.show({
            title: 'Confirma&ccedil;&atilde;o',
            message: 'Deseja realmente alterar a situa&ccedil;&atilde;o desta tarefa?',
            buttons: [{
                    label: 'N&atilde;o',
                    action: function(dialog) {
                        dialog.close();
                    }
                }, {
                    label: 'Sim',
                    cssClass: 'btn-primary',
                    action: function(dialog) {
                        var $button = this; // 'this' here is a jQuery object that wrapping the <button> DOM element.
                        $button.disable();
                        $button.spin();
                        dialog.setClosable(false);

                        var url = __PATH__ + 'ajax/altera-status-task/id/' + id+'/status/'+status+'/id_catalog/'+id_catalog+'/id_reclass/'+id_category;
                        $.ajax({
                            url: url,
                            data: {comment: comment},
                            method: 'POST',
                            dataType: "json",
                            success: function(data, textStatus, jqXHR) {
                                if (data['ok']) {
                                    dialog.close();
                                    location = __PATH__+'tasks/list/status/d5';
                                } else {
                                    $button.enable();
                                    $button.stopSpin();
                                    dialog.setClosable(true);
                                    tAlert(data['data']);
                                }
                                

                            },
                            error: function(jqXHR, textStatus, errorThrown)
                            {
                                showErrorAlert('Ocorreu um erro.', 'JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown);

                                $button.enable();
                                $button.stopSpin();
                                dialog.setClosable(true);
                            }
                        });
                    }
                }]
        });
    }
    return false;
}   

function openModalChangeTask(tipo){
    var ok = false;
    if(tipo == 's'){
        if($('#nstatus').val() == 0){
            showErrorAlert('Ocorreu um erro.', '&Eacute; necess&aacute;rio selecionar um status para realizar a transfer&ecirc;ncia.');
        }else{
            var url = __PATH__ + 'ajax/check-action-catalog/id/' + $('#nstatus').val();
            $.ajax({
                url: url,
                type: 'GET',
                dataType: "json",
                success: function(data, textStatus, jqXHR) {
                    
                    if(parseInt(data['catalogo'])==1){
                        $('#div-catalog').removeClass('hidden'); 
                        $('#select_catalog').val('1'); 
                    }else{
                        $('#div-catalog').addClass('hidden'); 
                        $('#select_catalog').val('0'); 
                    }
                    if(parseInt(data['reclassifica'])==1){
                        $('#div-reclass').removeClass('hidden'); 
                        $('#do_reclass').val('1'); 
                    }else{
                        $('#div-reclass').addClass('hidden'); 
                        $('#do_reclass').val('0'); 
                    }
                    $('#comentariotransf').val(data['resposta']);
                },
                error: function(jqXHR, textStatus, errorThrown)
                {
                    showErrorAlert('Ocorreu um erro.', 'JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown);
                }
            });
            
            $('#btn-change-group').addClass('hidden');
            $('#btn-change-status').removeClass('hidden');
            ok = true;
        }
    }else{
        if($('#ngroup').val() == 0){
            showErrorAlert('Ocorreu um erro.', '&Eacute; necess&aacute;rio selecionar um grupo para realizar a transfer&ecirc;ncia.');
        }else{
            $('#btn-change-status').addClass('hidden');
            $('#btn-change-group').removeClass('hidden'); 
            ok = true;
        }
    }
    
    if(ok){
        $('#modal-change').modal('show');
    }
}


function adicionarEnvolvidoTask(id, envolvido) {
    BootstrapDialog.show({
        title: 'Confirma&ccedil;&atilde;o',
        message: 'Deseja realmente adicionar o usu&aacute;rio nesta tarefa?',
        buttons: [{
                label: 'N&atilde;o',
                action: function(dialog) {
                    dialog.close();
                }
            }, {
                label: 'Sim',
                cssClass: 'btn-primary',
                action: function(dialog) {
                    var $button = this; // 'this' here is a jQuery object that wrapping the <button> DOM element.
                    $button.disable();
                    $button.spin();
                    dialog.setClosable(false);
                    
                    var url = __PATH__ + 'ajax/adiciona-envolvido-task/id/' + id+'/envolvido/'+envolvido;
                    $.ajax({
                        url: url,
                        type: 'GET',
                        dataType: "json",
                        success: function(data, textStatus, jqXHR) {
                            if (data['ok']) {
                                dialog.close();
                                $('#p-envolvidos').append('<p class="form-control-static">'+$('#nenvolvido option:selected').text()+'</p>');
                                
                            } else {
                                $button.enable();
                                $button.stopSpin();
                                dialog.setClosable(true);
                            }
                            tAlert(data['data']);

                        },
                        error: function(jqXHR, textStatus, errorThrown)
                        {
                            showErrorAlert('Ocorreu um erro.', 'JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown);
                
                            $button.enable();
                            $button.stopSpin();
                            dialog.setClosable(true);
                        }
                    });
                }
            }]
    });

    return false;
}   



function verificaNotificacoes(){
    var v_url = __PATH__ + 'ajax/verifica-notificacoes';
    $.ajax({
        url: v_url,
        type: "GET",
        dataType: "json",
        success: function(data, textStatus, jqXHR)
        {
            var title = document.title;
            document.title = title.replace(/ \(.*\)/i,'');
            
            if(data['qtd'] >0){
                if(!$('.popover').hasClass('in') && data['qtd'] > parseInt($('#avisosnl').html())){
                    $('#link-bell').popover({trigger: 'manual'});
                    $('#link-bell').popover('show');
                    $( "#link-bell" ).on( "click", function() {
                        $('#link-bell').popover('hide');
                    });
                    Notify(__NAME__,'Opa, tem novidade no sistema.');
                }
                $('#avisosnl').html(data['qtd']).removeClass('hidden');
                document.title += ' ('+data['qtd']+')';
            }else{
                $('#avisosnl').html(data['qtd']).addClass('hidden');
            }
            
            $('#div-notificacoes').html(data['notificacoes']);
            $('#isOffline').val(0);
            
        },
        error: function(jqXHR, textStatus, errorThrown)
        {
            if($('#isOffline').val()==0){
               showErrorAlert('Ocorreu um erro.', 'O sistema parece estar offline, verifique sua conex&atilde;o.');
            }
            $('#isOffline').val(1);
        }
    });

    
}

function getDependencias(projeto){
    $('#id_dependencia').html('<option value="0">Aguarde, buscando...</option>');
    var urlupdate = __PATH__ + 'ajax/update-field/json/false/class/tasks/filtro/id_projeto,i,' + projeto;
    $.get(urlupdate, function(data){ 
        $('#id_dependencia').html('<option value="0" selected>Sem depend&ecirc;ncia</option>'+data);
        $('#id_dependencia').val(0);
    });
    

    
}


function addFieldValue(){
    var formId = 'form-add-fieldvalue';
    if (validaForm($$(formId))) {
        $("#btnAddFieldValue").html('<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Aguarde...');
        $("#btnAddFieldValue").attr("disabled", true);

        var v_url = __PATH__ + 'ajax/save-fieldvalue/';

        $('#' + formId).ajaxSubmit({
            url: v_url,
            type: "POST",
            dataType: "json",
            beforeSend: function() {
                blockOnSubmit();
                $('#progressPercent').html('0%');
            },
            uploadProgress: function(event, position, total, percentComplete) {
                $('#progressPercent').html(percentComplete + '%');
            },
            success: function(data, textStatus, jqXHR)
            {
                unblockUi();
                if (data['ok']) {
                    $('#nome_vc').val('');
                    $('#valor_vc').val('');
                    tAlert('<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert">&times;</a>Campo inserido com sucesso!</div>');
                    getListFieldValues('txtFieldValues',$('#id_field_vc').val());
                    $('#nome').focus();
                } else {
                    showErrorAlert('Erro no salvamento do registro', data['data']);
                    
                }
                $("#btnAddFieldValue").removeAttr('disabled');
                $("#btnAddFieldValue").html('Adicionar');

            },
            error: function(jqXHR, textStatus, errorThrown)
            {
                unblockUi();
                showErrorAlert('Erro no salvamento do registro', 'JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown);
                
                $("#btnAddFieldValue").removeAttr('disabled');
                $("#btnAddFieldValue").html('Adicionar');
            }
        });
    }
    return false;
}

function delFieldValueForm(id) {

    BootstrapDialog.show({
        title: 'Confirma&ccedil;&atilde;o',
        message: 'Deseja realmente excluir o(s) registro(s) selecionado(s)?',
        buttons: [{
                label: 'N&atilde;o',
                action: function(dialog) {
                    dialog.close();
                }
            }, {
                label: 'Sim',
                cssClass: 'btn-primary',
                action: function(dialog) {
                    var $button = this; // 'this' here is a jQuery object that wrapping the <button> DOM element.
                    $button.disable();
                    $button.spin();
                    dialog.setClosable(false);

                    var v_url = __PATH__ + 'ajax/del-fieldvalue/id/' + id;
                    $.ajax({
                        url: v_url,
                        type: "POST",
                        dataType: "json",
                        success: function(data, textStatus, jqXHR)
                        {
                            if (data['ok']) {
                                dialog.close();
                                getListFieldValues('txtFieldValues',$('#id_field_vc').val());
                            } else {
                                showErrorAlert('Erro na exclus&atilde;o do registro', data['data']);
                            }
                            $button.enable();
                            $button.stopSpin();
                            dialog.setClosable(true);
                        },
                        error: function(jqXHR, textStatus, errorThrown)
                        {
                            showErrorAlert('Ocorreu um erro.', 'JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown);
                            $button.enable();
                            $button.stopSpin();
                            dialog.setClosable(true);
                        }
                    });

                }
            }]
    });

    return false;
}

function getListFieldValues(retorno, id){
    $('#' + retorno).html('<p>Aguarde, buscando...</p>');
    $('#' + retorno).load(__PATH__ + 'ajax/list-fieldvalues/json/false/id/'+id);
}

function getFields(task, cat){
    $('#txtFields').html('<div class="alert alert-waiting">Aguarde, buscando campos...</div>');
    $('#txtFields').load(__PATH__ + 'ajax/get-fields/json/false/id_task/'+task+'/id_category/'+cat,function(){
        fieldFunctions();    
    });
    /*$.getJSON(__PATH__ + 'ajax/get-fields-default/id/'+cat,function(data){
        $.each( data.campos, function( key, status ){
            if(status=='hidden'){
                $('#lbl'+key).addClass('hidden');
                $('#'+key).removeClass('required');
            }else{
                $('#lbl'+key).removeClass('hidden');
                $('#'+key).addClass('required');
            }
        }); 
        
    });*/
    
    
    
}


function getCategories(box, id){
    $('#c' + box).html('<option>Aguarde, buscando...</option>');
    var urlupdate = __PATH__ + 'ajax/get-categories/json/false/id/'+id;
    $('#c' + box).load(urlupdate);
}

function getCategoriesFull(box, id){
    $('#c' + box).html('<option>Aguarde, buscando...</option>');
    var urlupdate = __PATH__ + 'ajax/get-categories/json/false/full/1/id/'+id;
    $('#c' + box).load(urlupdate);
}

function getCatalogs(box, id){
    $('#c' + box).html('<option>Aguarde, buscando...</option>');
    var urlupdate = __PATH__ + 'ajax/get-catalogs/json/false/id/'+id;
    $('#c' + box).load(urlupdate);
}



function addTrigger(id, id_status){
    var formId = 'form-add-trigger'+id;
    if (validaForm($$(formId))) {
        $("#btnAddTrigger"+id).html('<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Aguarde...');
        $("#btnAddTrigger"+id).attr("disabled", true);


        var v_url = __PATH__ + 'ajax/save/class/triggers';
        if (validaForm($$(formId))) {
            $('#' + formId).ajaxSubmit({
                url: v_url,
                type: "POST",
                dataType: "json",
                beforeSend: function() {
                    blockOnSubmit();
                    $('#progressPercent').html('0%');
                },
                uploadProgress: function(event, position, total, percentComplete) {
                    $('#progressPercent').html(percentComplete + '%');
                },
                success: function(data, textStatus, jqXHR)
                {
                    if (data['ok']) {
                        tAlert("<div class=\"alert alert-success\"><a href=\"#\" class=\"close\" data-dismiss=\"alert\">&times;</a>Registro salvo com sucesso!</div>");
                        getTableList('txttriggers'+id, 'triggers/filtro/id_status,i,'+id_status+';id_trigger,i,'+id);
                    } else {
                        showErrorAlert('Erro no salvamento do registro', data['data']);
                    }
                    $("#btnAddTrigger"+id).removeAttr('disabled');
                    $("#btnAddTrigger"+id).html('Adicionar');
                    unblockUi();
                
                },
                error: function(jqXHR, textStatus, errorThrown)
                {
                    unblockUi();
                    showErrorAlert('Ocorreu um erro.', 'JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown);

                    $("#btnAddTrigger"+id).removeAttr('disabled');
                    $("#btnAddTrigger"+id).html('Adicionar');
                }
            });

        } else {
            $("#btnAddTrigger"+id).removeAttr('disabled');
            $("#btnAddTrigger"+id).html('Adicionar');
        }

        
    }
    return false;
}


function executeTrigger(id, id_status){
    var answerQuestion = false;
    $('#btnTrg'+id_status).prop("disabled",true);
    var v_url = __PATH__ + 'ajax/check-has-survey/id/'+id+'/id_trigger/'+id_status;
    $.ajax({
        url: v_url,
        type: "POST",
        dataType: "json",
        success: function(data, textStatus, jqXHR)
        {
            if (data['ok']) {
                showPesquisa(data['data'],id,id_status,'');
                answerQuestion = true;
            }
        },
        error: function(jqXHR, textStatus, errorThrown)
        {
            showErrorAlert('Ocorreu um erro.', 'JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown);
        }
    }).done(function() { 
    
        if(!answerQuestion){

            v_url = __PATH__ + 'ajax/trigger-execute/id/'+id+'/btn/'+id_status;
            $.ajax({
                url: v_url,
                type: "POST",
                dataType: "json",
                beforeSend: function() {
                    blockOnSubmit();
                    $('#progressPercent').html('0%');
                },
                uploadProgress: function(event, position, total, percentComplete) {
                    $('#progressPercent').html(percentComplete + '%');
                },
                success: function(data, textStatus, jqXHR)
                {
                    tAlert(data['data']);
                    if (data['ok']) {
                        setTimeout(function(){location.reload();},1000);
                    } else {
                        showErrorAlert('Erro no salvamento do registro', data['data']);
                    }
                    unblockUi();

                },
                error: function(jqXHR, textStatus, errorThrown)
                {
                    unblockUi();
                    showErrorAlert('Ocorreu um erro.', 'JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown);
                }
            });    
        }
        $('#btnTrg'+id_status).prop("disabled",false);
    });
    return false;
}

function executeTriggerComment(id, id_status, frmComment){
    //function executeTriggerComment(id, id_status, comentario){
    //if(comentario.length < 10){
    if($("#"+frmComment+" textarea[name=comentario]").val().length < 10){
        showErrorAlert('Ocorreu um erro.', '&Eacute; necess&aacute;rio um coment&aacute;rio com no m&iacute;nimo 10 caracteres para prosseguir.');
    }else{
        $('#btnTrg'+id_status).prop("disabled",true);
        var answerQuestion = false;
        var v_url = __PATH__ + 'ajax/check-has-survey/id/'+id+'/id_trigger/'+id_status;
        $.ajax({
            url: v_url,
            type: "POST",
            dataType: "json",
            success: function(data, textStatus, jqXHR)
            {
                if (data['ok']) {
                    //showPesquisa(data['data'],id,id_status, comentario);
                    showPesquisa(data['data'],id,id_status, frmComment);
                    answerQuestion = true;
                }
            },
            error: function(jqXHR, textStatus, errorThrown)
            {
                showErrorAlert('Ocorreu um erro na checagem de pesquisa', 'JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown);
            }
        }).done(function() {
        
            if(!answerQuestion){
                blockUi();
                url = __PATH__ + 'ajax/trigger-execute-comment/id/'+id+'/trg/'+id_status;
                $('#'+frmComment).ajaxSubmit({
                //$.ajax({
                    url: url,
                    type: "POST",
                    dataType: "json",
                    /*data: { comentario: comentario },
                    beforeSend: function() {
                        blockOnSubmit();
                        $('#progressPercent').html('0%');
                    },
                    uploadProgress: function(event, position, total, percentComplete) {
                        $('#progressPercent').html(percentComplete + '%');
                    },*/
                    success: function(data, textStatus, jqXHR)
                    {
                        tAlert(data['data']);
                        if (data['ok']) {
                            setTimeout(function(){location.reload();},1000);
                        } else {
                            showErrorAlert('Erro no salvamento do registro', data['data']);
                        }
                        unblockUi();

                    },
                    error: function(jqXHR, textStatus, errorThrown)
                    {
                        unblockUi();
                        showErrorAlert('Ocorreu um erro no salvamento', 'JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown);
                    }
                });    
            }
            $('#btnTrg'+id_status).prop("disabled",false);
        });
    }
    return false;
}


function executeTriggerOvelayComment(id, id_status){
    BootstrapDialog.show({
        closable: true,
        closeByBackdrop: false,
        closeByKeyboard: false,
        title: 'Informa&ccedil;&otilde;es complementares',
        type: 'type-default',
        message:'<form id="frmCommentTrigger" method="post"> \
            <div class="row"><div class="form-group col-sm-12"> \
                <label for="">Descreva o motivo:</label> \
                <textarea name="comentario" id="comentariotriggerbutton" rows="8" class="form-control"></textarea> \
            </div> \
            <div id="anexos" class="col-sm-12"> \
                <div class="form-group col-sm-12"> \
                    <label for="">Anexo</label> \
                    <input type="file" class="form-control" name="file[]" /> \
                </div> \
            </div>\
            <div class="col-sm-12">\
                <div class="form-group col-sm-12"><a href="javascript:;" onclick="javascript:$(\'#anexos\').append(\'<div class=\\\'form-group col-sm-12\\\'><input type=\\\'file\\\' class=\\\'form-control\\\' name=\\\'file[]\\\' /></div>\');"><span class="glyphicon glyphicon-plus"></span> Mais anexos</a></div>\
            </div></div></form>',
        buttons: 
        [
            
            {
                label: 'Prosseguir',
                cssClass: 'btn-primary',
                action: function(dialog) {
                    //executeTriggerComment(id, id_status, $('#comentariotriggerbutton').val());
                    executeTriggerComment(id, id_status, 'frmCommentTrigger');
                }
                
            },
            {
                label: 'Cancelar',
                cssClass: 'pull-right',
                action: function(dialogRef) {
                    dialogRef.close();
                }
            }
        ]
    });


}

function clearNotifications() {
    BootstrapDialog.show({
        title: 'Confirma&ccedil;&atilde;o',
        message: 'Deseja realmente excluir a(s) notifica&ccedil;&atilde;o(&otilde;es)?',
        buttons: [{
                label: 'N&atilde;o',
                action: function(dialog) {
                    dialog.close();
                }
            }, {
                label: 'Sim',
                cssClass: 'btn-primary',
                action: function(dialog) {
                    var $button = this; // 'this' here is a jQuery object that wrapping the <button> DOM element.
                    $button.disable();
                    $button.spin();
                    dialog.setClosable(false);

                    var v_url = __PATH__ + 'ajax/apagar-notificacoes';
                    $.ajax({
                        url: v_url,
                        type: "POST",
                        dataType: "json",
                        success: function(data, textStatus, jqXHR)
                        {
                            if (data['ok']) {
                                dialog.close();
                                tAlert(data['data']);
                                verificaNotificacoes();
                            } else {
                                showErrorAlert('Erro na exclus&atilde;o do registro', data['data']);
                            }
                            $button.enable();
                            $button.stopSpin();
                            dialog.setClosable(true);
                        },
                        error: function(jqXHR, textStatus, errorThrown)
                        {
                            showErrorAlert('Ocorreu um erro.', 'JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown);
                            $button.enable();
                            $button.stopSpin();
                            dialog.setClosable(true);
                        }
                    });
                }
            }]
    });

    return false;
}


function showPesquisa(id, id_task, id_status, comentario) {
    blockUi(); 
    
    var url = __PATH__ + 'ajax/show-pesquisa/id/' + id;
    var $message = $('<div class="row"></div>');
    var $title = 'Question&aacute;rio de Pesquisa';
    $.ajax({
        url: url,
        type: 'GET',
        dataType: "json",
        success: function(data) {
            $message.append(data['data']);
        },
        error: function(jqXHR, textStatus, errorThrown)
        {
            showErrorAlert('Ocorreu um erro.', 'JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown);
                
        }

    }).done(function() {
        BootstrapDialog.show({
            closable: true,
            closeByBackdrop: false,
            closeByKeyboard: false,
            title: $title,
            type: 'type-default',
            message: $message,
            cssClass: 'pesquisa-dialog',
            onshown: function(dialog) {
                unblockUi();
            },
            buttons: [{
                    label: 'Salvar',
                    cssClass: 'btn-success pull-right',
                    action: function(dialog) {
                        var $button = this; // 'this' here is a jQuery object that wrapping the <button> DOM element.
                        $button.disable();
                        $button.spin();
                        dialog.setClosable(false);

                        var v_url = __PATH__ + 'ajax/save-pesquisa/id/'+id;
                        
                        $('#frmPesquisa').ajaxSubmit({
                            url: v_url,
                            type: "POST",
                            dataType: "json",
                            success: function(data, textStatus, jqXHR)
                            {
                                if (data['ok']) {
                                    tAlert("<div class=\"alert alert-success\"><a href=\"#\" class=\"close\" data-dismiss=\"alert\">&times;</a>Pesquisa salva com sucesso!</div>");
                                    dialog.close();
                                    
                                    if(comentario == ''){
                                        executeTrigger(id_task, id_status);
                                    }else{
                                        executeTriggerComment(id_task, id_status, comentario)
                                    }
                                    
                                } else {

                                    showErrorAlert('Erro no salvamento do registro', data['data']);

                                    $button.enable();
                                    $button.stopSpin();
                                    dialog.setClosable(true);
                                }


                            },
                            error: function(jqXHR, textStatus, errorThrown)
                            {
                                showErrorAlert('Ocorreu um erro.', 'JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown);


                                $button.enable();
                                $button.stopSpin();
                                dialog.setClosable(true);
                            }
                        });

                        
                    }
                },{
                    label: 'Cancelar',
                    cssClass: 'pull-right',
                    action: function(dialogRef) {
                        dialogRef.close();
                    }
                }]
        });


    });
}

function checkStatusClientBranch(status, tipo){
    if(status == '1'){
        $('#txt-msg-atraso').html('').addClass('hidden');
        $('.bootstrap-dialog-footer-buttons .btn-primary').removeClass('hidden');
        if(tipo == 'c'){
            $('#unidade').prop('disabled', false);
        }
    }else{
        if(status == '0'){
            $('#txt-msg-atraso').html('Desculpe, '+(tipo=='b'?'filial desativada':'cliente desativado')+'. N&atilde;o ser&aacute; permitida abertura de novos chamados.');
        }else{
            $('#txt-msg-atraso').html('Desculpe, '+(tipo=='b'?'filial em atraso':'cliente em atraso')+'. N&atilde;o ser&aacute; permitida abertura de novos chamados.');
        }
        if(tipo == 'c'){
            $('#unidade').prop('disabled', true);
        }
        $('#txt-msg-atraso').removeClass('hidden')
        $('.bootstrap-dialog-footer-buttons .btn-primary').addClass('hidden');
    }
}


function getBranchesGroupPermission(id_client, id_group){
    $('#txtBranches').html('<p>Aguarde, buscando...</p>');
    var urlupdate = __PATH__ + 'ajax/get-permissions-bg/json/false/id_client/'+id_client+'/id_group/'+id_group;
    $('#txtBranches').load(urlupdate);
}
function getBranchesUserPermission(id_client, id_user){
    $('#txtBranches').html('<p>Aguarde, buscando...</p>');
    var urlupdate = __PATH__ + 'ajax/get-permissions-bu/json/false/id_client/'+id_client+'/id_user/'+id_user;
    $('#txtBranches').load(urlupdate);
}

function getClientBranches(id_client, tipo){
    
    $('#filial').html('<option>Aguarde, buscando filiais...</option>'); 
    $.get( __PATH__ + 'ajax/update-field/json/false/class/branches/nsli/1/filtro/id_client,i,'+ id_client, function(data){  
        $('#filial').html('<option value=\'0\' selected>Todas</option>'+data);    
    });

    if(tipo == 0){ 
        var AM = ($('#id_client').find('option:selected').data('matriz') == 1);
        var AF = ($('#id_client').find('option:selected').data('filial') == 1);
        
        $('#filial').prop('disabled',false);
        if(AM && AF){
            $('#unidade').attr('readonly', false).css('pointer-events','auto').css('touch-action','auto').val(1);    
            $('#filial').prop('disabled',true);
        }else{
            if(AM){
                $('#filial').prop('disabled',true);
                $('#unidade').val(1);
            }else{
                $('#unidade').val(2);
            }
            $('#unidade').attr('readonly', true).css('pointer-events','none').css('touch-action','none');
        }
    }
}

function blockUiMsg(message, timeout, callback){
    var cb = callback || 'unblockUi()';
    var timeout = timeout || 5000;
    var count = timeout/1000;

    message += '<br><em class="small">Esta janela se fechar&aacute; em <span id="msg_seg">'+count+'</span> segundos. <a href="javascript:;" onclick="'+cb+'">Fechar agora!</a></em>'

    $.blockUI({ message: message,
        css: {
            border: 'none',
            padding: '15px',
            backgroundColor: '#000',
            '-webkit-border-radius': '10px',
            '-moz-border-radius': '10px',
            opacity: 1,
            color: '#fff'
        }
    });
    var tout;
    function dcounter() { 
        $('#msg_seg').html(count);
        if(count > 0){ 
            count--;
            tout = setTimeout(function(){dcounter()},1000);
        }
    }
    
    dcounter();
    setTimeout(function() {
        clearTimeout(tout);
        eval(cb);
    }, timeout);
}



    function exportTaskCSV(busca, paramadd, page, maxpage, arquivo){
        
        if(page == 1){ blockOnSubmit(); }
        $.ajax({
            url: __PATH__+'ajax/export-data-csv',
            type: "POST",
            data: { busca: busca, paramadd: paramadd, page: page, arquivo: arquivo },
            dataType: "json",
            success: function(data, textStatus, jqXHR) {
                $('#progressPercent').html((page*100/maxpage).toFixed(2)+'%');
            },
            error: function(jqXHR, textStatus, errorThrown){
                unblockUi();
                showErrorAlert('Erro no salvamento do registro', 'JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown);
                return true;
            }
        }).then(function(data){
            
            if(page < maxpage){
                exportTaskCSV(busca,paramadd, ++page, maxpage, arquivo);
            }else{
                unblockUi();
                location = __BASEPATH__+'uploads/exportacoes/'+arquivo
                return true;
            }

         });
        
    }

    function exportSimpleTaskCSV(busca, paramadd, page, maxpage, arquivo){
        if(page == 1){ blockOnSubmit(); }
        $.ajax({
            url: __PATH__+'ajax/export-simple-data-csv',
            type: "POST",
            data: { busca: busca, paramadd: paramadd, page: page, arquivo: arquivo },
            dataType: "json",
            success: function(data, textStatus, jqXHR) {
                $('#progressPercent').html((page*100/maxpage).toFixed(2)+'%');
            },
            error: function(jqXHR, textStatus, errorThrown){
                unblockUi();
                showErrorAlert('Erro no salvamento do registro', 'JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown);
                return true;
            }
        }).then(function(data){
            
            if(page < maxpage){
                exportSimpleTaskCSV(busca,paramadd, ++page, maxpage, arquivo);
            }else{
                unblockUi();
                location = __BASEPATH__+'uploads/exportacoes/'+arquivo
                return true;
            }

         });
        
    }

    function sincronizeCNAE(codigo){
        blockUi();
        $.ajax({
            url: __PATH__+'ajax/sincronze-cnae/id/'+codigo,
            type: "GET",
            dataType: "json",
            success: function(data, textStatus, jqXHR) {
                unblockUi();
                tAlert(data['data']);
                getTableList('txtservices', 'servicesaccount/id_account/'+codigo+'/filtro/id_account,i,'+codigo);
            },
            error: function(jqXHR, textStatus, errorThrown){
                unblockUi();
                showErrorAlert('Erro no salvamento do registro', 'JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown);
                return true;
            }
        });
        
    }

    function doFollowUp(){

        for (instance in CKEDITOR.instances)
            CKEDITOR.instances[instance].updateElement();
    
        $('#frm-followup').ajaxSubmit({
            url: __PATH__ + 'ajax/do-follow-up',
            type: "POST",
            dataType: "json",
            beforeSend: function() {
                blockOnSubmit();
                $('#progressPercent').html('0%');
            },
            uploadProgress: function(event, position, total, percentComplete) {
                $('#progressPercent').html(percentComplete + '%'); 
            },
            success: function(resp) {
                if (resp.success) {
                    MessageBox.success(resp.message); 
                } else {
                    MessageBox.error(resp.message); 
                }
                $('#progressPercent').html('');
                unblockUi();
                $('#over-followup').modal('hide');
            },
            error: function(xhr, status, error) {
                MessageBox.error('Falha na requisição: ' + error);
                $('#progressPercent').html('');
                unblockUi();
            }
        });

        
    }

    function transmiteNFSe(codigo){
        blockUi();
        $.ajax({
            url: __PATH__+"ajax/transmitir-nfse/id/"+codigo,
            type: "GET",
            dataType: "json",
            success: function(data) {
                unblockUi();
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: data.message
                    });
                    $("#txtRetorno").html(data.message);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: data.message
                    })
                }
            },
            error: function(jqXHR, textStatus, errorThrown){
                unblockUi();
                showErrorAlert("Erro na transmiss&atilde;o", 'JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown);
            }
        });
    }


    function verificaLoteNFSe(codigo){
        blockUi();
        $.ajax({
            url: __PATH__+"ajax/verifica-lote-nfse/id/"+codigo,
            type: "GET",
            dataType: "json",
            success: function(data) {
                unblockUi();
                Swal.fire({
                    icon: 'success',
                    title: data.message
                })
            },
            error: function(jqXHR, textStatus, errorThrown){
                unblockUi();
                showErrorAlert("Erro na transmiss&atilde;o", 'JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown);
                return true;
            }
        });
        
    }

    function openModalCancelarNFSe(id) {
        Swal.fire({
            title: 'Cancelamento de NFS-e',
            html: '<form id="frmCommentTrigger" method="post"> \
                <div class="row"><div class="form-group col-sm-12"> \
                <label for="">Descreva o motivo:</label> \
                <textarea name="comentario" id="cancela-nfs-motivo" rows="8" class="form-control"></textarea> \
                </div> \
                </form>',
            showCancelButton: true,
            confirmButtonText: 'Confirmar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true,
            preConfirm: () => {
                if ($('#cancela-nfs-motivo').val() == '') {
                    Swal.showValidationMessage('É necessário informar o motivo do cancelamento.');
                } else {
                    cancelarNFSe(id, $('#cancela-nfs-motivo').val());
                }
            }
        });
    }

    function cancelarNFSe(codigo, motivo, ldialog) {
        var ldialog = ldialog || null;
        if (parseInt(codigo) == 0) {
            Swal.fire("Erro no cancelamento", 'Erro no ID da NFS-e.');
        } else if (motivo == '') {
            Swal.fire("Erro no cancelamento", '&Eacute; necess&aacute;rio informar o motivo do cancelamento.');
        } else {
            Swal.fire({
                title: 'Confirma&ccedil;&atilde;o',
                text: 'Deseja realmente cancelar esta NFS-e?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sim',
                cancelButtonText: 'N&atilde;o',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    blockUi();
                    $("#txtOverNFRetorno").html("Realizando cancelamento...");
                    $.ajax({
                        url: __PATH__ + "ajax/cancelar-nfse/id/" + codigo,
                        type: "POST",
                        dataType: "json",
                        data: { motivo: motivo },
                        success: function (data) {
                            unblockUi();
                            Swal.fire({
                                icon: 'warning',
                                title: data.message
                            });
                            if (ldialog) ldialog.close();
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            unblockUi();
                            Swal.fire("Erro no cancelamento", 'JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown);
                        }
                    });
                }
            });
        }
    }


    function geraNFSeConta(id_conta, callback=null) {
        if (parseInt(id_conta) == 0) {
            Swal.fire("Erro na geração", "Erro no ID da Conta a receber.", "error");
        } else {
            Swal.fire({
                title: 'Confirmação',
                text: "Deseja realmente gerar uma NFS-e baseada nesta conta?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sim',
                cancelButtonText: 'Não'
            }).then((result) => {
                if (result.isConfirmed) {

                    blockUi();
                    $.ajax({
                        url: __PATH__ + "ajax/gerar-nf-conta/id/" + id_conta,
                        type: "GET",
                        dataType: "json",
                        success: function (data) {
                            unblockUi();
                            if (data.success) {
                                Swal.fire({
                                    title: "Sucesso",
                                    text: data.message,
                                    icon: "success"
                                });
                                if(callback) callback();
                                
                            } else {
                                Swal.fire({
                                    title: "Erro na geração",
                                    text: data.message,
                                    icon: "error"
                                });
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            unblockUi();
                            Swal.fire({
                                title: "Erro na geração",
                                html: 'JQXHR: ' + jqXHR.responseText + '<br>Status: ' + textStatus + '<br>Throw: ' + errorThrown,
                                icon: "error"
                            });
                        }
                    });
                }
            });
        }
    }



    function buscaContasParaRemessa(dti, dtf, todos, id_retorno) {

        var url = __PATH__ + 'ajax/busca-contas-remessa';
    
        if(dti != ''){
            dtAux = dti.split('/');
            url += '/dti/'+dtAux[2]+'-'+dtAux[1]+"-"+dtAux[0];
        }

        if(dtf != ''){
            dtAux = dtf.split('/');
            url += '/dtf/'+dtAux[2]+'-'+dtAux[1]+"-"+dtAux[0];
        }

        url += '/todos/'+todos;

        
        blockUi();
        $.ajax({
            url: url,
            type: "GET",
            dataType: "json", // Espera uma resposta em JSON
            success: function(response) {
                unblockUi();
                if (response.success) {
                    $('#' + id_retorno).html(response.html); // Insere o HTML no elemento
                } else {
                    $('#' + id_retorno).html(response.message);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                unblockUi();
                console.error("Erro na requisição: ", textStatus, errorThrown);
                alert("Ocorreu um erro ao fazer a requisição. Por favor, tente novamente.");
            }
        });
    }
    

    function setLoteTransmitido(id, transmitido){
        
        blockUi();
        $.ajax({
            url: __PATH__+'ajax/set-lote-transmitido/id/'+id+'/transmitido/'+transmitido,
            type: "GET",
            dataType: "json",
            success: function(response) {
                unblockUi();
                if(response.success){
                    window.location.reload(true);
                }else{
                    showErrorAlert("Erro ao alterar o Lote", resp.message);
                }
                
            },
            error: function(jqXHR, textStatus, errorThrown){
                unblockUi();
                alert("Erro no cancelamento");
            }
    
        });
    }
   
