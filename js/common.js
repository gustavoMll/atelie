const togglePasswordType = (button) => {
	var icon = $(button.parentElement).find('i.ti');
	var input = $(button.parentElement).find('input');
	if (input.attr('type') === 'password') {
		input.attr('type', 'text');
		icon.removeClass('ti-eye-off');
		icon.addClass('ti-eye');
	} else {
		input.attr('type', 'password');
		icon.removeClass('ti-eye');
		icon.addClass('ti-eye-off');
	}
}

const showTogglePassword = (input) => {
	var parent = $(input).parent();
	parent.addClass("position-relative");
	$(`
    <a href="javascript:;" aria-label="Show/Hide Password" class="text-primary position-absolute top-50 translate-middle-y h5" data-type="togglePassword" style="right: 10px;" onclick="togglePasswordType(this);">
      <i class="ti ti-eye-off fs-4"></i>
    </a>
  `).insertAfter(input);
}

jQuery.fn.preventDoubleSubmission = function () {
	$(this).bind("submit", function (e) {
		var $form = $(this);

		if ($form.data("submitted") === true) {
			// Previously submitted - don't submit again
			e.preventDefault();
		} else {
			// Mark it so that the next submit can be ignored
			$form.data("submitted", true);
		}
	});

	// Keep chainability
	return this;
};

function $$(s) {
	return document.getElementById(s);
}

function validaForm(form) {
    var seg = true;
    var applyOnblur;
    
    for (var i = 0; i < form.elements.length; i++) {
        applyOnblur = false;
        var elem = form.elements[i];
        elem.className = elem.className.replace(/(?:^|\s)error(?!\S)/, '');
        
        if (elem.className.search('ckEmail') > -1 && elem.className.search('required') > -1) {
            if (!checkMail(elem.value)) {
                elem.className += " error";
                seg = false;
                applyOnblur = true;
            }

        } else if (elem.className.search('ckDate') > -1 && elem.className.search('required') > -1) {
            if (!validatedate(elem.value)) {
                elem.className += " error";
                seg = false;
                applyOnblur = true;
            }
        } else {
            if (elem.className.search('required') > -1 && elem.value.length < 1) {
                elem.className += " error";
                seg = false;
                applyOnblur = true;
            }
        }
        
        if(applyOnblur){
            if(elem && !elem.onblur || elem.getAttribute('onblur').search('removeErrorTag') < 0){
                elem.setAttribute('onblur',elem.getAttribute('onblur')+';removeErrorTag(this);');
            }
        }
    }
    if(seg){
        $('#'+form.id).preventDoubleSubmission();
    }else{
        BootstrapDialog.show({
            title: 'Erro',
            message: 'Alguns campos do formul&aacute;rio n&atilde;o foram preenchidos corretamente. Favor conferir.',
            type: BootstrapDialog.TYPE_DANGER
        });
    }
    return seg;
}

