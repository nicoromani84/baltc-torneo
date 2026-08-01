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
						setTimeout(function(){window.location.href = loginurl;}, 800);
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
		tournament_type: 'doubles',
		jugadoresCache: {},

		// Inicializamos el módulo
		run: function() {
			var that = this;

			// Overlay click
			$(document).on('click', '#overlay', function(){
				that.hideSideModal();
			})

			// Dashboard only shows doubles
			that.tournament_type = 'doubles';

			// Cargar datos del torneo seleccionado
			that.getReservations();

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

			$('#add').click(function(){
				that.showAssignModal();
			});

			// Event listeners para editar y borrar jugadores
			$(document).on('click', '.btn-editar-jugador', function(){
				var jugadorId = $(this).data('id');
				var jugador = that.jugadoresCache[jugadorId];
				if(!jugador) return;
				$('#jugador-id').val(jugador.id);
				$('#jugador-reserva-id').val(jugador.reserva_id);
				$('#form-jugador-titulo').text('Editar Jugador');
				$('#form-editar-fields').show();
				$('#form-nuevo-fields').hide();
				$('#jugador-name').val(jugador.name);
				$('#jugador-dni').val(jugador.dni);
				$('#jugador-email').val(jugador.email);
				$('#jugador-gender').val(jugador.gender);
				$('#jugador-category').val(jugador.category_id);
				$('#form-jugador').slideDown();
				$('html,body').animate({scrollTop:0}, 300);
			});

			$(document).on('click', '.btn-editar-categoria', function(){
				var reservationId = $(this).data('id');
				var categoryId = $(this).data('category');
				var pairsName = $(this).data('name');

				var categories = that.getCategories();
				var html = '<select id="new-category" class="form-control" style="margin-bottom:10px;">';
				categories.forEach(function(cat) {
					html += '<option value="' + cat.id + '" ' + (cat.id == categoryId ? 'selected' : '') + '>' + cat.name + '</option>';
				});
				html += '</select>';

				var modal = $('<div class="modal fade" id="modalEditCategoria" tabindex="-1" role="dialog">' +
					'<div class="modal-dialog" role="document">' +
					'<div class="modal-content">' +
					'<div class="modal-header"><h5 class="modal-title">Cambiar Categoría</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>' +
					'<div class="modal-body">' + html + '</div>' +
					'<div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button><button type="button" class="btn btn-primary" id="btn-save-categoria">Guardar</button></div>' +
					'</div></div></div>');

				$('body').append(modal);
				modal.modal('show');

				$('#btn-save-categoria').on('click', function(){
					var newCategoryId = $('#new-category').val();
					$.ajax({
						url: adminurl + '/editParejaCategory',
						type: 'POST',
						data: { reservation_id: reservationId, category_id: newCategoryId },
						headers: {'X-Auth-Token': token},
						success: function(res) {
							if(res.action) {
								modal.modal('hide');
								modal.remove();
								that.getReservations(function(){ showNotification('success', 'Categoría actualizada.'); });
							} else {
								alert('Error: ' + (res.msg || 'Error desconocido'));
							}
						}
					});
				});

				modal.on('hidden.bs.modal', function() {
					modal.remove();
				});
			});

			$(document).on('click', '.btn-borrar-jugador', function(){
				var jugadorId = $(this).data('id');
				var jugador = that.jugadoresCache[jugadorId];
				if(!jugador) return;
				if(!confirm('¿Eliminar a ' + jugador.name + ' del torneo?')) return;
				$.ajax({
					url: adminurl + '/deleteJugador',
					type: 'POST',
					data: { id: jugador.id, reserva_id: jugador.reserva_id },
					headers: {'X-Auth-Token': token},
					success: function(res) {
						if(res.action) that.getReservations(function(){ showNotification('success', 'Jugador eliminado correctamente.'); });
					}
				});
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
					// Actualizar headers según tipo de torneo
					var thead = $('#jugadoresTable thead tr');
					if(that.tournament_type === 'doubles' && res.parejas) {
						thead.html('<th>Pareja</th><th>Categoría</th><th>Género</th><th width="80"></th>');
					} else {
						thead.html('<th>Nombre</th><th>DNI</th><th>Email</th><th>Género</th><th>Categoría</th><th width="80"></th>');
					}

					// Para dobles y singles: mostrar tabla
					var container = $('#reservas-container');
					if(container.length > 0) {
						container.remove();
					}

					var table = $('#jugadoresTable');
					if(table.length > 0) {
						table.show();
						if ($.fn.DataTable.isDataTable('#jugadoresTable')) {
							$('#jugadoresTable').DataTable().destroy();
						}
					}

					var tbody = '';
					that.jugadoresCache = {};

					if(that.tournament_type === 'doubles' && res.parejas) {
						// Modo dobles: mostrar parejas como tabla
						$.each(res.parejas, function(i, pareja) {
							that.jugadoresCache[pareja.reservation_id] = pareja;
							tbody += '<tr class="pareja-row" data-nombre="' + pareja.pareja.toLowerCase() + '" data-category="' + pareja.category_id + '" data-gender="' + pareja.gender + '">';
							tbody += '<td style="text-transform:capitalize;">' + pareja.pareja.toLowerCase() + '</td>';
							tbody += '<td><span class="badge badge-info">' + pareja.categoria + '</span></td>';
							tbody += '<td>' + (pareja.gender == 'M' ? '<span class="badge badge-primary">M</span>' : '<span class="badge badge-danger">F</span>') + '</td>';
							tbody += '<td nowrap>';
							tbody += '<button class="btn btn-xs btn-warning btn-editar-categoria" data-id="' + pareja.reservation_id + '" data-category="' + pareja.category_id + '" data-name="' + pareja.pareja.toLowerCase() + '"><i class="fas fa-edit"></i></button>';
							tbody += '<button class="btn btn-xs btn-danger btn-borrar-jugador" data-id="' + pareja.reservation_id + '" data-reserva="' + pareja.reservation_id + '" data-name="' + pareja.pareja.toLowerCase() + '"><i class="fas fa-trash"></i></button>';
							tbody += '</td>';
							tbody += '</tr>';
						});
						$('#contador-parejas').text(res.parejas.length + ' pareja' + (res.parejas.length !== 1 ? 's' : ''));
					} else if(res.jugadores) {
						// Modo singles: mostrar jugadores como tabla
						$.each(res.jugadores, function(i, jugador) {
							that.jugadoresCache[jugador.id] = jugador;
							tbody += '<tr class="jugador-row" data-nombre="' + jugador.name.toLowerCase() + '" data-dni="' + jugador.dni + '" data-category="' + jugador.category_id + '" data-gender="' + jugador.gender + '">';
							tbody += '<td style="text-transform:capitalize">' + jugador.name.toLowerCase() + '</td>';
							tbody += '<td>' + jugador.dni + '</td>';
							tbody += '<td>' + jugador.email + '</td>';
							tbody += '<td>' + (jugador.gender == 'M' ? '<span class="badge badge-primary">M</span>' : '<span class="badge badge-danger">F</span>') + '</td>';
							tbody += '<td><span class="badge badge-info">' + jugador.categoria + '</span></td>';
							tbody += '<td nowrap>';
							tbody += '<button class="btn btn-xs btn-warning btn-editar-jugador" data-id="' + jugador.id + '" data-reserva="' + jugador.reserva_id + '" data-name="' + $('<div>').text(jugador.name).html() + '" data-dni="' + jugador.dni + '" data-email="' + jugador.email + '" data-gender="' + jugador.gender + '" data-category="' + jugador.category_id + '"><i class="fas fa-edit"></i></button>';
							tbody += '<button class="btn btn-xs btn-danger btn-borrar-jugador" data-id="' + jugador.id + '" data-reserva="' + jugador.reserva_id + '" data-name="' + jugador.name.toLowerCase() + '"><i class="fas fa-trash"></i></button>';
							tbody += '</td>';
							tbody += '</tr>';
						});
					}

					that.table.find('tbody').html(tbody);

					$('#jugadoresTable').DataTable({
						dom: 'ltp',
						order: [[ 0, "ASC" ]],
						pageLength: 25
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
	},

	singles: {
		table: $('#jugadoresTable'),
		tournament_type: 'singles',

		run: function() {
			var that = this;

			$('#jugadoresTable').DataTable({
				dom: 'ltp',
				order: [[ 2, "DESC" ]]
			});

			that.getReservations();

			$('#filtro-categoria').change(function(){
				that.getReservations();
			});

			$('#filtro-gender').change(function(){
				that.getReservations();
			});

			$(document).on('click', '.btn-editar-jugador', function(){
				var jugadorId = $(this).data('id');
				if(!jugadorId) return;
				$('#jugador-id').val($(this).data('id'));
				$('#jugador-reserva-id').val($(this).data('reserva'));
				$('#form-jugador-titulo').text('Editar Jugador');
				$('#form-editar-fields').show();
				$('#form-nuevo-fields').hide();
				$('#jugador-name').val($(this).data('name'));
				$('#jugador-dni').val($(this).data('dni'));
				$('#jugador-email').val($(this).data('email'));
				$('#jugador-gender').val($(this).data('gender'));
				$('#jugador-category').val($(this).data('category'));
				$('#form-jugador').slideDown();
				$('html,body').animate({scrollTop:0}, 300);
			});

			$(document).on('click', '.btn-borrar-jugador', function(){
				var d = $(this).data();
				if(!confirm('¿Eliminar a ' + d.name + ' del torneo?')) return;
				$.ajax({
					url: adminurl + '/deleteJugador', type: 'POST',
					data: { id:d.id, reserva_id:d.reserva },
					headers: {'X-Auth-Token': token},
					success: function(res){ if(res.action) location.reload(); }
				});
			});
		},

		getReservations: function(cb) {
			var that = this;
			var ajaxData = {category: $('#filtro-categoria').val(), gender: $('#filtro-gender').val(), tournament_type: that.tournament_type};
			$.ajax({
				url: adminurl + '/getReservations',
				type: "POST",
				data: ajaxData,
				headers: { 'X-Auth-Token' : token },
				success: function(res) {
					var tbody = '';
					if(res.jugadores) {
						$.each(res.jugadores, function(i, jugador) {
							tbody += '<tr class="jugador-row" data-nombre="' + jugador.name.toLowerCase() + '" data-dni="' + jugador.dni + '" data-category="' + jugador.category_id + '" data-gender="' + jugador.gender + '">';
							tbody += '<td style="text-transform:capitalize">' + jugador.name.toLowerCase() + '</td>';
							tbody += '<td>' + jugador.dni + '</td>';
							tbody += '<td>' + jugador.email + '</td>';
							tbody += '<td>' + (jugador.gender == 'M' ? '<span class="badge badge-primary">M</span>' : '<span class="badge badge-danger">F</span>') + '</td>';
							tbody += '<td><span class="badge badge-info">' + jugador.categoria + '</span></td>';
							tbody += '<td nowrap>';
							tbody += '<button class="btn btn-xs btn-warning btn-editar-jugador" data-id="' + jugador.id + '" data-reserva="' + jugador.reserva_id + '" data-name="' + $('<div>').text(jugador.name).html() + '" data-dni="' + jugador.dni + '" data-email="' + jugador.email + '" data-gender="' + jugador.gender + '" data-category="' + jugador.category_id + '"><i class="fas fa-edit"></i></button>';
							tbody += '<button class="btn btn-xs btn-danger btn-borrar-jugador" data-id="' + jugador.id + '" data-reserva="' + jugador.reserva_id + '" data-name="' + jugador.name.toLowerCase() + '"><i class="fas fa-trash"></i></button>';
							tbody += '</td>';
							tbody += '</tr>';
						});
					}

					that.table.find('tbody').html(tbody);

					if(typeof cb == 'function')
						cb(res);
				}
			});
		}
	}
};