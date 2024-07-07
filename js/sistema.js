
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


function geraNFSeConta(id_conta) {
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
                            loadContas();
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

function calculaMovimentacao(sufixo) {
    var valor = $('#valor'+sufixo).val().replace('.','').replace(',','.');
    var juro = $('#juro'+sufixo).val().replace('.','').replace(',','.');
    var multa = $('#multa'+sufixo).val().replace('.','').replace(',','.');
    var desconto = $('#desconto'+sufixo).val().replace('.','').replace(',','.');

    if(valor == '') valor = '0';
    if(juro == '') juro = '0';
    if(multa == '') multa = '0';
    if(desconto == '') desconto = '0';
    $('#total'+sufixo).val(number_format(parseFloat(valor) + parseFloat(juro) + parseFloat(multa) - parseFloat(desconto),2,',','.'));
}