function MD5(string) {
	function RotateLeft(lValue, iShiftBits) {
		return (lValue << iShiftBits) | (lValue >>> (32 - iShiftBits));
	}
	function AddUnsigned(lX, lY) {
		var lX4, lY4, lX8, lY8, lResult;
		lX8 = lX & 0x80000000;
		lY8 = lY & 0x80000000;
		lX4 = lX & 0x40000000;
		lY4 = lY & 0x40000000;
		lResult = (lX & 0x3fffffff) + (lY & 0x3fffffff);
		if (lX4 & lY4) {
			return lResult ^ 0x80000000 ^ lX8 ^ lY8;
		}
		if (lX4 | lY4) {
			if (lResult & 0x40000000) {
				return lResult ^ 0xc0000000 ^ lX8 ^ lY8;
			} else {
				return lResult ^ 0x40000000 ^ lX8 ^ lY8;
			}
		} else {
			return lResult ^ lX8 ^ lY8;
		}
	}

	function F(x, y, z) {
		return (x & y) | (~x & z);
	}
	function G(x, y, z) {
		return (x & z) | (y & ~z);
	}
	function H(x, y, z) {
		return x ^ y ^ z;
	}
	function I(x, y, z) {
		return y ^ (x | ~z);
	}
	function FF(a, b, c, d, x, s, ac) {
		a = AddUnsigned(a, AddUnsigned(AddUnsigned(F(b, c, d), x), ac));
		return AddUnsigned(RotateLeft(a, s), b);
	}

	function GG(a, b, c, d, x, s, ac) {
		a = AddUnsigned(a, AddUnsigned(AddUnsigned(G(b, c, d), x), ac));
		return AddUnsigned(RotateLeft(a, s), b);
	}

	function HH(a, b, c, d, x, s, ac) {
		a = AddUnsigned(a, AddUnsigned(AddUnsigned(H(b, c, d), x), ac));
		return AddUnsigned(RotateLeft(a, s), b);
	}

	function II(a, b, c, d, x, s, ac) {
		a = AddUnsigned(a, AddUnsigned(AddUnsigned(I(b, c, d), x), ac));
		return AddUnsigned(RotateLeft(a, s), b);
	}

	function ConvertToWordArray(string) {
		var lWordCount;
		var lMessageLength = string.length;
		var lNumberOfWords_temp1 = lMessageLength + 8;
		var lNumberOfWords_temp2 =
			(lNumberOfWords_temp1 - (lNumberOfWords_temp1 % 64)) / 64;
		var lNumberOfWords = (lNumberOfWords_temp2 + 1) * 16;
		var lWordArray = Array(lNumberOfWords - 1);
		var lBytePosition = 0;
		var lByteCount = 0;
		while (lByteCount < lMessageLength) {
			lWordCount = (lByteCount - (lByteCount % 4)) / 4;
			lBytePosition = (lByteCount % 4) * 8;
			lWordArray[lWordCount] =
				lWordArray[lWordCount] |
				(string.charCodeAt(lByteCount) << lBytePosition);
			lByteCount++;
		}
		lWordCount = (lByteCount - (lByteCount % 4)) / 4;
		lBytePosition = (lByteCount % 4) * 8;
		lWordArray[lWordCount] = lWordArray[lWordCount] | (0x80 << lBytePosition);
		lWordArray[lNumberOfWords - 2] = lMessageLength << 3;
		lWordArray[lNumberOfWords - 1] = lMessageLength >>> 29;
		return lWordArray;
	}

	function WordToHex(lValue) {
		var WordToHexValue = "",
			WordToHexValue_temp = "",
			lByte,
			lCount;
		for (lCount = 0; lCount <= 3; lCount++) {
			lByte = (lValue >>> (lCount * 8)) & 255;
			WordToHexValue_temp = "0" + lByte.toString(16);
			WordToHexValue =
				WordToHexValue +
				WordToHexValue_temp.substr(WordToHexValue_temp.length - 2, 2);
		}
		return WordToHexValue;
	}

	function Utf8Encode(str) {
		str = ("" + str).replace(/\r\n/g, "\n");
		var utftext = "";

		for (var n = 0; n < str.length; n++) {
			var c = str.charCodeAt(n);

			if (c < 128) {
				utftext += String.fromCharCode(c);
			} else if (c > 127 && c < 2048) {
				utftext += String.fromCharCode((c >> 6) | 192);
				utftext += String.fromCharCode((c & 63) | 128);
			} else {
				utftext += String.fromCharCode((c >> 12) | 224);
				utftext += String.fromCharCode(((c >> 6) & 63) | 128);
				utftext += String.fromCharCode((c & 63) | 128);
			}
		}

		return utftext;
	}

	var x = Array();
	var k, AA, BB, CC, DD, a, b, c, d;
	var S11 = 7,
		S12 = 12,
		S13 = 17,
		S14 = 22;
	var S21 = 5,
		S22 = 9,
		S23 = 14,
		S24 = 20;
	var S31 = 4,
		S32 = 11,
		S33 = 16,
		S34 = 23;
	var S41 = 6,
		S42 = 10,
		S43 = 15,
		S44 = 21;

	string = Utf8Encode(string);

	x = ConvertToWordArray(string);

	a = 0x67452301;
	b = 0xefcdab89;
	c = 0x98badcfe;
	d = 0x10325476;

	for (k = 0; k < x.length; k += 16) {
		AA = a;
		BB = b;
		CC = c;
		DD = d;
		a = FF(a, b, c, d, x[k + 0], S11, 0xd76aa478);
		d = FF(d, a, b, c, x[k + 1], S12, 0xe8c7b756);
		c = FF(c, d, a, b, x[k + 2], S13, 0x242070db);
		b = FF(b, c, d, a, x[k + 3], S14, 0xc1bdceee);
		a = FF(a, b, c, d, x[k + 4], S11, 0xf57c0faf);
		d = FF(d, a, b, c, x[k + 5], S12, 0x4787c62a);
		c = FF(c, d, a, b, x[k + 6], S13, 0xa8304613);
		b = FF(b, c, d, a, x[k + 7], S14, 0xfd469501);
		a = FF(a, b, c, d, x[k + 8], S11, 0x698098d8);
		d = FF(d, a, b, c, x[k + 9], S12, 0x8b44f7af);
		c = FF(c, d, a, b, x[k + 10], S13, 0xffff5bb1);
		b = FF(b, c, d, a, x[k + 11], S14, 0x895cd7be);
		a = FF(a, b, c, d, x[k + 12], S11, 0x6b901122);
		d = FF(d, a, b, c, x[k + 13], S12, 0xfd987193);
		c = FF(c, d, a, b, x[k + 14], S13, 0xa679438e);
		b = FF(b, c, d, a, x[k + 15], S14, 0x49b40821);
		a = GG(a, b, c, d, x[k + 1], S21, 0xf61e2562);
		d = GG(d, a, b, c, x[k + 6], S22, 0xc040b340);
		c = GG(c, d, a, b, x[k + 11], S23, 0x265e5a51);
		b = GG(b, c, d, a, x[k + 0], S24, 0xe9b6c7aa);
		a = GG(a, b, c, d, x[k + 5], S21, 0xd62f105d);
		d = GG(d, a, b, c, x[k + 10], S22, 0x2441453);
		c = GG(c, d, a, b, x[k + 15], S23, 0xd8a1e681);
		b = GG(b, c, d, a, x[k + 4], S24, 0xe7d3fbc8);
		a = GG(a, b, c, d, x[k + 9], S21, 0x21e1cde6);
		d = GG(d, a, b, c, x[k + 14], S22, 0xc33707d6);
		c = GG(c, d, a, b, x[k + 3], S23, 0xf4d50d87);
		b = GG(b, c, d, a, x[k + 8], S24, 0x455a14ed);
		a = GG(a, b, c, d, x[k + 13], S21, 0xa9e3e905);
		d = GG(d, a, b, c, x[k + 2], S22, 0xfcefa3f8);
		c = GG(c, d, a, b, x[k + 7], S23, 0x676f02d9);
		b = GG(b, c, d, a, x[k + 12], S24, 0x8d2a4c8a);
		a = HH(a, b, c, d, x[k + 5], S31, 0xfffa3942);
		d = HH(d, a, b, c, x[k + 8], S32, 0x8771f681);
		c = HH(c, d, a, b, x[k + 11], S33, 0x6d9d6122);
		b = HH(b, c, d, a, x[k + 14], S34, 0xfde5380c);
		a = HH(a, b, c, d, x[k + 1], S31, 0xa4beea44);
		d = HH(d, a, b, c, x[k + 4], S32, 0x4bdecfa9);
		c = HH(c, d, a, b, x[k + 7], S33, 0xf6bb4b60);
		b = HH(b, c, d, a, x[k + 10], S34, 0xbebfbc70);
		a = HH(a, b, c, d, x[k + 13], S31, 0x289b7ec6);
		d = HH(d, a, b, c, x[k + 0], S32, 0xeaa127fa);
		c = HH(c, d, a, b, x[k + 3], S33, 0xd4ef3085);
		b = HH(b, c, d, a, x[k + 6], S34, 0x4881d05);
		a = HH(a, b, c, d, x[k + 9], S31, 0xd9d4d039);
		d = HH(d, a, b, c, x[k + 12], S32, 0xe6db99e5);
		c = HH(c, d, a, b, x[k + 15], S33, 0x1fa27cf8);
		b = HH(b, c, d, a, x[k + 2], S34, 0xc4ac5665);
		a = II(a, b, c, d, x[k + 0], S41, 0xf4292244);
		d = II(d, a, b, c, x[k + 7], S42, 0x432aff97);
		c = II(c, d, a, b, x[k + 14], S43, 0xab9423a7);
		b = II(b, c, d, a, x[k + 5], S44, 0xfc93a039);
		a = II(a, b, c, d, x[k + 12], S41, 0x655b59c3);
		d = II(d, a, b, c, x[k + 3], S42, 0x8f0ccc92);
		c = II(c, d, a, b, x[k + 10], S43, 0xffeff47d);
		b = II(b, c, d, a, x[k + 1], S44, 0x85845dd1);
		a = II(a, b, c, d, x[k + 8], S41, 0x6fa87e4f);
		d = II(d, a, b, c, x[k + 15], S42, 0xfe2ce6e0);
		c = II(c, d, a, b, x[k + 6], S43, 0xa3014314);
		b = II(b, c, d, a, x[k + 13], S44, 0x4e0811a1);
		a = II(a, b, c, d, x[k + 4], S41, 0xf7537e82);
		d = II(d, a, b, c, x[k + 11], S42, 0xbd3af235);
		c = II(c, d, a, b, x[k + 2], S43, 0x2ad7d2bb);
		b = II(b, c, d, a, x[k + 9], S44, 0xeb86d391);
		a = AddUnsigned(a, AA);
		b = AddUnsigned(b, BB);
		c = AddUnsigned(c, CC);
		d = AddUnsigned(d, DD);
	}
	var temp = WordToHex(a) + WordToHex(b) + WordToHex(c) + WordToHex(d);
	return temp.toLowerCase();
}

