CKEDITOR.config.extraPlugins = 'colorbutton';
CKEDITOR.config.allowedContent = true;

function errorFunction(err, textStatus, errorThrown) {
	unblockUi();
	MessageBox.error("Ocorreu um erro. Para detalhes pressione F12 e verifique no console.");
	console.log(err, textStatus, errorThrown);
}

function confirmDialog(text, callback) {
	Swal.fire({
		title: 'Confirma&ccedil;&atilde;o',
		text,
		icon: 'warning',
		showCancelButton: true,
		showLoaderOnConfirm: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Sim',
		cancelButtonText: 'N&atilde;o'
	}).then((result) => {
		if (result.isConfirmed) {
			callback();
		}
	})
}

function showPreview(element, imgField, module) {
	var module = module || '';
	var imgField = imgField || 'img';
	var file = element.files[0];

	if (file) {
		$("#imagem_" + imgField + "_" + module).fadeIn().removeClass("d-none");
		$(`#btnchange_${imgField}_${module}`).fadeIn();
		var reader = new FileReader();
		reader.onload = function () {
			$("#preview_" + imgField + "_" + module).attr("src", reader.result);
			element.text = file.name;
		}

		reader.readAsDataURL(file);
	}
}


function deletePreviewImage(src = '', imgField, module) {
	var module = module || '';
	var imgField = imgField || 'img';
	$("#input_" + imgField + "_" + module).val("");
	if (src != '') {
		$("#preview_" + imgField + "_" + module).attr('src', src);
		$(`#btnchange_${imgField}_${module}`).fadeOut();
	}
	else {
		$("#imagem_" + imgField + "_" + module).fadeOut();
	}
}

function resetPreview(imgField, module) {
	const html = `
		<article class="card text-white card-preview">
			<div style="display: flex; align-items:center; gap:.75rem; position: absolute; right: 20px;">
				<button type="button" class="btn btn-primary btn-xs" onclick="deletePreviewImage('')">
					<span class="glyphicon glyphicon-remove"></span>
					Cancelar alteração
				</button>
			</div>
			<figure class="ratio ratio-4x3 card-img">
				<img src="" id="preview_` + imgField + `_` + module + `" class="img-preview"/>
			</figure>
		</article>`;

	$("#imagem_" + imgField + "_" + module).html(html);

}

function duplicateAction(module, id, callback) {
	callback = callback || null;

	confirmDialog("Deseja realmente duplicar o(s) registro(s) selecionado(s)?", () => {
		$.ajax({
			url: `${__PATH__}ajax/duplicate/class/${module}/id/${id}`,
			dataType: `json`,
			success: (res) => {
				if (res.success) {
					MessageBox.success(res.message);
					if (callback) {
						callback();
					}
					setTimeout(function(){
						window.location.reload();
					}, 3000);
				} else {
					MessageBox.error(res.message);
				}
			},
			error: errorFunction
		});
	});

	return false;
}

function delFormAction(module, id, callback) {
	callback = callback || null;

	confirmDialog("Deseja realmente excluir o(s) registro(s) selecionado(s)", () => {
		blockUi();
		$.ajax({
			url: `${__PATH__}ajax/del/class/${module}/id/${id}`,
			dataType: `json`,
			success: (res) => {
				unblockUi();
				if (res.success) {
					MessageBox.success(res.message);
					if (callback) {
						callback();
					}
				} else {
					MessageBox.error(res.message);
				}
			},
			error: errorFunction
		});
	});

	return false;
}

function changeStatus(module, id, to) {
	let html = `
		<a href="javascript:;" onclick="changeStatus('${module}',${id},${to == 1 ? '0' : '1'})">
			<span class="ti ti-circle-check-filled text-${to == 1 ? 'success' : 'default'}" title="${to == 1 ? 'Desativar' : 'Ativar'}"></span>
		</a>
	`;

	blockUi();
	$.ajax({
		url: `${__PATH__}ajax/change-active/class/${module}/id/${id}/to/${to}`,
		dataType: `json`,
		success: function (resp) {
			unblockUi();
			if (resp.success) {
				$(`#status${id}`).html(html);
			} else {
				MessageBox.error(resp.message);
			}
		},
		error: errorFunction,
	});

	return false;
}

