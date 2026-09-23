var reserva = {

	run : function() {
		this.reserva.triggers();

		// Actualizo las canchas al cargar
		$('#fecha').change()
	},
	reserva: {
		vars: {
			names: [],
			data: []
		},
		triggers: function(){
			var that = this;

			if(!isMobile().any())
				$('.niceselect').niceSelect();

			// Deshabilitar input de pareja al cargar
			$('#partner-input').prop('disabled', true);
			$('#mixed-partner-info').hide();

			that.getPartners(function(res){
				if(res.action) {
					that.vars.data = res.data.map(function(a){
						return {id: a.id, name: a.name.toLowerCase()};
					})

					that.vars.names = res.data.map(function(a){
						return a.name.toLowerCase();
					})

					// Partners autocomplete
					that.initTypeahead();
				}
			});

			// Manejador para cambio de categoría
			$(document).on('change', '#category', function(){
				var selectedId = $(this).val();
				var selectedOption = $(this).find('option[value="' + selectedId + '"]');
				var categoryGender = selectedOption.attr('data-gender');
				var isMixta = categoryGender === 'X';

				console.log('Category changed. ID: ' + selectedId + ', Gender: ' + categoryGender + ', isMixta: ' + isMixta);

				if(isMixta) {
					console.log('Loading mixed partners for category ' + selectedId);
					reserva.reserva.loadMixedPartners(selectedId);
				} else {
					console.log('Loading all partners (non-mixed)');
					reserva.reserva.reloadAllPartners();
				}
			});

			jQuery.validator.addMethod("validName", function(value, element) {
				return that.vars.names.indexOf(value) >= 0;
			});

			$('#reservar form').validate({
				ignore: [],
		        errorElement: "div",
		        errorClass: 'is-invalid',
		        validClass: 'is-valid',
		        onkeyup: false,
		        onclick: false,
		        onfocusout: false,
		        rules: {
					partner: {
						required: true,
						validName : true
					}
				},
				messages: {
					partner: {
						required: 'Debes ingresar tu pareja',
						validName: 'La pareja ingresada no existe o no está disponible'
					}
				},
		        errorPlacement: function (error, element) {
		            error.addClass("invalid-feedback");
		            error.insertAfter(element);
		        },
		        submitHandler: function(form) {
		        	var formdata = that.serializedToObj($('.form form').serializeArray());
		        	formdata.partner = that.getPartnerId(formdata.partner);

		        	if(typeof formdata.partner == 'undefined' || formdata.partner == '') {
		        		showNotification('error', 'Debes ingresar tu compañero.');
		        		return false;
		        	}

		        	if(typeof formdata.category == 'undefined' || formdata.category == '') {
		        		showNotification('error', 'Debe seleccionar una categoría.');
		        		return false;
		        	}

		        	if(!$('#acepto_reglamento').is(':checked')) {
		        		showNotification('error', 'Debés aceptar el reglamento del torneo.');
		        		return false;
		        	}

		        	$(form).find('button[type="submit"]').prop('disabled', true).addClass('loading');

		        	setTimeout(function(){
			        	$.ajax({
							url: reservaurl + '/add',
							type: "POST",
							data: formdata,
							headers: { 'X-Auth-Token' : token },
							success: function(res) {
								if(res.action){
									var arr = that.serializedToObj($(form).serializeArray());
										arr.court = $(form).find('#cancha option[value="' + arr.court + '"]').text();
										arr.category = $(form).find('#category option[value="' + arr.category + '"]').text();
									that.showSuccess(arr);
								} else {
									if(typeof res.msg !== 'undefined') {
										if(Array.isArray(res.msg)) {
											showNotification('error', res.msg.join('<br>'));
										} else {
											showNotification('error', res.msg);
										}
									} else {
										showNotification('error', 'Lo siento, ocurrió un error.');
									}
								}
							},
							complete: function() {
								$(form).find('button[type="submit"]').prop('disabled', false).removeClass('loading');
							}
						});
		        	}, 1000);
		        }
		    });

		    $('.typeahead').bind('typeahead:selected', function(ev, suggestion) {
				// Si el nombre existe.
				if(that.vars.names.indexOf(suggestion) >= 0) {
					//$('input[name="partner"]').val(that.vars.names.indexOf(suggestion))
				}
			});

			$('.typeahead').bind('typeahead:render', function(ev, suggestion) {
				new SimpleBar($('.simplebar')[0])
			});
		},

		getPartnerId(name) {
			var that = this;
			//return that.vars.names.indexOf(name) + 1;
			return reserva.reserva.vars.data[reserva.reserva.vars.names.map(function(e) {return e; }).indexOf(name)].id;
		},

		serializedToObj: function(array) {
			var data = {};
			$(array).each(function(index, obj){
			    data[obj.name] = obj.value;
			});
			return data;
		},

		substringMatcher: function(strs) {
			return function findMatches(q, cb) {
				var matches, substringRegex;
				matches = [];
				substrRegex = new RegExp(q, 'i');
				$.each(strs, function(i, str) {
					if (substrRegex.test(str)) {
						matches.push(str);
					}
				});

				cb(matches);
			};
		},

		getPartners: function(callback) {
			var that = this;
			$.ajax({
				url: baseurl + '/partners/getAllExceptMe',
				type: "GET",
				data: {},
				headers: { 'X-Auth-Token' : token },
				success: function(res) {
					if (typeof callback == 'function') {
						callback(res);
					}
				}
			});
		},

		initTypeahead: function() {
			var that = this;
			// Destroy existing typeahead
			$('.typeahead').typeahead('destroy');

			// Partners autocomplete
			$('.typeahead').typeahead(
			{
				hint: true,
				highlight: true,
				minLength: 1,
				menu: isMobile().any() ? $('.mobile-suggestions') : '',
				classNames: { menu: 'tt-menu simplebar' }
			}, {
				name: 'states',
				source: that.substringMatcher(that.vars.names),
				limit: 10000
			});
		},

		loadMixedPartners: function(categoryId) {
			var that = this;
			console.log('loadMixedPartners called with categoryId: ' + categoryId + ', userGender: ' + userGender);
			$('#mixed-partner-info').hide();
			$('#partner-input').prop('disabled', false);

			$.ajax({
				url: baseurl + '/admin/searchMixedPartners',
				type: "POST",
				data: {
					category: categoryId,
					gender: userGender
				},
				headers: { 'X-Auth-Token' : token },
				success: function(res) {
					console.log('searchMixedPartners response:', res);
					if(res.action && res.data) {
						that.vars.data = res.data.map(function(a){
							return {id: a.id, name: a.name.toLowerCase()};
						})

						that.vars.names = res.data.map(function(a){
							return a.name.toLowerCase();
						})

						$('#mixed-partner-info').hide();
						that.initTypeahead();
					} else {
						$('#mixed-partner-info').hide();
						console.log('Error response:', res);
					}
				},
				error: function(err) {
					$('#mixed-partner-info').hide();
					console.log('AJAX error:', err);
				}
			});
		},

		reloadAllPartners: function() {
			var that = this;
			$('#mixed-partner-info').hide();
			$('#partner-input').prop('disabled', true).val('');
			that.getPartners(function(res){
				if(res.action) {
					that.vars.data = res.data.map(function(a){
						return {id: a.id, name: a.name.toLowerCase()};
					})

					that.vars.names = res.data.map(function(a){
						return a.name.toLowerCase();
					})

					that.initTypeahead();
				}
			});
		},

		showSuccess: function(data) {
			var splashHTML = [
				'<div id="inscripcion-splash">',
					'<div class="splash-inner">',
						'<div class="splash-check"><i class="fas fa-check-circle"></i></div>',
						'<h2 class="splash-titulo">¡Estás inscripto!</h2>',
						'<div class="splash-categoria"><i class="fas fa-star"></i> Categoría ' + data.category + '</div>',
						'<div class="splash-partner"><i class="fas fa-user-friends"></i> Pareja: <strong>' + data.partner + '</strong></div>',
						'<div class="splash-fecha"><i class="fas fa-calendar-alt"></i> Inicio: <strong>6 de Octubre 2026</strong></div>',
						'<div class="splash-aviso"><i class="fas fa-info-circle"></i> Tu categoría puede quedar sujeta a revisión por parte de la organización.</div>',
						'<button class="btn btn-secondary splash-btn" id="splash-cerrar">Cerrar</button>',
					'</div>',
				'</div>'
			].join('');

			$('body').append(splashHTML);
			$('#inscripcion-splash').hide().fadeIn(400);

			$(document).on('click', '#splash-cerrar', function(){
				$('#inscripcion-splash').fadeOut(300, function(){ $(this).remove(); });
				// Limpiar el formulario
				$('#category').val('').trigger('change');
				$('input[name="partner"]').val('');
				if($('.niceselect').length) $('.niceselect').niceSelect('update');
				$('#acepto_reglamento').prop('checked', false);
				$('button[type="submit"]').prop('disabled', false).removeClass('loading');
			});

			if(!$('#splash-styles').length) {
				var css = [
					'#inscripcion-splash {',
						'position:fixed;inset:0;z-index:9999;',
						'background:rgba(0,0,0,0.85);',
						'display:flex;align-items:center;justify-content:center;padding:20px;',
					'}',
					'.splash-inner {',
						'background:linear-gradient(135deg,#1a1a2e 0%,#16213e 100%);',
						'border:1px solid rgba(165,208,81,0.4);border-radius:20px;',
						'padding:40px 30px;text-align:center;max-width:360px;width:100%;',
						'box-shadow:0 20px 60px rgba(0,0,0,0.6);',
					'}',
					'.splash-check { font-size:70px;color:#a5d051;margin-bottom:16px; }',
					'.splash-titulo { color:#fff;font-size:26px;font-weight:800;text-transform:uppercase;letter-spacing:1px;margin-bottom:20px; }',
					'.splash-categoria { background:rgba(165,208,81,0.15);border:1px solid rgba(165,208,81,0.4);color:#a5d051;font-weight:700;font-size:16px;padding:10px 20px;border-radius:8px;margin-bottom:12px; }',
					'.splash-partner { background:rgba(165,208,81,0.15);border:1px solid rgba(165,208,81,0.4);color:#a5d051;font-weight:700;font-size:16px;padding:10px 20px;border-radius:8px;margin-bottom:12px; }',
					'.splash-fecha { color:rgba(255,255,255,0.8);font-size:15px;margin-bottom:16px; }',
					'.splash-fecha strong { color:#fff; }',
					'.splash-aviso { background:rgba(255,200,0,0.1);border:1px solid rgba(255,200,0,0.3);color:rgba(255,220,80,0.9);font-size:13px;padding:10px 14px;border-radius:8px;margin-bottom:24px;line-height:1.4; }',
					'.splash-btn { width:100%;padding:14px;font-size:16px;font-weight:700;border-radius:10px; }'
				].join('');
				$('<style id="splash-styles">').text(css).appendTo('head');
			}
		}
	}
};