function str_pad(input, pad_length, pad_string, pad_type) {
	var half = "",
		pad_to_go;

	var str_pad_repeater = function (s, len) {
		var collect = "",
			i;

		while (collect.length < len) {
			collect += s;
		}
		collect = collect.substr(0, len);
		return collect;
	};
	input += "";
	pad_string = pad_string !== undefined ? pad_string : " ";
	if (
		pad_type != "STR_PAD_LEFT" &&
		pad_type != "STR_PAD_RIGHT" &&
		pad_type != "STR_PAD_BOTH"
	) {
		pad_type = "STR_PAD_RIGHT";
	}
	if ((pad_to_go = pad_length - input.length) > 0) {
		if (pad_type == "STR_PAD_LEFT") {
			input = str_pad_repeater(pad_string, pad_to_go) + input;
		} else if (pad_type == "STR_PAD_RIGHT") {
			input = input + str_pad_repeater(pad_string, pad_to_go);
		} else if (pad_type == "STR_PAD_BOTH") {
			half = str_pad_repeater(pad_string, Math.ceil(pad_to_go / 2));
			input = half + input + half;
			input = input.substr(0, pad_length);
		}
	}

	return input;
}

function number_format(number, decimals, dec_point, thousands_sep) {
	number = (number + "").replace(/[^0-9+\-Ee.]/g, "");
	var n = !isFinite(+number) ? 0 : +number,
		prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
		sep = typeof thousands_sep === "undefined" ? "," : thousands_sep,
		dec = typeof dec_point === "undefined" ? "." : dec_point,
		s = "",
		toFixedFix = function (n, prec) {
			var k = Math.pow(10, prec);
			return "" + Math.round(n * k) / k;
		};
	// Fix for IE parseFloat(0.55).toFixed(0) = 0;
	s = (prec ? toFixedFix(n, prec) : "" + Math.round(n)).split(".");
	if (s[0].length > 3) {
		s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
	}
	if ((s[1] || "").length < prec) {
		s[1] = s[1] || "";
		s[1] += new Array(prec - s[1].length + 1).join("0");
	}
	return s.join(dec);
}

function string2Float(text, parse) {
	return parseFloat(text.replace(/[^0-9,]+/g, "").replace(",", "."));
}

function parseMoney(text) {
	return number_format(text, 2, ",", ".");
}

