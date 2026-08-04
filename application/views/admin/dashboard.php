<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!-- header.php ya incluye la navbar admin responsive -->

<div class="page-content">
    <div class="content-inner">
        <div style="padding:20px 24px;background:#fff;border-bottom:1px solid #dee2e6;">
        	<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
                <div>
                    <h2 style="margin:0 0 12px 0;font-size:20px;">Parejas Inscriptas - Dobles</h2>
                    <div style="margin-top:8px;">
                        <a href="<?=base_url('admin/singles')?>" class="btn btn-sm btn-outline-secondary" style="font-size:12px;">
                            <i class="fas fa-arrow-left"></i> Ver Singles
                        </a>
                    </div>
                </div>
                <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
	            	<button id="btn-inscripciones" class="btn btn-sm <?=$inscripciones_abiertas ? 'btn-success' : 'btn-secondary'?>" data-estado="<?=$inscripciones_abiertas ? '1' : '0'?>">
						<i class="fas <?=$inscripciones_abiertas ? 'fa-lock-open' : 'fa-lock'?>"></i>
						Inscripciones: <strong><?=$inscripciones_abiertas ? 'Abiertas' : 'Cerradas'?></strong>
					</button>
                	<button class="btn btn-info btn-sm" id="btn-agregar-pareja"><i class="fas fa-plus"></i> Agregar Pareja</button>
                	<button class="btn btn-success btn-sm" id="btn-descargar-excel"><i class="fas fa-download"></i> Descargar Excel</button>
                </div>
            </div>
        </div>
        <section id="reservas">
            <div class="container-fluid">

                <!-- FORM NUEVO/EDITAR -->
                <div id="form-jugador" style="display:none; margin-bottom:20px;">
                    <div class="card">
                        <div class="card-body">
                            <h5 id="form-jugador-titulo" class="card-title">Nuevo Jugador</h5>
                            <input type="hidden" id="jugador-id">
                            <input type="hidden" id="jugador-reserva-id">

                            <!-- MODO EDITAR: campos completos -->
                            <div id="form-editar-fields">
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label>Nombre completo</label>
                                        <input type="text" id="jugador-name" class="form-control" placeholder="APELLIDO, NOMBRE">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>DNI</label>
                                        <input type="number" id="jugador-dni" class="form-control" placeholder="12345678">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Email</label>
                                        <input type="email" id="jugador-email" class="form-control">
                                    </div>
                                    <div class="form-group col-md-1">
                                        <label>Género</label>
                                        <select id="jugador-gender" class="form-control">
                                            <option value="">...</option>
                                            <option value="M">M</option>
                                            <option value="F">F</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>Categoría</label>
                                        <select id="jugador-category" class="form-control">
                                            <option value="">...</option>
                                            <?php if(!empty($categories)) foreach($categories as $cat): ?>
                                            <option value="<?=$cat->id?>"><?=htmlspecialchars($cat->name)?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <button class="btn btn-primary btn-sm" id="btn-guardar-jugador">Guardar</button>
                                <button class="btn btn-secondary btn-sm" id="btn-cancelar-jugador">Cancelar</button>
                            </div>

                            <!-- MODO NUEVO: buscar en partners -->
                            <div id="form-nuevo-fields">
                                <div class="form-row align-items-end">
                                    <div class="form-group col-md-6 mb-0" style="position:relative;">
                                        <label>Buscar jugador por nombre o DNI</label>
                                        <input type="text" id="buscar-partner" class="form-control" placeholder="Escribí nombre o DNI..." autocomplete="off">
                                        <div id="autocomplete-partner" style="display:none; position:absolute; top:100%; left:0; right:0; background:#fff; border:1px solid #ced4da; border-top:none; border-radius:0 0 4px 4px; z-index:1000; max-height:260px; overflow-y:auto; box-shadow:0 4px 8px rgba(0,0,0,0.1);"></div>
                                    </div>
                                </div>
                                <div id="resultados-partner" style="display:none;"></div>
                                <!-- Jugador seleccionado -->
                                <div id="partner-seleccionado" style="display:none; margin-top:12px;">
                                    <div class="alert alert-success py-2 px-3 d-flex align-items-center justify-content-between">
                                        <span><i class="fas fa-user-check"></i> <strong id="partner-sel-nombre"></strong> <span class="text-muted small" id="partner-sel-dni"></span></span>
                                        <button class="btn btn-xs btn-outline-secondary" id="btn-cambiar-partner">Cambiar</button>
                                    </div>
                                    <input type="hidden" id="nuevo-partner-id">
                                    <div class="form-row mt-2">
                                        <div class="form-group col-md-3">
                                            <label>Categoría</label>
                                            <select id="nuevo-category" class="form-control">
                                                <option value="">Seleccionar...</option>
                                                <?php if(!empty($categories)) foreach($categories as $cat): ?>
                                                <option value="<?=$cat->id?>"><?=htmlspecialchars($cat->name)?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <button class="btn btn-primary btn-sm" id="btn-inscribir-partner">Inscribir</button>
                                    <button class="btn btn-secondary btn-sm" id="btn-cancelar-jugador-nuevo">Cancelar</button>
                                </div>
                                <div style="margin-top:8px;" id="nuevo-cancelar-wrap">
                                    <button class="btn btn-secondary btn-sm" id="btn-cancelar-jugador-nuevo2">Cancelar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BUSCADOR -->
                <div class="admin-filtros-row">
                    <div class="admin-filtro-buscar">
                        <input type="text" id="buscar-jugador" class="form-control" placeholder="Buscar por nombre o DNI...">
                    </div>
                    <div class="admin-filtro-cat">
                        <select id="filtro-categoria" class="form-control">
                            <option value="">Todas las categorías</option>
                            <?php foreach($categories as $cat): ?>
                            <option value="<?=$cat->id?>"><?=htmlspecialchars($cat->name)?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="admin-filtro-gen">
                        <select id="filtro-gender" class="form-control">
                            <option value="">Todos</option>
                            <option value="M">Caballeros</option>
                            <option value="F">Damas</option>
                        </select>
                    </div>
                    <div class="admin-filtro-contador">
                        <span class="text-muted small" id="contador-parejas"></span>
                    </div>
                </div>

                <!-- TABLA -->
                <table class="table table-hover table-sm" id="jugadoresTable">
                    <thead class="thead-light">
                        <tr>
                            <th>Nombre</th>
                            <th>DNI</th>
                            <th>Email</th>
                            <th>Género</th>
                            <th>Categoría</th>
                            <th width="80"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($jugadores)) foreach($jugadores as $j): ?>
                        <tr class="jugador-row"
                            data-nombre="<?=strtolower($j->name)?>"
                            data-dni="<?=$j->dni?>"
                            data-category="<?=$j->category_id?>"
                            data-gender="<?=$j->gender?>">
                            <td style="text-transform:capitalize"><?=strtolower($j->name)?></td>
                            <td><?=$j->dni?></td>
                            <td><?=$j->email?></td>
                            <td><?=$j->gender == 'M' ? '<span class="badge badge-primary">M</span>' : '<span class="badge badge-danger">F</span>'?></td>
                            <td><span class="badge badge-info"><?=$j->categoria?></span></td>
                            <td nowrap>
                                <?php if(empty($readonly)): ?>
                                <button class="btn btn-xs btn-warning btn-editar-jugador"
                                    data-id="<?=$j->id?>"
                                    data-reserva="<?=$j->reserva_id?>"
                                    data-name="<?=$j->name?>"
                                    data-dni="<?=$j->dni?>"
                                    data-email="<?=$j->email?>"
                                    data-gender="<?=$j->gender?>"
                                    data-category="<?=$j->category_id?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-xs btn-danger btn-borrar-jugador"
                                    data-id="<?=$j->id?>"
                                    data-reserva="<?=$j->reserva_id?>"
                                    data-name="<?=strtolower($j->name)?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if(empty($jugadores)): ?>
                <p class="text-center text-muted">No hay jugadores inscriptos.</p>
                <?php endif; ?>

            </div>
        </section>
    </div>
    <div id="sidemodal"><div class="content">hola</div></div>
    <div id="overlay"></div>
