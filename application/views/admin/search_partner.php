<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container mt-4">
	<div class="card">
		<div class="card-header bg-info text-white">
			<h4 class="mb-0"><i class="fas fa-search"></i> Búsqueda de Jugador: <?=htmlspecialchars($nombre_busca)?></h4>
		</div>
		<div class="card-body">
			<form method="get" action="<?=base_url('admin/searchPartner')?>" class="mb-4">
				<div class="input-group">
					<input type="text" name="q" class="form-control" placeholder="Nombre o DNI..." value="<?=htmlspecialchars($nombre_busca)?>" autofocus>
					<div class="input-group-append">
						<button class="btn btn-info" type="submit"><i class="fas fa-search"></i> Buscar</button>
					</div>
				</div>
			</form>

			<?php if(count($partners) > 0): ?>
				<div class="alert alert-info mb-3">
					Se encontraron <strong><?=count($partners)?></strong> resultado/s.
				</div>

				<div class="table-responsive">
					<table class="table table-striped table-bordered">
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
							<tr>
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
			<?php else: ?>
				<div class="alert alert-warning">
					<i class="fas fa-exclamation-triangle"></i> No se encontraron resultados para "<strong><?=htmlspecialchars($nombre_busca)?></strong>"
				</div>
			<?php endif; ?>

			<div class="mt-3">
				<a href="<?=base_url('admin/partners')?>" class="btn btn-secondary">
					<i class="fas fa-arrow-left"></i> Volver a Partners
				</a>
			</div>
		</div>
	</div>
</div>
