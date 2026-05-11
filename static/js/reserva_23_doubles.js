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

			that.getPartners(function(res){
				if(res.action) {
					that.vars.data = res.data.map(function(a){
						return {id: a.id, name: a.name.toLowerCase()};
					})
					
					that.vars.names = res.data.map(function(a){
						return a.name.toLowerCase();
					})

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
				}
			});

			jQuery.validator.addMethod("validName", function(value, element) {
				return that.vars.names.indexOf(value) >= 0;
			});

			$(document).on('input', 'input.typeahead', function(){
				$('html, body').animate({ scrollTop: $(this).offset().top }, 0);
			})

			$(document).on('focus', 'input.typeahead', function(){
				$('html, body').animate({ scrollTop: $(this).offset().top }, 0);
			})

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
						required: 'Debes ingresar tu compañero',
						validName: 'El compañero ingresado no existe'
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
		        		showNotification('error', 'Debe seleccionar una categoríaa.');
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

		showSuccess: function(data) {
			console.log(data)
			var mm = multimodal,
				mmbody = multimodal.outBody();
			mm.setHeader('<div class="icon-box"><i class="fas fa-check-circle"></i></div>');
			mm.setHeaderStyle('reserve-success');
			mm.setBody('<h4>Ya estas inscripto!</h4><ul><li><i class="icon far fa-calendar"></i><span>10/04/2024</span></li><li><i class="icon fas fa-star"></i><span>' + data.category + '</span></li><li><i class="icon fas fa-user-friends"></i><span style="text-transform:capitalize">' + data.partner + '</span></li></ul>');
			mm.setCallbackFooter('');
			mm.toggle();
		}
	}
};