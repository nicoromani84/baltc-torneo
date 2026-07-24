var admin = {
	login: {

		// Inicializamos el módulo
		run: function() {
			var that = this;

			$(document).on('focus', '#login-dni', function(){
				$('html, body').animate({ scrollTop: $(this).offset().top }, 0);
			});

			$('#login').validate({
	            errorElement: "div",
	            errorClass: 'is-invalid',
	            validClass: 'is-valid',
	            onclick: false,
	            onfocusout: false,
	            rules: {
					username: {
						required: true
					},
					password: {
						required: true
					}
				},
				messages: {
					username: {
						required: 'Debe completar el usuario'
					},
					password: {
						required: 'Debe completar la contraseña'
					}
				},
	            errorPlacement: function (error, element) {
	                error.addClass("invalid-feedback");
	                error.insertAfter(element);
	            },
	            submitHandler: function(form) {
	            	that.do( function(res) {
					if (res.action) {
						showNotification('success', 'Sesión iniciada correctamente.')
						setTimeout(function(){window.location.reload();}, 1500);
					} else {
						showNotification('error', res.msg)
					}
				} );
	            }
	        });

		},

		//Process Login
		do: function(callback) {
			var that = this;
			$.ajax({
				url: loginurl + '/proccess',
				type: "POST",
				data: $('#login').serialize(),
				headers: { 'X-Auth-Token' : token },
				success: function(res) {
					if (typeof callback == 'function') {
						callback(res);
					}
				}
			});
		}
	},

	dashboard: {
		table: $('#jugadoresTable'),
		reservations: [],
		partners: {
			names: [],
			data: []
		},
		selectedPartners: [],
		tournament_type: 'singles',

		// Inicializamos el módulo
		run: function() {
			var that = this;
			that.getReservations();

			// Overlay click
			$(document).on('click', '#overlay', function(){
				that.hideSideModal();
			})

			$('#jugadoresTable').DataTable({
				dom: 'ltp',
				order: [[ 2, "DESC" ]]
			});

			// Partners validation
			jQuery.validator.addMethod("validName", function(value, element) {
				return that.vars.names.indexOf(value) >= 0;
			});

			$('#filtro-categoria').change(function(){
				that.getReservations();
			});

			$('#filtro-gender').change(function(){
				that.getCategories(function(res) {
					if(res.action) {
						$('select[name="category"] option').remove();
						$.each(res.categories, function(i, v){
							$('#filtro-categoria').append('<option value="' + v.id + '">' + v.name + '</option>');
						});
						that.getReservations();
					}
				})
			});

			$(document).on('click', '.tournament-tab', function(e){
				e.preventDefault();
				var tournament = $(this).data('tournament');
				console.log('Tab clicked:', tournament);
				that.tournament_type = tournament;
				$('.tournament-tab').removeClass('active');
				$(this).addClass('active');
				that.getReservations();
			});

			$('#add').click(function(){
				that.showAssignModal();
			});
		},

		hideSideModal: function() {
			var that = this;
			$('body').removeClass('sidemodal-opened');
			setTimeout(function(){
				if($('#sidemodal form').length)
					$('#sidemodal form')[0].reset();
				that.selectedPartners = [];
			}, 300);
		},

		prepareSubmit: function(action, id) {
			var that = this;
			// Submit reserva
			$('#sidemodal form').validate({
				ignore: [],
		        errorElement: "div",
		        errorClass: 'is-invalid',
		        validClass: '',
		        onkeyup: false,
		        onclick: false,
		        onfocusout: false,
		        rules: {
		        	category: {
		        		required: true
		        	}
				},
				messages: {
		        	category: {
		        		required: 'Debe seleccionar una categoría.'
		        	}
				},
		        errorPlacement: function (error, element) {
		            error.addClass("invalid-feedback");
		            error.insertAfter(element);
		        },
		        submitHandler: function(form) {
		        	var formdata = that.serializedToObj($(form).serializeArray());
		        		formdata.partners = that.selectedPartners.map(function(e) { return parseInt(e.id); });

		        	if(that.selectedPartners.length <= 1) {
		        		showNotification('error', 'Debe seleccionar al menos 2 participantes.');
		        		$(form).find('input.typeahead.tt-input').focus();
		        		return false;
		        	}
		        	
		        	$(form).find('button[type="submit"]').prop('disabled', true).addClass('loading');

		        	if(action == 'add') {
		        		action = adminurl + '/addReservation'
		        	} else if (action == 'edit') {
		        		formdata.id = id;
		        		action = adminurl + '/editReservation';
		        	}

		        	setTimeout(function(){
			        	$.ajax({
							url: action,
							type: "POST",
							data: formdata,
							headers: { 'X-Auth-Token' : token },
							success: function(res) {
								if(res.action){
									that.getReservations(function(){
										that.hideSideModal();
										showNotification('success', 'Incripción realizada correctamente.');
									});
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
		},

		getPartnerId(name) {
			var that = this;
			return that.vars.names.indexOf(name) + 1;
		},

		serializedToObj: function(array) {
			var data = {};
			$(array).each(function(index, obj){
			    data[obj.name] = obj.value;
			});
			return data;
		},

		showAssignModal: function() {
			var that = this;
			that.showSideModal(function(){
				$.ajax({
					url: baseurl + 'static/mst_templates/form_reservation_new.mst',
					data: {},
					success: function(template){
						that.selectedPartners = [];
						var showListPartners = function() {
							var items = '<ul>';
							$.each(that.selectedPartners, function(i, p) {
								items += '<li data-id="' + p.id + '"><a href="#" class="remove"><i class="fas fa-times-circle"></i></a><span>' + p.name + '</span></li>';
							})
							items += '</ul>'
							$('#sidemodal .partners').html(items);
						}

						var removeItemPartners = function(id) {
							var index = that.selectedPartners.map(function(e) { return parseInt(e.id); }).indexOf(id);
							if(index >= 0) {
								that.selectedPartners.splice(index, 1);
								$('#sidemodal .content .typeahead').prop('disabled', that.selectedPartners.length >= 4);
								showListPartners();
							}
						}

						$(document).on('click', '#sidemodal .partners ul li a.remove', function(e){
							e.preventDefault();
							removeItemPartners($(this).parent().data('id'))
						})
						var data = {
							categories: that.getCategories()
						}
					    var rendered = Mustache.render(template, data);
					    $('#sidemodal .content').html(rendered);

						// Partners autocomplete
						$('#sidemodal .content .typeahead').typeahead(
						{
							hint: true,
							highlight: true,
							minLength: 1,
							menu: isMobile().any() ? $('.mobile-suggestions') : '',
							classNames: { menu: 'tt-menu simplebar' }
						}, {
							name: 'states',
							source: that.substringMatcher(that.partners.names),
							limit: 10000
						}).bind('typeahead:selected', function(obj, selected, name) {
							
						    // Si existe el nombre en el listado de socios
						    var p_index = that.partners.names.indexOf($(obj.target).val());
						    if(p_index >= 0) {
						    	that.selectedPartners.push(that.partners.data[p_index]);
						    }

						    $('#sidemodal .content .typeahead').prop('disabled', that.selectedPartners.length >= 4);

						    // Mostramos el listado de partners actualizado
						    showListPartners();

						    // Limpiamos el input para poder seguir agregando.
						    setTimeout(function(){
						    	$('#sidemodal .content .typeahead').typeahead('val','');
						    })

						    return false; /* didn't do anything, maybe not necessary */
						}).off('blur');

						// Preparo la validación del formulario
						that.prepareSubmit('add');

						// Muestro el contenido
						$('#sidemodal .content').addClass('visible');
					},
					cache: false
				});
			})
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

		// Mostrar side modal
		showSideModal: function(cb) {
			var that = this;
			$('td.empty button').tooltip('hide');
			$('#sidemodal').css('height', $('#sidemodal').parent().innerHeight() + 'px');
			$('body').addClass('sidemodal-opened');
			$(document).keyup(function(e) {
				console.log(e.key)
			    if (e.key === "Escape") {
			    	that.hideSideModal();
			    	$(document).unbind("keyup")
			    }
			});

			if(typeof cb == 'function')
				cb();
		},

		// Obtenemos las reservas para la tabla
		getReservations: function(cb) {
			var that = this;
			var ajaxData = {category: $('#filtro-categoria').val(), gender: $('#filtro-gender').val(), tournament_type: that.tournament_type};
			console.log('Enviando datos:', ajaxData);
			$.ajax({
				url: adminurl + '/getReservations',
				type: "POST",
				data: ajaxData,
				headers: { 'X-Auth-Token' : token },
				success: function(res) {
					// Destruyo la tabla
					if ($.fn.DataTable.isDataTable('#jugadoresTable')) {
						$('#jugadoresTable').DataTable().destroy();
					}

					// Armo el tbody de la tabla
					var tbody = '';

					// Relleno los campos con los jugadores
					if(res.jugadores) {
						$.each(res.jugadores, function(i, jugador) {
							tbody += '<tr class="jugador-row" data-nombre="' + jugador.name.toLowerCase() + '" data-dni="' + jugador.dni + '" data-category="' + jugador.category_id + '" data-gender="' + jugador.gender + '">';
							tbody += '<td style="text-transform:capitalize">' + jugador.name.toLowerCase() + '</td>';
							tbody += '<td>' + jugador.dni + '</td>';
							tbody += '<td>' + jugador.email + '</td>';
							tbody += '<td>' + (jugador.gender == 'M' ? '<span class="badge badge-primary">M</span>' : '<span class="badge badge-danger">F</span>') + '</td>';
							tbody += '<td><span class="badge badge-info">' + jugador.categoria + '</span></td>';
							tbody += '<td nowrap>';
							tbody += '<button class="btn btn-xs btn-warning btn-editar-jugador" data-id="' + jugador.id + '" data-reserva="' + jugador.reserva_id + '" data-name="' + jugador.name + '" data-dni="' + jugador.dni + '" data-email="' + jugador.email + '" data-gender="' + jugador.gender + '" data-category="' + jugador.category_id + '"><i class="fas fa-edit"></i></button>';
							tbody += '<button class="btn btn-xs btn-danger btn-borrar-jugador" data-id="' + jugador.id + '" data-reserva="' + jugador.reserva_id + '" data-name="' + jugador.name.toLowerCase() + '"><i class="fas fa-trash"></i></button>';
							tbody += '</td>';
							tbody += '</tr>';
						});
					}

					// Muestro la estructura de la tabla
					that.table.find('tbody').html(tbody);

					that.table.find('button.delete-reservation').on('click', function(e){
						e.preventDefault();
						var id = $(this).closest('tr').data('id');
						if(confirm('Confirma eliminar esta reserva?')) {
							$.ajax({
								url: adminurl + '/delete',
								type: "POST",
								data: {id: id},
								headers: { 'X-Auth-Token' : token },
								success: function(res) {
									if(res.action){
										that.getReservations(function(){
											showNotification('success', 'Inscripción eliminada correctamente.');
										});
									} else {
										showNotification('error', 'Lo siento, ocurrió un error.');
									}
								}
							});
						}
					});

					$('#jugadoresTable').DataTable({
						dom: 'ltp',
						order: [[ 2, "DESC" ]]
					});

					if(typeof cb == 'function')
						cb(res);
				}
			});
		},

		// Obtenemos las categorías
		getCategories: function(cb) {
			var that = this;
			var categories = [];
			$.ajax({
				url: adminurl + '/getCategories',
				type: "POST",
				data: {gender: $('#filtro-gender').val()},
				async: false,
				headers: { 'X-Auth-Token' : token },
				success: function(res) {
					if(typeof cb == 'function') {
						cb(res);
						categories = false;
					} else {
						categories = res.categories;
					}

				}
			});
			if(categories)
				return categories;
		}
	}
};