function validatedate(dtValue) {
	var dateformat1 = /^([0-9]{2})\/([0-9]{2})\/([0-9]{4})$/;
	var dateformat2 = /^([0-9]{4})-([0-9]{2})-([0-9]{2})$/;

	var valid = true;
	// Match the date format through regular expression
	if (dtValue.match(dateformat1)) {
		var pdate = dtValue.split("/");
		var dd = parseInt(pdate[0]);
		var mm = parseInt(pdate[1]);
		var yy = parseInt(pdate[2]);
	} else if (dtValue.match(dateformat2)) {
		var pdate = dtValue.split("-");
		var dd = parseInt(pdate[2]);
		var mm = parseInt(pdate[1]);
		var yy = parseInt(pdate[0]);
	} else {
		valid = false;
	}

	if (valid) {
		// Create list of days of a month [assume there is no leap year by default]
		var ListofDays = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
		if (mm == 1 || mm > 2) {
			if (dd > ListofDays[mm - 1]) {
				return false;
			}
		}
		if (mm == 2) {
			var lyear = false;
			if ((!(yy % 4) && yy % 100) || !(yy % 400)) {
				lyear = true;
			}
			if (lyear == false && dd >= 29) {
				return false;
			}
			if (lyear == true && dd > 29) {
				return false;
			}
		}
		return true;
	} else {
		return false;
	}
}

function checkMail(mail) {
	var er = new RegExp(/^[A-Za-z0-9_\-\.]+@[A-Za-z0-9_\-\.]{2,}\.[A-Za-z0-9]{2,}(\.[A-Za-z0-9])?/);
	if (typeof mail == "string") {
		if (er.test(mail)) {
			return true;
		}
	} else if (typeof mail == "object") {
		if (er.test(mail.value)) {
			return true;
		}
	} else {
		return false;
	}
}

function selectOptionBy(type, obj, value) {
	for (var i = 0; i < obj.options.length; i++) {
		if (type == "text") {
			if (obj.options[i].text == value) {
				obj.options[i].selected = true;
			}
		} else {
			if (obj.options[i].value == value) {
				obj.options[i].selected = true;
			}
		}
	}
	return true;
}

function inArray(busca, array) {
	for (var i = 0; i < array.length; i++) {
		if (array[i] == busca) return true;
	}
	return false;
}

function tAlert(title, message, type) {
	const options = { closeButton: true, progressBar: true };
	if (type == "error" || type == "e") {
		toastr.error(message, title, options);
	} else if (type == "warning" || type == "w") {
		toastr.warning(message, title, options);
	} else {
		toastr.success(message, title, options);
	}
}

function getCidades(id_estado, id_cidade){
	let ret = $(`#selectCidades`);
	$.ajax({
		url: `${__PATH__}ajax/selecionar-cidades/id_estado/${id_estado}/id_cidade/${id_cidade}`,
		type: `post`,
		dataType: `json`,
		success: function (resp) {
			ret.html(resp.html);
		},
		error: function (resp) {
			console.log(resp);
			ret.html(`Ocorreu um erro`);
		}
	});
	return false;
}

function getAddress(idField='') {

	function getDataFromApi(cep) {
		$.getJSON(
			"https://viacep.com.br/ws/" + cep.replace(/[^0-9]/g, "") + "/json/",
			function (data) {
				if (data) {
					$(`#${idField}endereco`).val(data.logradouro);
					$(`#${idField}bairro`).val(data.bairro);
					if ($(`#${idField}id_cidade`)) $(`#${idField}id_cidade`).val(data.ibge);
					$(`#${idField}cidade`).val(data.localidade);
					$(`#${idField}estado option`).each(function () {
						if ($(this).val() == data.uf || $(this).data('uf') == data.uf) {
							$(`#${idField}estado`).val($(this).val());		
						}
					});
					if($(`#${idField}selectCidades`).length > 0){
						getCidades($(`#${idField}estado`).val(), data.ibge);
					}
				}
			}
		);
	}

	if ($.trim($(`#${idField}cep`).val()) != "") {
		if ($(`#${idField}endereco`).val() == "") {
			getDataFromApi($(`#${idField}cep`).val());
		} else {
			Swal.fire({
				title: 'Confirma&ccedil;&atilde;o',
				text: "Deseja substituir o cep existe?",
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: 'var(--atelie-secondary)',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Sim, substituir!'
			}).then((result) => {
				if (result.isConfirmed) {
					getDataFromApi($(`#${idField}cep`).val());
				}
			});
		}
	}
}

function showErrorAlert(title, message) {
	MessageBox.error(message, title);
}

function unblockUi() {
	$.unblockUI();
}

function blockUiConfig(message) {
	$.blockUI({
		message,
		css: {
			border: "none",
			padding: "15px",
			backgroundColor: "#000",
			"-webkit-border-radius": "10px",
			"-moz-border-radius": "10px",
			opacity: 1,
			color: "#fff",
			"z-index": 1052,
		},
		overlayCSS: {
			"z-index": 1051,
		},
	});
}

function blockUi() {
	blockUiConfig('<img src="' +__SYSTEMPATH__ +'css/img/loading.gif" width="20px" /> Aguarde...');
}

function blockOnSubmit() {
	blockUiConfig('<img src="' +__SYSTEMPATH__ +'css/img/loading.gif" width="20px" /> <span id="progressPercent">0%</span>');
}

