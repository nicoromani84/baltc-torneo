<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container mt-4">
	<div class="card">
		<div class="card-header bg-info text-white">
			<h4 class="mb-0"><i class="fas fa-users"></i> Todos los Jugadores (<?=count($partners)?>)</h4>
		</div>
		<div class="card-body">
			<p class="text-muted mb-3">Filtro de cliente (busca en tiempo real):</p>
			<input type="text" id="filtro" class="form-control mb-3" placeholder="Escribe nombre o DNI para filtrar...">

			<div class="table-responsive">
				<table class="table table-striped table-bordered table-sm" id="tabla-partners">
					<thead class="table-light">
						<tr>
							<th>Nombre</th>
							<th>DNI</th>
							<th>Email</th>
							<th>Género</th>
							<th>Estado</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($partners as $p): ?>
						<tr data-nombre="<?=strtolower($p->name)?>" data-dni="<?=$p->dni?>">
							<td><strong><?=$p->name?></strong></td>
							<td><?=$p->dni?></td>
							<td>
								<?php if($p->email): ?>
									<?=$p->email?>
								<?php else: ?>
									<span class="text-danger"><i class="fas fa-times-circle"></i> Sin email</span>
								<?php endif; ?>
							</td>
							<td>
								<?php if($p->gender == 'M'): ?>
									<span class="badge badge-primary">Caballero</span>
								<?php else: ?>
									<span class="badge badge-danger">Dama</span>
								<?php endif; ?>
							</td>
							<td>
								<?php
									if($p->reservation_ids && $p->tournament_types):
										$types = explode(',', $p->tournament_types);
										$has_singles = in_array('singles', $types);
										$has_doubles = in_array('doubles', $types);
									else:
										$has_singles = false;
										$has_doubles = false;
									endif;
								?>
								<?php if($has_singles): ?>
									<span class="badge badge-success">Singles</span>
								<?php endif; ?>
								<?php if($has_doubles): ?>
									<span class="badge badge-warning">Dobles</span>
								<?php endif; ?>
								<?php if(!$has_singles && !$has_doubles): ?>
									<span class="badge badge-secondary">No inscripto</span>
								<?php endif; ?>
							</td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<script>
$(function(){
	$('#filtro').on('keyup', function(){
		var filtro = $(this).val().toLowerCase();
		if(filtro.length === 0) {
			$('#tabla-partners tbody tr').show();
			return;
		}

		$('#tabla-partners tbody tr').each(function(){
			var nombre = $(this).data('nombre');
			var dni = $(this).data('dni');

			if(nombre.includes(filtro) || dni.includes(filtro)) {
				$(this).show();
			} else {
				$(this).hide();
			}
		});
	});
});
</script>