function deleteImage(module, id, imagem) {
	confirmDialog("Deseja realmente excluir a imagem?", () => {
		blockUi();
		$.ajax({
			url: `${__PATH__}ajax/delete-image/class/${module}/id/${id}/imagem/${imagem}`,
			dataType: `json`,
			success: function (resp) {
				unblockUi();
				if (resp.success) {
					$(`#imagem_${imagem}_${module}`).fadeOut(`slow`);
					$(`#btndel_${imagem}_${module}`).fadeOut();
					$(`#btnchange_${imagem}_${module}`).replaceWith(
						`
					<button style='display:none;' id="btnchange_${imagem}_${module}" type="button" class="btn btn-primary btn-sm" onclick="deletePreviewImage('' , '${imagem}', '${module}')">
                        <i class="ti ti-circle-x-filled"></i>
                        Cancelar alteração
                    </button>
						`
					);
					MessageBox.success(resp.message);
				} else {
					MessageBox.error(resp.message);
				}
			},
			error: errorFunction,
		});
	});

	return false;
}

function deleteFile(module, id, field) {
	confirmDialog("Deseja realmente excluir o arquivo?", () => {
		blockUi();
		$.ajax({
			url: `${__PATH__}ajax/delete-file/class/${module}/id/${id}/field/${field}`,
			dataType: `json`,
			success: function (resp) {
				unblockUi();
				if (resp.success) {
					$(`#arquivo_${field}_${module}`).fadeOut(`slow`);
					MessageBox.success(resp.message);
				} else {
					MessageBox.error(resp.message);
				}
			},
			error: errorFunction,
		});
	});

	return false;
}

function saveOrder(module, posicao) {
	var ordem = "";
	$(".position" + posicao + " .well").each(function (i, obj) {
		ordem += (ordem == "" ? "" : "|") + $(this).attr("data-id");
	});

	$("#retorno" + posicao).html("<smal>Aguarde, salvando ordem...</smal>");

	$.ajax({
		url: `${__PATH__}ajax/change-order/class/${module}/position/${posicao}/order/${ordem}`,
		dataType: `json`,
		success: function (resp) {
			$(`#retorno${posicao}`).html(resp.message);
			if (resp.success) {
				var i = 1;
				$(".position" + posicao + " .well").each(function (o, obj) {
					$(this).find(".ordem").html(i++ + "&ordm;");
				});
			}
		},
		error: errorFunction,
	});
	return false;
}

function modalForm(module, id, params, callback, savebutton, closable) {

	const modal = new LevModal({buttons:[]});
	var savebutton = savebutton || true;
	var closable = closable || true;

	const saveFunction = (module, id, params, callback) => {

		let form = modal.dialog.find(`form`).eq(0);

		$(form).find(".ckeditor").each(function (key, textarea) {
			CKEDITOR.instances[textarea.id].updateElement();
		});
		
		form.addClass('was-validated');
		
		if (!form[0].checkValidity()) return false;

		form.ajaxSubmit({
			url: `${__PATH__}ajax/save/class/${module}/id/${id}`,
			type: `POST`,
			dataType: `json`,
			beforeSubmit: function () {
				blockOnSubmit();
				$("#progressPercent").html(`0%`);
			},
			uploadProgress: function (event, position, total, percentComplete) {
				$("#progressPercent").html(percentComplete + "%");
			},
			success: function (resp) {

				if (resp.success) {
					unblockUi();
					if(closable){
						modal.hide();
					}
					if(callback){
						callback(resp);
					}

					MessageBox.success(resp.message);
				} else {
					unblockUi();
					MessageBox.error(resp.message);
				}

			},
			error: errorFunction,
		});
	}

	var idAtual = id;

	blockUi();

	if(closable){
		modal.appendButton({
			label: `Cancelar`,
			class: `order-2 btn border-transparent opacity-50 ms-auto`,
			'data-bs-dismiss': 'modal',
		});
	}

	if(idAtual > 0){
		modal.appendButton({
			label: `Apagar`,
			class: `order-1 btn border-transparent opacity-50 text-danger-hover`,
			onClick: () => {
				delFormAction(module, idAtual, () => { 
					modal.hide(); 
					$(`#tr-${module}${idAtual}`).fadeOut(`slow`, function(){ 
						$(this).remove(); 
					}); 
				});
			},
		});
	}

	if (savebutton) {
		modal.appendButton({
			label: `Salvar`,
			class: `order-3 btn btn-secondary fw-bold text-white`,
			onClick: () => {
				saveFunction(module, idAtual, params, callback);
			},
		});
	}

	modal.loadAndShow(`${__PATH__}ajax/${id > 0 ? 'edit' : 'add'}/class/${module}/id/${id}${params}`);
}