function blockUiMsg(message, timeout, callback) {
	var cb = callback || "unblockUi()";
	var timeout = timeout || 5000;
	var count = timeout / 1000;

	message +=
		'<br><em class="small">Esta janela se fechar&aacute; em <span id="msg_seg">' +
		count +
		'</span> segundos. <a href="javascript:;" onclick="' +
		cb +
		'">Fechar agora!</a></em>';

	blockUiConfig(message);

	var tout;
	function dcounter() {
		$("#msg_seg").html(count);
		if (count > 0) {
			count--;
			tout = setTimeout(function () {
				dcounter();
			}, 1000);
		}
	}

	dcounter();
	setTimeout(function () {
		clearTimeout(tout);
		eval(cb);
	}, timeout);
}

function removeDiacritics(str) {
	var defaultDiacriticsRemovalMap = [
		{
			base: "A",
			letters: /[\u0041\u24B6\uFF21\u00C0\u00C1\u00C2\u1EA6\u1EA4\u1EAA\u1EA8\u00C3\u0100\u0102\u1EB0\u1EAE\u1EB4\u1EB2\u0226\u01E0\u00C4\u01DE\u1EA2\u00C5\u01FA\u01CD\u0200\u0202\u1EA0\u1EAC\u1EB6\u1E00\u0104\u023A\u2C6F]/g,
		},
		{ base: "AA", letters: /[\uA732]/g },
		{ base: "AE", letters: /[\u00C6\u01FC\u01E2]/g },
		{ base: "AO", letters: /[\uA734]/g },
		{ base: "AU", letters: /[\uA736]/g },
		{ base: "AV", letters: /[\uA738\uA73A]/g },
		{ base: "AY", letters: /[\uA73C]/g },
		{
			base: "B",
			letters: /[\u0042\u24B7\uFF22\u1E02\u1E04\u1E06\u0243\u0182\u0181]/g,
		},
		{
			base: "C",
			letters: /[\u0043\u24B8\uFF23\u0106\u0108\u010A\u010C\u00C7\u1E08\u0187\u023B\uA73E]/g,
		},
		{
			base: "D",
			letters: /[\u0044\u24B9\uFF24\u1E0A\u010E\u1E0C\u1E10\u1E12\u1E0E\u0110\u018B\u018A\u0189\uA779]/g,
		},
		{ base: "DZ", letters: /[\u01F1\u01C4]/g },
		{ base: "Dz", letters: /[\u01F2\u01C5]/g },
		{
			base: "E",
			letters: /[\u0045\u24BA\uFF25\u00C8\u00C9\u00CA\u1EC0\u1EBE\u1EC4\u1EC2\u1EBC\u0112\u1E14\u1E16\u0114\u0116\u00CB\u1EBA\u011A\u0204\u0206\u1EB8\u1EC6\u0228\u1E1C\u0118\u1E18\u1E1A\u0190\u018E]/g,
		},
		{ base: "F", letters: /[\u0046\u24BB\uFF26\u1E1E\u0191\uA77B]/g },
		{
			base: "G",
			letters: /[\u0047\u24BC\uFF27\u01F4\u011C\u1E20\u011E\u0120\u01E6\u0122\u01E4\u0193\uA7A0\uA77D\uA77E]/g,
		},
		{
			base: "H",
			letters: /[\u0048\u24BD\uFF28\u0124\u1E22\u1E26\u021E\u1E24\u1E28\u1E2A\u0126\u2C67\u2C75\uA78D]/g,
		},
		{
			base: "I",
			letters: /[\u0049\u24BE\uFF29\u00CC\u00CD\u00CE\u0128\u012A\u012C\u0130\u00CF\u1E2E\u1EC8\u01CF\u0208\u020A\u1ECA\u012E\u1E2C\u0197]/g,
		},
		{ base: "J", letters: /[\u004A\u24BF\uFF2A\u0134\u0248]/g },
		{
			base: "K",
			letters: /[\u004B\u24C0\uFF2B\u1E30\u01E8\u1E32\u0136\u1E34\u0198\u2C69\uA740\uA742\uA744\uA7A2]/g,
		},
		{
			base: "L",
			letters: /[\u004C\u24C1\uFF2C\u013F\u0139\u013D\u1E36\u1E38\u013B\u1E3C\u1E3A\u0141\u023D\u2C62\u2C60\uA748\uA746\uA780]/g,
		},
		{ base: "LJ", letters: /[\u01C7]/g },
		{ base: "Lj", letters: /[\u01C8]/g },
		{
			base: "M",
			letters: /[\u004D\u24C2\uFF2D\u1E3E\u1E40\u1E42\u2C6E\u019C]/g,
		},
		{
			base: "N",
			letters: /[\u004E\u24C3\uFF2E\u01F8\u0143\u00D1\u1E44\u0147\u1E46\u0145\u1E4A\u1E48\u0220\u019D\uA790\uA7A4]/g,
		},
		{ base: "NJ", letters: /[\u01CA]/g },
		{ base: "Nj", letters: /[\u01CB]/g },
		{
			base: "O",
			letters: /[\u004F\u24C4\uFF2F\u00D2\u00D3\u00D4\u1ED2\u1ED0\u1ED6\u1ED4\u00D5\u1E4C\u022C\u1E4E\u014C\u1E50\u1E52\u014E\u022E\u0230\u00D6\u022A\u1ECE\u0150\u01D1\u020C\u020E\u01A0\u1EDC\u1EDA\u1EE0\u1EDE\u1EE2\u1ECC\u1ED8\u01EA\u01EC\u00D8\u01FE\u0186\u019F\uA74A\uA74C]/g,
		},
		{ base: "OI", letters: /[\u01A2]/g },
		{ base: "OO", letters: /[\uA74E]/g },
		{ base: "OU", letters: /[\u0222]/g },
		{
			base: "P",
			letters: /[\u0050\u24C5\uFF30\u1E54\u1E56\u01A4\u2C63\uA750\uA752\uA754]/g,
		},
		{ base: "Q", letters: /[\u0051\u24C6\uFF31\uA756\uA758\u024A]/g },
		{
			base: "R",
			letters: /[\u0052\u24C7\uFF32\u0154\u1E58\u0158\u0210\u0212\u1E5A\u1E5C\u0156\u1E5E\u024C\u2C64\uA75A\uA7A6\uA782]/g,
		},
		{
			base: "S",
			letters: /[\u0053\u24C8\uFF33\u1E9E\u015A\u1E64\u015C\u1E60\u0160\u1E66\u1E62\u1E68\u0218\u015E\u2C7E\uA7A8\uA784]/g,
		},
		{
			base: "T",
			letters: /[\u0054\u24C9\uFF34\u1E6A\u0164\u1E6C\u021A\u0162\u1E70\u1E6E\u0166\u01AC\u01AE\u023E\uA786]/g,
		},
		{ base: "TZ", letters: /[\uA728]/g },
		{
			base: "U",
			letters: /[\u0055\u24CA\uFF35\u00D9\u00DA\u00DB\u0168\u1E78\u016A\u1E7A\u016C\u00DC\u01DB\u01D7\u01D5\u01D9\u1EE6\u016E\u0170\u01D3\u0214\u0216\u01AF\u1EEA\u1EE8\u1EEE\u1EEC\u1EF0\u1EE4\u1E72\u0172\u1E76\u1E74\u0244]/g,
		},
		{
			base: "V",
			letters: /[\u0056\u24CB\uFF36\u1E7C\u1E7E\u01B2\uA75E\u0245]/g,
		},
		{ base: "VY", letters: /[\uA760]/g },
		{
			base: "W",
			letters: /[\u0057\u24CC\uFF37\u1E80\u1E82\u0174\u1E86\u1E84\u1E88\u2C72]/g,
		},
		{ base: "X", letters: /[\u0058\u24CD\uFF38\u1E8A\u1E8C]/g },
		{
			base: "Y",
			letters: /[\u0059\u24CE\uFF39\u1EF2\u00DD\u0176\u1EF8\u0232\u1E8E\u0178\u1EF6\u1EF4\u01B3\u024E\u1EFE]/g,
		},
		{
			base: "Z",
			letters: /[\u005A\u24CF\uFF3A\u0179\u1E90\u017B\u017D\u1E92\u1E94\u01B5\u0224\u2C7F\u2C6B\uA762]/g,
		},
		{
			base: "a",
			letters: /[\u0061\u24D0\uFF41\u1E9A\u00E0\u00E1\u00E2\u1EA7\u1EA5\u1EAB\u1EA9\u00E3\u0101\u0103\u1EB1\u1EAF\u1EB5\u1EB3\u0227\u01E1\u00E4\u01DF\u1EA3\u00E5\u01FB\u01CE\u0201\u0203\u1EA1\u1EAD\u1EB7\u1E01\u0105\u2C65\u0250]/g,
		},
		{ base: "aa", letters: /[\uA733]/g },
		{ base: "ae", letters: /[\u00E6\u01FD\u01E3]/g },
		{ base: "ao", letters: /[\uA735]/g },
		{ base: "au", letters: /[\uA737]/g },
		{ base: "av", letters: /[\uA739\uA73B]/g },
		{ base: "ay", letters: /[\uA73D]/g },
		{
			base: "b",
			letters: /[\u0062\u24D1\uFF42\u1E03\u1E05\u1E07\u0180\u0183\u0253]/g,
		},
		{
			base: "c",
			letters: /[\u0063\u24D2\uFF43\u0107\u0109\u010B\u010D\u00E7\u1E09\u0188\u023C\uA73F\u2184]/g,
		},
		{
			base: "d",
			letters: /[\u0064\u24D3\uFF44\u1E0B\u010F\u1E0D\u1E11\u1E13\u1E0F\u0111\u018C\u0256\u0257\uA77A]/g,
		},
		{ base: "dz", letters: /[\u01F3\u01C6]/g },
		{
			base: "e",
			letters: /[\u0065\u24D4\uFF45\u00E8\u00E9\u00EA\u1EC1\u1EBF\u1EC5\u1EC3\u1EBD\u0113\u1E15\u1E17\u0115\u0117\u00EB\u1EBB\u011B\u0205\u0207\u1EB9\u1EC7\u0229\u1E1D\u0119\u1E19\u1E1B\u0247\u025B\u01DD]/g,
		},
		{ base: "f", letters: /[\u0066\u24D5\uFF46\u1E1F\u0192\uA77C]/g },
		{
			base: "g",
			letters: /[\u0067\u24D6\uFF47\u01F5\u011D\u1E21\u011F\u0121\u01E7\u0123\u01E5\u0260\uA7A1\u1D79\uA77F]/g,
		},
		{
			base: "h",
			letters: /[\u0068\u24D7\uFF48\u0125\u1E23\u1E27\u021F\u1E25\u1E29\u1E2B\u1E96\u0127\u2C68\u2C76\u0265]/g,
		},
		{ base: "hv", letters: /[\u0195]/g },
		{
			base: "i",
			letters: /[\u0069\u24D8\uFF49\u00EC\u00ED\u00EE\u0129\u012B\u012D\u00EF\u1E2F\u1EC9\u01D0\u0209\u020B\u1ECB\u012F\u1E2D\u0268\u0131]/g,
		},
		{ base: "j", letters: /[\u006A\u24D9\uFF4A\u0135\u01F0\u0249]/g },
		{
			base: "k",
			letters: /[\u006B\u24DA\uFF4B\u1E31\u01E9\u1E33\u0137\u1E35\u0199\u2C6A\uA741\uA743\uA745\uA7A3]/g,
		},
		{
			base: "l",
			letters: /[\u006C\u24DB\uFF4C\u0140\u013A\u013E\u1E37\u1E39\u013C\u1E3D\u1E3B\u017F\u0142\u019A\u026B\u2C61\uA749\uA781\uA747]/g,
		},
		{ base: "lj", letters: /[\u01C9]/g },
		{
			base: "m",
			letters: /[\u006D\u24DC\uFF4D\u1E3F\u1E41\u1E43\u0271\u026F]/g,
		},
		{
			base: "n",
			letters: /[\u006E\u24DD\uFF4E\u01F9\u0144\u00F1\u1E45\u0148\u1E47\u0146\u1E4B\u1E49\u019E\u0272\u0149\uA791\uA7A5]/g,
		},
		{ base: "nj", letters: /[\u01CC]/g },
		{
			base: "o",
			letters: /[\u006F\u24DE\uFF4F\u00F2\u00F3\u00F4\u1ED3\u1ED1\u1ED7\u1ED5\u00F5\u1E4D\u022D\u1E4F\u014D\u1E51\u1E53\u014F\u022F\u0231\u00F6\u022B\u1ECF\u0151\u01D2\u020D\u020F\u01A1\u1EDD\u1EDB\u1EE1\u1EDF\u1EE3\u1ECD\u1ED9\u01EB\u01ED\u00F8\u01FF\u0254\uA74B\uA74D\u0275]/g,
		},
		{ base: "oi", letters: /[\u01A3]/g },
		{ base: "ou", letters: /[\u0223]/g },
		{ base: "oo", letters: /[\uA74F]/g },
		{
			base: "p",
			letters: /[\u0070\u24DF\uFF50\u1E55\u1E57\u01A5\u1D7D\uA751\uA753\uA755]/g,
		},
		{ base: "q", letters: /[\u0071\u24E0\uFF51\u024B\uA757\uA759]/g },
		{
			base: "r",
			letters: /[\u0072\u24E1\uFF52\u0155\u1E59\u0159\u0211\u0213\u1E5B\u1E5D\u0157\u1E5F\u024D\u027D\uA75B\uA7A7\uA783]/g,
		},
		{
			base: "s",
			letters: /[\u0073\u24E2\uFF53\u00DF\u015B\u1E65\u015D\u1E61\u0161\u1E67\u1E63\u1E69\u0219\u015F\u023F\uA7A9\uA785\u1E9B]/g,
		},
		{
			base: "t",
			letters: /[\u0074\u24E3\uFF54\u1E6B\u1E97\u0165\u1E6D\u021B\u0163\u1E71\u1E6F\u0167\u01AD\u0288\u2C66\uA787]/g,
		},
		{ base: "tz", letters: /[\uA729]/g },
		{
			base: "u",
			letters: /[\u0075\u24E4\uFF55\u00F9\u00FA\u00FB\u0169\u1E79\u016B\u1E7B\u016D\u00FC\u01DC\u01D8\u01D6\u01DA\u1EE7\u016F\u0171\u01D4\u0215\u0217\u01B0\u1EEB\u1EE9\u1EEF\u1EED\u1EF1\u1EE5\u1E73\u0173\u1E77\u1E75\u0289]/g,
		},
		{
			base: "v",
			letters: /[\u0076\u24E5\uFF56\u1E7D\u1E7F\u028B\uA75F\u028C]/g,
		},
		{ base: "vy", letters: /[\uA761]/g },
		{
			base: "w",
			letters: /[\u0077\u24E6\uFF57\u1E81\u1E83\u0175\u1E87\u1E85\u1E98\u1E89\u2C73]/g,
		},
		{ base: "x", letters: /[\u0078\u24E7\uFF58\u1E8B\u1E8D]/g },
		{
			base: "y",
			letters: /[\u0079\u24E8\uFF59\u1EF3\u00FD\u0177\u1EF9\u0233\u1E8F\u00FF\u1EF7\u1E99\u1EF5\u01B4\u024F\u1EFF]/g,
		},
		{
			base: "z",
			letters: /[\u007A\u24E9\uFF5A\u017A\u1E91\u017C\u017E\u1E93\u1E95\u01B6\u0225\u0240\u2C6C\uA763]/g,
		},
	];

	for (var i = 0; i < defaultDiacriticsRemovalMap.length; i++) {
		str = str.replace(
			defaultDiacriticsRemovalMap[i].letters,
			defaultDiacriticsRemovalMap[i].base
		);
	}

	return str;
}