</div>


<script type="text/javascript">
const adminurl = '<?=base_url('admin')?>';
const token = '<?=$token?>';
const baseurl = '<?=base_url()?>';

$(function() {
	admin.dashboard.run();

	// Switch inscripciones
	$('#btn-inscripciones').on('click', function(){
		var estado = $(this).data('estado');
		var nuevoEstado = estado == '1' ? 0 : 1;
		$.ajax({
			url: adminurl + '/toggleInscripciones',
			type: 'POST',
			data: { value: nuevoEstado },
			headers: {'X-Auth-Token': token},
			success: function(res){
				if(res.action) {
					$(this).data('estado', nuevoEstado);
					if(nuevoEstado == 1) {
						$('#btn-inscripciones').removeClass('btn-secondary').addClass('btn-success').data('estado','1').html('<i class="fas fa-lock-open"></i> Inscripciones: <strong>Abiertas</strong>');
					} else {
						$('#btn-inscripciones').removeClass('btn-success').addClass('btn-secondary').data('estado','0').html('<i class="fas fa-lock"></i> Inscripciones: <strong>Cerradas</strong>');
					}
				} else {
					alert('Error al cambiar estado.');
				}
			}
		});
	});

	// Buscador
	function filtrar() {
		var buscar = $('#buscar-jugador').val().toLowerCase();
		var cat = $('#filtro-categoria').val();
		var gen = $('#filtro-gender').val();
		var visible = 0;

		// Filtrar tanto jugadores como parejas
		$('.jugador-row, .pareja-row').each(function(){
			var nombre = $(this).data('nombre');
			var dni = String($(this).data('dni') || '');
			var rowCat = String($(this).data('category'));
			var rowGen = $(this).data('gender');
			var ok = (!buscar || nombre.indexOf(buscar)>=0 || dni.indexOf(buscar)>=0) && (!cat || rowCat===cat) && (!gen || rowGen===gen);
			$(this).toggle(ok);
			if(ok) visible++;
		});
		$('#contador-jugadores').text(visible + ' jugadores');
		// Destacar filtros activos
		$('#filtro-categoria').toggleClass('active-filter', cat !== '');
		$('#filtro-gender').toggleClass('active-filter', gen !== '');
	}
	// Filtro por defecto: 1ra Caballeros
	$('#filtro-categoria').val('');
	$('#filtro-gender').val('');
	filtrar();

	$('#filtro-categoria, #filtro-gender').on('change', filtrar);
	$('#buscar-jugador').on('input', function(){
		var buscar = $(this).val();
		// Usar DataTables search si la tabla existe
		if ($.fn.DataTable.isDataTable('#jugadoresTable')) {
			$('#jugadoresTable').DataTable().search(buscar).draw();
		} else {
			// Fallback para búsqueda manual si DataTables no está disponible
			if(buscar.length > 0) {
				var visible = 0;
				buscar = buscar.toLowerCase();
				$('.jugador-row, .pareja-row').each(function(){
					var nombre = $(this).data('nombre');
					var dni = String($(this).data('dni') || '');
					var ok = nombre.indexOf(buscar) >= 0 || dni.indexOf(buscar) >= 0;
					$(this).toggle(ok);
					if(ok) visible++;
				});
				$('#contador-jugadores').text(visible + ' jugadores');
			} else {
				filtrar();
			}
		}
	});

	// Nuevo — abre modo búsqueda de partners
	$('#add').on('click', function(){
		$('#jugador-id, #jugador-reserva-id').val('');
		$('#form-jugador-titulo').text('Nuevo Jugador');
		$('#form-editar-fields').hide();
		$('#form-nuevo-fields').show();
		$('#buscar-partner').val('');
		$('#resultados-partner, #partner-seleccionado').hide();
		$('#nuevo-cancelar-wrap').show();
		$('#form-jugador').slideDown();
		$('html,body').animate({scrollTop:0}, 300);
	});

	$('#btn-cancelar-jugador').on('click', function(){ $('#form-jugador').slideUp(); });
	$('#btn-cancelar-jugador-nuevo, #btn-cancelar-jugador-nuevo2').on('click', function(){ $('#form-jugador').slideUp(); });

	// Autocomplete partner
	var _partnerTimer = null;
	$('#buscar-partner').on('input', function(){
		var q = $.trim($(this).val());
		clearTimeout(_partnerTimer);
		if(q.length < 2) { $('#autocomplete-partner').hide(); return; }
		_partnerTimer = setTimeout(function(){
			$.ajax({
				url: adminurl + '/buscarPartner',
				type: 'POST',
				data: { q: q },
				headers: {'X-Auth-Token': token},
				success: function(res){
					var html = '';
					if(!res.partners || !res.partners.length) {
						html = '<div style="padding:10px 14px; color:#888; font-size:13px;">Sin resultados</div>';
					} else {
						res.partners.forEach(function(p){
							var genBadge = p.gender=='M'
								? '<span style="background:#1a6fad;color:#fff;border-radius:10px;padding:1px 7px;font-size:10px;font-weight:700;margin-left:6px;">M</span>'
								: '<span style="background:#ad1a6f;color:#fff;border-radius:10px;padding:1px 7px;font-size:10px;font-weight:700;margin-left:6px;">F</span>';
							html += '<div class="ac-item" style="padding:9px 14px; cursor:pointer; border-bottom:1px solid #f0f0f0; display:flex; align-items:center; justify-content:space-between;" '
								+ 'data-id="'+p.id+'" data-name="'+p.name+'" data-dni="'+p.dni+'">'
								+ '<span style="font-size:13px; text-transform:capitalize; font-weight:600;">'+p.name.toLowerCase()+'</span>'
								+ '<span style="font-size:12px; color:#888;">'+p.dni + genBadge+'</span>'
								+ '</div>';
						});
					}
					$('#autocomplete-partner').html(html).show();
				}
			});
		}, 250);
	});

	// Cerrar autocomplete al hacer click afuera
	$(document).on('click', function(e){
		if(!$(e.target).closest('#buscar-partner, #autocomplete-partner').length) {
			$('#autocomplete-partner').hide();
		}
	});

	// Hover en items
	$(document).on('mouseenter', '.ac-item', function(){ $(this).css('background','#f0fae0'); });
	$(document).on('mouseleave', '.ac-item', function(){ $(this).css('background','#fff'); });

	// Seleccionar desde autocomplete
	$(document).on('click', '.ac-item', function(){
		var d = $(this).data();
		$('#nuevo-partner-id').val(d.id);
		$('#partner-sel-nombre').text(d.name.toLowerCase());
		$('#partner-sel-dni').text('DNI: ' + d.dni);
		$('#nuevo-category').val('');
		$('#buscar-partner').val('');
		$('#autocomplete-partner').hide();
		$('#partner-seleccionado').show();
		$('#nuevo-cancelar-wrap').hide();
	});

	$('#btn-cambiar-partner').on('click', function(){
		$('#partner-seleccionado').hide();
		$('#buscar-partner').val('');
		$('#nuevo-cancelar-wrap').show();
		$('#buscar-partner').focus();
	});

	// Inscribir partner
	$('#btn-inscribir-partner').on('click', function(){
		var partner_id = $('#nuevo-partner-id').val();
		var category   = $('#nuevo-category').val();
		if(!partner_id || !category){ alert('Seleccioná un jugador y una categoría.'); return; }
		$.ajax({
			url: adminurl + '/inscribirPartner',
			type: 'POST',
			data: { partner_id: partner_id, category: category },
			headers: {'X-Auth-Token': token},
			success: function(res){ if(res.action) location.reload(); else alert(res.msg || 'Error.'); }
		});
	});

	// Editar
	$(document).on('click', '.btn-editar-jugador', function(){
		var d = $(this).data();
		$('#jugador-id').val(d.id);
		$('#jugador-reserva-id').val(d.reserva);
		$('#form-jugador-titulo').text('Editar Jugador');
		$('#form-editar-fields').show();
		$('#form-nuevo-fields').hide();
		$('#jugador-name').val(d.name);
		$('#jugador-dni').val(d.dni);
		$('#jugador-email').val(d.email);
		$('#jugador-gender').val(d.gender);
		$('#jugador-category').val(d.category);
		$('#form-jugador').slideDown();
		$('html,body').animate({scrollTop:0}, 300);
	});

	// Guardar (editar)
	$('#btn-guardar-jugador').on('click', function(){
		var id = $('#jugador-id').val();
		var url = id ? adminurl + '/editJugador' : adminurl + '/addJugador';
		$.ajax({
			url: url, type: 'POST',
			data: { id:id, reserva_id:$('#jugador-reserva-id').val(), name:$('#jugador-name').val(), dni:$('#jugador-dni').val(), email:$('#jugador-email').val(), gender:$('#jugador-gender').val(), category:$('#jugador-category').val() },
			headers: {'X-Auth-Token': token},
			success: function(res){ if(res.action) location.reload(); else alert(res.msg || 'Error.'); }
		});
	});

	// Borrar

	// Descargar Excel
	$('#btn-descargar-excel').on('click', function(){
		var form = $('<form>', {
			method: 'POST',
			action: adminurl + '/descargarExcelJugadores'
		}).append($('<input>', {
			type: 'hidden',
			name: 'X-Auth-Token',
			value: token
		})).append($('<input>', {
			type: 'hidden',
			name: 'tournament_type',
			value: admin.dashboard.tournament_type
		}));
		$('body').append(form);
		form.submit();
		form.remove();
	});

    $(window).scroll(function(){
        if($(window).scrollTop() > $('header.header').innerHeight() + $('header.page-header').innerHeight()) {
            $('#sidemodal').addClass('fixed');
        } else {
            $('#sidemodal').removeClass('fixed');
        }
    })

    // Agregar Pareja
    $('#btn-agregar-pareja').on('click', function() {
        // Remover modal anterior si existe
        $('#modalAgregarPareja').remove();

        $.ajax({
            url: adminurl + '/getPartnersForPairing',
            type: 'POST',
            headers: {'X-Auth-Token': token, 'X-Requested-With': 'XMLHttpRequest'},
            success: function(res) {
                if(res.action) {
                    var partnersData = res.partners; // Guardar los datos para usar en el evento

                    var html = '';
                    html += '<div class="form-group"><label>Género</label>';
                    html += '<select class="newPairingGender form-control"><option value="">Seleccionar...</option><option value="M">Caballeros</option><option value="F">Damas</option></select></div>';
                    html += '<div class="form-group"><label>Categoría</label>';
                    html += '<select class="newPairingCategory form-control"><option value="">Seleccionar...</option>';
                    res.categories.forEach(function(cat) {
                        html += '<option value="' + cat.id + '">' + cat.name + '</option>';
                    });
                    html += '</select></div>';
                    html += '<div class="form-group"><label>Jugador 1</label>';
                    html += '<select class="newPairingPlayer1 form-control"><option value="">Seleccionar género primero...</option>';
                    html += '</select></div>';
                    html += '<div class="form-group"><label>Jugador 2</label>';
                    html += '<select class="newPairingPlayer2 form-control"><option value="">Seleccionar género primero...</option>';
                    html += '</select></div>';

                    var modal = $('<div class="modal fade" id="modalAgregarPareja" tabindex="-1" role="dialog"><div class="modal-dialog" role="document"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Agregar Pareja</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div><div class="modal-body">' + html + '</div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button><button type="button" class="btn btn-primary" id="btnGuardarPareja">Guardar</button></div></div></div></div>');

                    $('body').append(modal);
                    modal.modal('show');

                    // Filtrar jugadores cuando cambia el género
                    modal.find('.newPairingGender').on('change', function() {
                        var selectedGender = $(this).val();
                        var partner1 = modal.find('.newPairingPlayer1');
                        var partner2 = modal.find('.newPairingPlayer2');

                        partner1.find('option').not(':first').remove();
                        partner2.find('option').not(':first').remove();

                        if (selectedGender) {
                            partnersData.forEach(function(p) {
                                if (p.gender === selectedGender) {
                                    partner1.append('<option value="' + p.id + '">' + p.name + '</option>');
                                    partner2.append('<option value="' + p.id + '">' + p.name + '</option>');
                                }
                            });
                        }
                    });

                    // Guardar pareja
                    modal.find('#btnGuardarPareja').on('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        console.log('Click en Guardar detectado');

                        var gender = modal.find('.newPairingGender').val();
                        var category = modal.find('.newPairingCategory').val();
                        var player1 = modal.find('.newPairingPlayer1').val();
                        var player2 = modal.find('.newPairingPlayer2').val();

                        console.log('Gender:', gender, 'Category:', category, 'Player1:', player1, 'Player2:', player2);

                        if (!gender || !category || !player1 || !player2) {
                            alert('Completa todos los campos');
                            return;
                        }

                        if (player1 === player2) {
                            alert('Los jugadores deben ser diferentes');
                            return;
                        }

                        console.log('Enviando AJAX a:', adminurl + '/addPairing');
                        $.ajax({
                            url: adminurl + '/addPairing',
                            type: 'POST',
                            data: {
                                gender: gender,
                                category: category,
                                player1: player1,
                                player2: player2
                            },
                            headers: {'X-Auth-Token': token, 'X-Requested-With': 'XMLHttpRequest'},
                            success: function(res) {
                                console.log('Respuesta AJAX:', res);
                                if(res.action) {
                                    // Esperar a que el modal se oculte antes de removerlo
                                    modal.on('hidden.bs.modal', function() {
                                        $(this).remove();
                                        $('.modal-backdrop').remove();
                                        $('body').removeClass('modal-open');
                                    });
                                    modal.modal('hide');
                                    admin.dashboard.getReservations(function() {
                                        showNotification('success', 'Pareja agregada correctamente');
                                    });
                                } else {
                                    alert('Error: ' + (res.msg || 'Error desconocido'));
                                }
                            },
                            error: function(xhr, status, error) {
                                console.log('Error AJAX:', xhr.status, status, error);
                                alert('Error al agregar pareja: ' + xhr.status + ' ' + error);
                            }
                        });
                    });

                    modal.on('hidden.bs.modal', function() {
                        modal.remove();
                    });
                }
            }
        });
    });
});
</script>

<style>
#filtro-categoria.active-filter,
#filtro-gender.active-filter {
	border: 2px solid #a5d051 !important;
	background-color: #f0fae0 !important;
	font-weight: 600 !important;
	color: #2d6a00 !important;
}
</style>