function listenAllEventsForSource(source, callback) {
	// Get a list of all event types
	var eventTypes = Object.keys(window).filter(function(key) {
		return key.startsWith('on');
	});

	// Attach event listeners for each event type
	eventTypes.forEach(function(eventType) {
		source.addEventListener(eventType.slice(2), callback);
	});
}

function galleryRotateImage(id) {
	blockUi();

	$.ajax({
		url: __PATH__ + 'ajax/gallery-rotate-image/id/' + id,
		dataType: `json`,
		success: function (resp) {
			unblockUi();
			if (resp.success) {
				getLine(`fotos`, id);
			} else {
				MessageBox.error(resp.message);
			}
		},
		error: errorFunction
	});
}

function getName(classe, id, nameField, nameObjField) {
	if (id != "") {
		$.ajax({
			url: __PATH__ + "ajax/get-name/class/" + classe + "/id/" + id,
			dataType: "json",
			success: function (resp) {
				if (resp.success) {
					$("#" + nameField).val(resp["obj"][nameObjField]);
				} else {
					MessageBox.error(resp.message);
				}
			},
			error: errorFunction,
		});
	}
}

function modalFilter(form, module) {
    let queryArr = $(form).serializeArray();
    let queryString = '';
    let selectedClients = [];

    for (let i = 0; i < queryArr.length; i++) {
        if (queryArr[i].name === 'id_cliente') {
            selectedClients.push(queryArr[i].value);
        } else if (queryArr[i].value !== '') {
            queryString += `${queryArr[i].name}=${encodeURI(queryArr[i].value)}&`;
        }
    }

    if (selectedClients.length > 0) {
        queryString += `id_cliente=${encodeURI(selectedClients.join(','))}&`;
    }

    queryString = (queryString != '' ? queryString.slice(0, -1) : '');
    
    $('#modalFilter').modal('hide');
    tableList(module, queryString, 'resultados', true);
    
    return false;
}


function tableList(model, query, selector, changePath) {
	const imgLoading = '<img src="' + __SYSTEMPATH__ + 'css/img/loading.gif" alt="loading" width="20px" />';
	changePath = changePath || false;
	if (changePath) {
		setHref(`${model}?${query}`);
	}

	$(`[data-model="${model}"][data-type="qtdRegistros"]`).html(imgLoading);
	$(`#${selector}`).html("<p>" + imgLoading + " Aguarde, buscando registros...</p>");

	query = query.replace(/&?selector=[^&]+/g, '');
	query = query.replace(/&?changePath=[^&]+/g, '');

	var urlupdate = `${__PATH__}ajax/table-list/class/${model}?${query}${query == '' ? '' : '&'}selector=${selector}${changePath ? '&changePath=1' : ''}`;
	$.getJSON(urlupdate, function (resp) {
		if (resp.success) {
			$(`#${selector}`).html(resp.html);
			$(`[data-model="${model}"][data-type="qtdRegistros"]`).html(resp.qtd);
		} else {
			$(`#${selector}`).html(resp.message);
			$(`[data-model="${model}"][data-type="qtdRegistros"]`).html(0);
		}
		const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
		const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl,{
			trigger : 'hover'
		}))
		makeDragable();
	});
	return false;
}

