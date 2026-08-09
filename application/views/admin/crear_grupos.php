<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="container mt-4">
	<div class="card">
		<div class="card-header bg-dark text-white">
			<h4 class="mb-0"><i class="fas fa-users"></i> Crear Grupos - <?=$categoria_nombre?> <?=$genero_nombre?></h4>
		</div>
		<div class="card-body">
			<p class="text-muted">Asigná cada jugador a un grupo. Se generarán automáticamente todos los partidos de la fase de grupos (round-robin).</p>

			<form id="form-grupos">
				<input type="hidden" name="category" value="<?=$category_id?>">
				<input type="hidden" name="gender" value="<?=$gender?>">

				<div class="table-responsive">
					<table class="table table-bordered">
						<thead class="table-light">
							<tr>
								<th style="width:40%">Jugador</th>
								<th style="width:60%">Grupo</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($jugadores as $j): ?>
							<tr>
								<td><strong><?=htmlspecialchars($j->jugador)?></strong></td>
								<td>
									<select name="grupo[<?=$j->jugador_id?>]" class="form-control form-control-sm" required>
										<option value="">Elegir grupo...</option>
										<option value="A">Grupo A</option>
										<option value="B">Grupo B</option>
										<option value="C">Grupo C</option>
										<option value="D">Grupo D</option>
									</select>
								</td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>

				<div class="alert alert-info mt-3">
					<strong>Total de jugadores:</strong> <?=count($jugadores)?>
				</div>

				<div class="mt-3">
					<button type="submit" class="btn btn-success btn-lg">
						<i class="fas fa-check"></i> Generar Grupos
					</button>
					<a href="<?=base_url('admin/draws')?>" class="btn btn-secondary btn-lg">
						<i class="fas fa-times"></i> Cancelar
					</a>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
$(function(){
	var baseurl = '<?=base_url()?>';
	var token = '<?=$token?>';

	$('#form-grupos').on('submit', function(e){
		e.preventDefault();

		var formdata = $(this).serializeArray();
		var grupos = {};
		var todoAsignado = true;

		$.each(formdata, function(i, field) {
			if(field.name.startsWith('grupo[')) {
				if(!field.value) {
					todoAsignado = false;
					return false;
				}
				grupos[field.name.match(/\d+/)[0]] = field.value;
			}
		});

		if(!todoAsignado) {
			alert('Debes asignar todos los jugadores a un grupo.');
			return;
		}

		var btn = $(this).find('button[type=submit]');
		btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Generando...');

		$.ajax({
			url: baseurl + 'admin/guardarGrupos',
			type: 'POST',
			data: {
				category: $('input[name="category"]').val(),
				gender: $('input[name="gender"]').val(),
				grupos: JSON.stringify(grupos)
			},
			headers: { 'X-Auth-Token': token },
			success: function(res) {
				if(res.action) {
					alert('¡Grupos generados! Los partidos se crearon automáticamente.');
					location.href = baseurl + 'admin/partidos?cat=' + $('input[name="category"]').val() + '&gen=' + $('input[name="gender"]').val();
				} else {
					alert('Error: ' + (res.msg || 'Error desconocido'));
					btn.prop('disabled', false).html('<i class="fas fa-check"></i> Generar Grupos');
				}
			},
			error: function() {
				alert('Error de conexión');
				btn.prop('disabled', false).html('<i class="fas fa-check"></i> Generar Grupos');
			}
		});
	});
});
</script>
