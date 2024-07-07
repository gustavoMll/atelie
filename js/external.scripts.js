function errorFunction(err, textStatus, errorThrown) {
	unblockUi();
	MessageBox.error("Ocorreu um erro. Para detalhes pressione F12 e verifique no console.");
	console.log(err, textStatus, errorThrown);
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

function fieldFunctions(){
    if(window.innerWidth > 768){
        $(".date,.datewdp").mask("99/99/9999", { autoclear: false });
        $(".datehour").mask("99/99/9999 99:99:99", { autoclear: false });
        $(".dateshorthour").mask("99/99/9999 99:99", { autoclear: false });
        $(".hour").mask("99:99:99", { autoclear: false });
        $(".shorthour").mask("99:99", { autoclear: false });
        $(".cep").mask("99.999-999", { autoclear: false });
        $(".cpf").mask("000.000.000-00", { clearIfNotMatch: false });
        $(".cnpj").mask("00.000.000/0000-00", { clearIfNotMatch: false });
        $(".cpfcnpj").each(function (i, obj) {
            $(this).focus(function () {
                $(this).unmask().mask().val(this.value.replace(/[^0-9]/g, ""));
            })
                .blur(function () {
                    this.value = this.value.replace(/[^0-9]/g, "");
                    if (this.value.length > 11) {
                        $(this).mask("00.000.000/0000-00", { clearIfNotMatch: false });
                    } else {
                        $(this).mask("000.000.000-00", { clearIfNotMatch: false });
                    }
                });
        });

        $(".phone").each(function (i, obj) {
            var len = $(obj).val().replace(/[^0-0]/g, "").length;
            if (len > 10) {
                $(obj).attr("data-tmask", 11);
                $(obj).mask("(00) 00000-0000", { clearIfNotMatch: false });
            } else {
                $(obj).attr("data-tmask", 10);
                $(obj).mask("(00) 0000-00000", { clearIfNotMatch: false });
            }
            $(obj).keyup(function () {
                var leng = $(this).val().replace(/[^0-9]/g, "").length;
                if (leng > 10) {
                    if ($(this).attr("data-tmask") != 11) {
                        $(this).attr("data-tmask", 11);
                        $(this).mask("(00) 00000-0000", { clearIfNotMatch: false });
                    }
                } else if (leng <= 10) {
                    if ($(this).attr("data-tmask") != 10) {
                        $(this).attr("data-tmask", 10);
                        $(this).mask("(00) 0000-00000", { clearIfNotMatch: false });
                    }
                }
            });
        });
    }
    
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
}


/* automatic functions */
$(function() {
    fieldFunctions();
});