function getLine(modulo, id) {
	$.ajax({
		url: `${__PATH__}ajax/get-line/class/${modulo}/id/${id}`,
		dataType: `json`,
		success: function (resp) {
			if (resp.success) {
				$("#tr-" + modulo + id).html(resp.html);
				$('[data-toggle="tooltip"]').tooltip();
			} else {
				MessageBox.error(resp.message);
			}
		},
		error: errorFunction,
	});
}

function updateOptionField(modulo, filtro, seletor, nomeCampo) {
	$.ajax({
		url: `${__PATH__}ajax/update-field/class/${modulo}/field/${nomeCampo}/?${filtro}`,
		dataType: `json`,
		success: function (resp) {
			if (resp.success) {
				$(seletor).html(resp.options);
			} else {
				MessageBox.error(resp.message);
			}
		},
		error: errorFunction,
	});
}

function updateAutoCompleteField(module, filtro, idField, nameField) {
	$.ajax({
		url: `${__PATH__}ajax/update-autocomplete-field/class/${modulo}/?${filtro}`,
		dataType: `json`,
		success: function (resp) {
			if (resp.success) {
				$("#" + idField).val(resp.id);
				$("#" + nameField).val(resp.nome);
			} else {
				MessageBox.error(resp.message);
			}
		},
		error: errorFunction,
	});
}

function configuraUrl(field, id) {
	$$(id).value = removeDiacritics(field.value.replace(/ /g, "-")).toLowerCase();
}

function setHref(href) {
	const url = `${__PATH__}${href}`;
	window.history.pushState({ url }, ``, url);
}

function copyToClipboard(text) {
	navigator.clipboard.writeText(text).then(function () {
		MessageBox.success("Copiado com sucesso!");
	}, function (err) {
		MessageBox.danger("Erro copiando texto: " + err);
	});
}

function makeDragable() {
	$(".dragable").each(function (i, obj) {
		var table = $(obj).data("table");
		$(obj).sortable({
			tolerance: 'pointer',
			revert: 'invalid',
			forceHelperSize: true,
			handle: '.drag-handler',
			deactivate(event, ui) {
				const ordem = $(obj).find('.drag-handler').map(function () {
					return $(this).data('id');
				}).get();

				var url = `${__PATH__}ajax/change-order/class/${table}/order/${ordem}`;
				$.ajax({
					url: url,
					type: `GET`,
					dataType: `json`,
					success: function (resp) {
						if (!resp.success) {
							MessageBox.error(data["data"])
						}
					},
					error: errorFunction,
				});
			},
		});
	});
}

