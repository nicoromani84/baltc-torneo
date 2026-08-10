<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container mt-4">
	<div class="card">
		<div class="card-header bg-warning text-dark">
			<h4 class="mb-0"><i class="fas fa-envelope-open"></i> Jugadores en Dobles Sin Email Registrado</h4>
		</div>
		<div class="card-body">
			<?php if(count($jugadores) > 0): ?>
				<div class="alert alert-warning mb-3">
					<strong><?=count($jugadores)?> jugador/es</strong> inscritos en dobles no tienen email registrado.
				</div>

				<div class="table-responsive">
					<table class="table table-striped table-bordered">
						<thead class="table-light">
							<tr>
								<th>Nombre</th>
								<th>DNI</th>
								<th>Género</th>
								<th>Email</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($jugadores as $j): ?>
							<tr>
								<td><strong><?=$j->name?></strong></td>
								<td><?=$j->dni?></td>
								<td>
									<?php if($j->gender == 'M'): ?>
										<span class="badge badge-primary">Caballero</span>
									<?php else: ?>
										<span class="badge badge-danger">Dama</span>
									<?php endif; ?>
								</td>
								<td>
									<?php if(empty($j->email)): ?>
										<span class="text-danger"><i class="fas fa-times-circle"></i> Sin email</span>
									<?php else: ?>
										<?=$j->email?>
									<?php endif; ?>
								</td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			<?php else: ?>
				<div class="alert alert-success">
					<i class="fas fa-check-circle"></i> Todos los jugadores en dobles tienen email registrado.
				</div>
			<?php endif; ?>

			<div class="mt-3">
				<a href="<?=base_url('admin/draws')?>" class="btn btn-secondary">
					<i class="fas fa-arrow-left"></i> Volver
				</a>
			</div>
		</div>
	</div>
</div>
