<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container mt-4">
	<div class="card">
		<div class="card-header bg-success text-white">
			<h4 class="mb-0"><i class="fas fa-calendar-check"></i> Partidos Programados (<?=count($partidos)?>)</h4>
		</div>
		<div class="card-body">
			<?php if(count($partidos) > 0): ?>
				<div class="alert alert-success mb-3">
					Se han programado <strong><?=count($partidos)?></strong> partidos pendientes de resultado.
				</div>

				<div class="table-responsive">
					<table class="table table-striped table-bordered">
						<thead class="table-light">
							<tr>
								<th>Categoría</th>
								<th>Ronda</th>
								<th>Jugador 1</th>
								<th>Jugador 2</th>
								<th>Fecha</th>
								<th>Hora</th>
								<th>Deadline</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($partidos as $p): ?>
							<tr>
								<td><strong><?=$p->categoria?></strong></td>
								<td><?=$p->ronda?></td>
								<td><?=$p->jugador1_nombres?></td>
								<td><?=$p->jugador2_nombres?></td>
								<td>
									<?php if($p->fecha): ?>
										<?=date('d/m/Y', strtotime($p->fecha))?>
									<?php else: ?>
										<span class="text-muted">-</span>
									<?php endif; ?>
								</td>
								<td>
									<?php if($p->hora): ?>
										<?=$p->hora?>
									<?php else: ?>
										<span class="text-muted">-</span>
									<?php endif; ?>
								</td>
								<td>
									<?php if($p->deadline): ?>
										<small><?=date('d/m/Y', strtotime($p->deadline))?></small>
									<?php else: ?>
										<span class="text-muted">-</span>
									<?php endif; ?>
								</td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			<?php else: ?>
				<div class="alert alert-info">
					<i class="fas fa-info-circle"></i> No hay partidos programados.
				</div>
			<?php endif; ?>

			<div class="mt-3">
				<a href="<?=base_url('admin/enviarRecordatorios')?>" class="btn btn-primary">
					<i class="fas fa-bell"></i> Volver a Recordatorios
				</a>
			</div>
		</div>
	</div>
</div>