function fieldFunctions() {

	$('input[data-type="togglePassword"]').each((index, el) => {
		showTogglePassword(el);
	});

	const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
	const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl,{
		trigger : 'hover'
	}))

	$(".date").datepicker({
		dateFormat: "dd/mm/yy",
		monthNames: ["Janeiro", "Fevereiro", "Mar&ccedil;o", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro",],
		monthNamesShort: ["Jan", "Fev", "Mar", "Abr", "Mai", "Jun", "Jul", "Ago", "Set", "Out", "Nov", "Dez",],
		dayNamesShort: ["Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "S&aacute;b"],
		dayNamesMin: ["Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "S&aacute;b"],
	});
	$(".multidate").each(function (i, obj) {
		var defdate = "today";
		if (obj.value != "") {
			defdate = obj.value.split(",", 1);
		}
		$(obj).multiDatesPicker({
			dateFormat: "dd/mm/yy",
			monthNames: ["Janeiro", "Fevereiro", "Mar&ccedil;o", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro",],
			monthNamesShort: ["Jan", "Fev", "Mar", "Abr", "Mai", "Jun", "Jul", "Ago", "Set", "Out", "Nov", "Dez",],
			dayNamesShort: ["Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "S&aacute;b"],
			dayNamesMin: ["Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "S&aacute;b"],
			defaultDate: defdate.toString(),
		});
	});

	$(".money").maskMoney({ allowNegative: true, allowZero: true, thousands: ".", decimal: "," });
	$(".onlynumbers").numeric();
	$(".date,.datewdp").mask("00/00/0000", { clearIfNotMatch: false });
	$(".datehour").mask("00/00/0000 00:00:00", { clearIfNotMatch: false });
	$(".dateshorthour").mask("00/00/0000 00:00", { clearIfNotMatch: false });
	$(".hour").mask("00:00:00", { clearIfNotMatch: false });
	$(".shorthour").mask("00:00", { clearIfNotMatch: false });
	$(".cep").mask("00.000-000", { clearIfNotMatch: false });
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

	$(".uploader").each(function (i, obj) {
		var v_id = $(obj).data("id") == 0 ? $(obj).data("tid") : $(obj).data("id");
		let filters = {};
		if ($(obj).data("type") == 'fotos') {
			filters = {
				mime_types: [{ title: "Image files", extensions: "jpg,jpeg,gif,png" }],
			};
		}

		$(obj).pluploadQueue({
			runtimes: "html5,flash,silverlight,html4",
			url: __PATH__ + "ajax/file-upload/",
			rename: true,
			dragdrop: true,
			multiple_queues: true,
			filters,
			multipart_params: {
				"class": $(obj).data("table"),
				"id": $(obj).data("id"),
				"tid": $(obj).data("tid"),
				"type": $(obj).data("type"),
			},
			flash_swf_url: __BASEPATH__ + "js/Moxie.swf",
			silverlight_xap_url: __BASEPATH__ + "js/Moxie.xap",
			init: {
				FilesAdded: function (up, files) {
					up.start();
				},
				FileUploaded: function (up, file, info) {
					setTimeout(function () {
						up.removeFile(file);
					}, 2000);
				},
				UploadComplete: function (up, files) {
					tableList($(obj).data("type"), `tipo=${$(obj).data("table")}&id_tipo=${v_id}`, $(obj).data("retorno"));
				},
			},
		});

		if ($('#' + $(obj).data("retorno")).html() == '') {
			tableList($(obj).data("type"), `tipo=${$(obj).data("table")}&id_tipo=${v_id}`, $(obj).data("retorno"));
		}
	});

	$(".autocomplete").each(function (i, obj) {
		var urlBase = __PATH__ + "ajax/auto-complete/json/false/class/" + $(obj).attr("data-table");
		$(obj).autocomplete({
			source: function (request, response) {
				url = urlBase + "/term/" + encodeURI(request.term);
				if($(obj).attr("input-aux")){
					const inputs = ($(obj).attr("input-aux").split('/'));
					url += "/input-aux/"+inputs[0];
					for(let i = 1; i < inputs.length; i++){
						url += ','+inputs[i];
					}
				}
				if($(obj).attr("data-div")){
					url += "/data-div/" + $(obj).attr("data-div");
				}

				if ($(obj).attr("data-name")) {
					url += "/camponome/" + $(obj).attr("data-name");
				}
				
				if ($(obj).attr("data-aux")) {
					url += "/campoaux/" + $(obj).attr("data-aux");
				}
				
				if ($(obj).attr("data-filter")) {
					url += "?" + $(obj).attr("data-filter");
				}
				$.ajax({
					url: url,
					dataType: "json",
					success: function (data) {
						response(data);
					},
					error: errorFunction,
				});
			},
			select: function (event, ui) {
				if($(obj).attr("input-aux")){
					const inputs = ($(obj).attr("input-aux").split('/'));
					if(ui.item.campos.length == inputs.length +1){
						for(var i = 0; i < inputs.length; i++){
							$("#"+inputs[i]).val(ui.item.campos[i+1]);
						}
					}
				}
				$("#" + $(obj).attr("data-field")).val(ui.item.value);
				$("#" + $(obj).attr("data-edit")).val(ui.item.value);
				$(obj).val(ui.item.label);
				return false;
			},
		});
	});

	$(".multisel").each(function (i, obj) {
		if ($(obj).css("display") != "none") {
			$(obj).multiselect({
				nonSelectedText: $(obj).attr("data-select") ? $(obj).attr("data-select") : "Selecione...",
				nSelectedText: " opções selecionadas",
				includeSelectAllOption: true,
				selectAllText: "Selecionar Todos",
				allSelectedText: "Todas as opções",
				numberDisplayed: $(obj).attr("data-ndisplay") ? $(obj).attr("data-ndisplay") : 0,
				enableClickableOptGroups: true,
				buttonWidth: "100%",
			});
		}
	});

	$(".multiselsearch").each(function (i, obj) {
		if ($(obj).css("display") != "none") {
			$(obj).multiselect({
				nonSelectedText: $(obj).attr("data-select") ? $(obj).attr("data-select") : "Selecione...",
				includeSelectAllOption: $(obj).attr("data-sall") == 1,
				selectAllText: $(obj).attr("data-sall") == 1 ? "Selecionar Todos" : "",
				allSelectedText: $(obj).attr("data-sall") == 1 ? "Todas as opções" : "",
				enableFiltering: true,
				enableCaseInsensitiveFiltering: true,
				filterBehavior: "both",
				filterPlaceholder: "Pesquise...",
				buttonWidth: "100%",
			});
		}
	});

	$("div.required").each((i, obj) => {
		if($(obj).find("label").length > 0 && !$(obj).find("label").text().includes("*"))
			$(obj).find("label").get(0).textContent += "*";
		if($(obj).find("input, select").length > 0 && !$(obj).find("input, select").hasClass("required")){
			$(obj).find("input, select").addClass("required")
			$(obj).find("input, select").attr('required', true);
		}
	});

	makeDragable();
}

function keepSession() {
	setTimeout(function () {
		$.ajax({
			url: __PATH__ + "ajax/keepsession",
			cache: false,
			complete: function (data) {
				keepSession();
			},
		});
	}, 300000);
}

/* automatic functions */
$(function () {
	fieldFunctions();

	const toggleDropdown = (event) => {
		var button = event.target;
		var icon = $(button).parent().find(".dropdown-icon");
		var dropdown = $(button).data("target");
		$(button).toggleClass("show");
		if ($(button).hasClass("show")) {
		  icon.css("transform", "rotate(-180deg)");
		  $(dropdown).slideDown('fast');
		} else {
		  icon.css("transform", "rotate(0deg)");
		  $(dropdown).slideUp('fast');
		}
	  };
	  
	const closeDropdown = (button) => {
		var icon = $(button).parent().find(".dropdown-icon");
		var dropdown = $(button).data("target");
		$(button).toggleClass("show");
		if ($(button).hasClass("show")) {
			icon.css("transform", "rotate(-180deg)");
			$(dropdown).slideDown('fast');
		} else {
			icon.css("transform", "rotate(0deg)");
			$(dropdown).slideUp('fast');
		}
	};
	
	$('[data-toggle="flex-dropdown"]').click(toggleDropdown);
	
	$(document).click((evt) => {
		var element = evt.target;
		if (!$(".flex-dropdown").find(element).length) {
			closeDropdown($('[data-toggle="flex-dropdown"].show'));
		}
	});

	keepSession();
	var fullHeight = function () {

		$('.js-fullheight').css('height', $(window).height());
		$(window).resize(function () {
			$('.js-fullheight').css('height', $(window).height());
		});

	};
	fullHeight();

	$("[data-toggle='sidebarCollapse']").on('click', function () {
		$('#sidebar').toggleClass('collapse');
		$('#content').toggleClass('m-collapse-menu');
	});

	$(window).keydown(function(event) {
		if(!$(`body`).hasClass('modal-open')){
			if (event.which == 113) { //F2
				if ($('#btnAddBase').length > 0) {
					$(`#btnAddBase`).trigger('click');
				}
				return false;
			}
			else if (event.which == 114) { //F3
				if ($('#btnSearchBase').length > 0) {
					$(`#btnSearchBase`).trigger('click');
				}
				return false;
			}
		}
    });
});

function montarPdf(module, id_aluguel){
    const url = __PATH__+module+'/montar-pdf/id/' + id_aluguel;
    window.location.href = url;
}