class MessageBox {
	static info(msg, tl, cfgOvr) {
		this.notify("info", msg, tl, {
			closeButton: false,
			progressBar: true,
			...cfgOvr,
		});
	}

	static success(msg, tl, cfgOvr) {
		this.notify("success", msg, tl, {
			closeButton: false,
			progressBar: true,
			...cfgOvr,
		});
	}

	static warning(msg, tl, cfgOvr) {
		this.notify("warning", msg, tl, {
			closeButton: false,
			progressBar: true,
			...cfgOvr,
		});
	}

	static error(msg, tl, cfgOvr) {
		this.notify("error", msg, tl, {
			closeButton: false,
			progressBar: true,
			...cfgOvr,
		});
	}

	static notify(lvl, msg, tl, cfgOvr) {
		toastr[lvl](msg, tl, cfgOvr);
	}
}

class LevModal {
	modal;
	dialog;
	props = {
		id: '',
		title: 'T&iacute;tulo do Modal',
		size: 'modal-lg',
		keyboard: false,
		backdrop: 'static',
		body: '<p>Conte&uacute;do do modal</p>',
		buttons: [{
			type: 'button',
			class: 'btn btn-secondary',
			'data-bs-dismiss': 'modal',
			'aria-label': 'Fechar',
			label: 'Fechar'
		}]
	};

	defaultHtml = `
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title small text-uppercase"></h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body"></div>
				<div class="modal-footer"></div>
			</div>
		</div>
	`;

	constructor(props) {
		props = props || {};
		this.props.id = this.randId();
		if (Object.keys(props).length > 0) {
			this.props = { ...this.props, ...props };
		}
	}

	set buttons(btns) {
		this.props.buttons = btns;
	}

	get buttons() {
		return this.props.buttons;
	}

	set body(html) {
		this.props.body = html;
	}

	get body() {
		return this.props.body;
	}

	loadAndShow(url) {
		blockUi();
		$.get({
			url,
			dataType: `json`,
			success: (resp) => {
				unblockUi();
				if (!resp.success) {
					MessageBox.error(resp.message);
					return false;
				}

				this.props.size = resp.modalSize;
				this.props.title = resp.title;
				this.body = resp.html;
				this.show();
			},
			error: errorFunction,
		});
	}

	appendButton(btn) {
		this.props.buttons.push(btn);
	}

	getMaxZIndexModal() {
		return Math.max(
			...Array.from(document.querySelectorAll('.modal'), el =>
				parseFloat(window.getComputedStyle(el).zIndex),
			).filter(zIndex => !Number.isNaN(zIndex)),
			0,
		);
	}

	randId(length) {
		length = length || 10;
		let str = "";
		var chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
		for (var i = 0; i < length; i++) {
			str += chars.charAt(Math.floor(Math.random() * chars.length));
		}
		return str;
	}

	hide() {
		this.modal.hide();
	}

	show() {
		this.putOnBody();
		if (!this.modal) {
			this.modal = new bootstrap.Modal(`#${this.props.id}`, this.props);
			const maxZI = this.getMaxZIndexModal();

			this.addEventListener('hidden.bs.modal', event => {
				this.dialog.find(".ckeditor").each(function (key, textarea) {
					CKEDITOR.instances[textarea.id].destroy();
				});
				$(`#${this.props.id}`).remove();
			});

			this.addEventListener('show.bs.modal', event => {
				$(this.modal._element).css('z-index', maxZI + 1);
			});

			this.addEventListener('shown.bs.modal', event => {
				this.dialog.find(".ckeditor").each(function (key, textarea) {
					CKEDITOR.replace(textarea.id);
				});
			});
			fieldFunctions();
		}

		this.modal.show();
	}

	mountModal() {
		this.dialog = $(`<div class="modal fade" data-bs-focus="false" tabindex="-1"></div>`).html(this.defaultHtml);
		this.dialog.attr('id', this.props.id);
		this.dialog.find('.modal-title').html(this.props.title);
		this.dialog.find('.modal-dialog').addClass(this.props.size);
		this.dialog.find('.modal-body').html(this.body);
		const footer = this.dialog.find('.modal-footer');
		footer.html('');
		this.buttons.forEach(btn => {
			let $btn = $(`<button></button>`);
			for (const k in btn) {
				if (k.toLowerCase().substring(0, 2) == 'on') {
					$btn.on(k.toLowerCase().substring(2), btn[k]);
				} else if (k == 'label') {
					$btn.html(btn[k]);
				} else {
					$btn.attr(k, btn[k]);
				}
			}
			footer.append($btn);
		});
	}

	putOnBody() {
		if ($(`#${this.props.id}`).length > 0) $(`#${this.props.id}`).remove();
		this.mountModal();
		$(`body`).append(this.dialog);
	}

	addEventListener(name, event) {
		this.dialog.on(name, event);
	}
}

Date.isLeapYear = function (year) { 
    return (((year % 4 === 0) && (year % 100 !== 0)) || (year % 400 === 0)); 
};

Date.getDaysInMonth = function (year, month) {
    return [31, (Date.isLeapYear(year) ? 29 : 28), 31, 30, 31, 30, 31, 31, 30, 31, 30, 31][month];
};

Date.prototype.isLeapYear = function () { 
    return Date.isLeapYear(this.getFullYear()); 
};

Date.prototype.getDaysInMonth = function () { 
    return Date.getDaysInMonth(this.getFullYear(), this.getMonth());
};

Date.prototype.addMonths = function (value) {
    var n = this.getDate();
    this.setDate(1);
    this.setMonth(this.getMonth() + value);
    this.setDate(Math.min(n, this.getDaysInMonth()));
    return this;
};

$(document).ready(() => {
	$('input[data-type="togglePassword"]').each((index, el) => {
		showTogglePassword(el);
	});
});