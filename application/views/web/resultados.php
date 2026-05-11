<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<header>
	<div class="container">
		<div class="logo">
			<img src="<?=asset_url('img')?>/logo.png" alt="Logo">
		</div>
		<ul class="buttons">
			<li>
				<a href="<?=base_url('menu')?>">
					<span class="icon-logout"><i class="fas fa-arrow-left"></i></span>
				</a>
			</li>
		</ul>
	</div>
</header>

<div class="page reserve-page">
	<div id="resultados-page" class="container">
		<div class="container">
			<h1>Resultados</h1>
			<p>Torneo Interno de Singles</p>

			<?php if(!empty($partidos)): ?>
			<?php
				$grupo_actual = '';
				foreach($partidos as $p):
					$gen_label = $p->gender == 'M' ? 'Caballeros' : ($p->gender == 'F' ? 'Damas' : '');
					$icono = $p->gender == 'M' ? 'fa-male' : 'fa-female';
					$grupo = $p->categoria . '||' . $p->gender . '||' . $p->ronda;
					$grupo_cat = $p->categoria . '||' . $p->gender;
					if($grupo_cat !== $grupo_actual):
						if($grupo_actual !== '') echo '</div></div>';
						$grupo_actual = $grupo_cat;
			?>
			<div class="resultado-categoria">
				<div class="resultado-categoria-header <?=($mi_category && $p->category==$mi_category && $p->gender==$mi_gender)?'mi-categoria':''?>">
					<i class="fas <?=$icono?>"></i> <?=$p->categoria?> — <?=$gen_label?>
					<?php if($mi_category && $p->category==$mi_category && $p->gender==$mi_gender): ?>
					<span class="mi-categoria-badge">Mi categoría</span>
					<?php endif; ?>
				</div>
				<div class="resultado-lista">
			<?php endif; ?>

				<div class="resultado-partido <?=$p->ganador_id ? 'jugado' : 'pendiente'?>">
					<div class="resultado-ronda"><?=$p->ronda?></div>
					<div class="resultado-enfrentamiento">
						<span class="resultado-jugador <?=$p->ganador_id==$p->jugador1_id?'ganador':($p->ganador_id?'perdedor':'')?>">
							<?=$p->ganador_id==$p->jugador1_id ? '<i class="fas fa-check-circle"></i> ' : ''?>
							<?=strtolower($p->jugador1)?>
						</span>
						<span class="resultado-vs">
							<?=$p->score ? $p->score : 'vs'?>
						</span>
						<span class="resultado-jugador <?=$p->ganador_id==$p->jugador2_id?'ganador':($p->ganador_id?'perdedor':'')?>">
							<?=$p->ganador_id==$p->jugador2_id ? '<i class="fas fa-check-circle"></i> ' : ''?>
							<?=strtolower($p->jugador2)?>
						</span>
					</div>
					<?php if(!empty($p->fecha)): ?>
					<div class="resultado-fecha-prog"><i class="fas fa-calendar-alt"></i> <?=date('d/m', strtotime($p->fecha))?><?=$p->hora ? ' — ' . substr($p->hora,0,5) : ''?></div>
					<?php endif; ?>
				<?php if(!$p->ganador_id): ?>
					<div class="resultado-badge-pendiente">pendiente</div>
					<?php endif; ?>
				</div>

			<?php endforeach; ?>
			<?php if($grupo_actual !== '') echo '</div></div>'; ?>

			<?php else: ?>
			<div class="resultado-vacio">
				<i class="fas fa-trophy"></i>
				<p>Aún no hay resultados cargados.</p>
			</div>
			<?php endif; ?>
		</div>
	</div>
</div>

<style>
#resultados-page {
	min-height: calc(100vh - 110px);
	display: table-cell;
	vertical-align: top;
	height: 100%;
	padding-top: 30px;
	padding-bottom: 30px;
}
#resultados-page h1 {
	text-align: center;
	color: #fff;
	text-transform: uppercase;
	font-weight: 700;
	font-size: 38px;
	text-shadow: 4px 4px 12px rgba(0,0,0,0.9);
	margin: 0;
}
#resultados-page > .container > p {
	color: #fff;
	text-align: center;
	font-size: 18px;
	text-shadow: 2px 2px 6px rgba(0,0,0,0.8);
	margin: 5px 0 25px 0;
}
.resultado-categoria {
	background: rgba(0,0,0,.45);
	border-radius: 8px;
	margin-bottom: 15px;
	overflow: hidden;
}
.resultado-categoria-header {
	background: rgba(165,208,81,0.3);
	border-left: 4px solid #a5d051;
	color: #a5d051;
	font-weight: 700;
	font-size: 15px;
	text-transform: uppercase;
	padding: 10px 15px;
	letter-spacing: 0.5px;
}
.resultado-lista { padding: 5px 0; }
.resultado-partido {
	padding: 10px 15px;
	border-bottom: 1px solid rgba(255,255,255,0.07);
}
.resultado-partido:last-child { border-bottom: none; }
.resultado-ronda {
	font-size: 10px;
	text-transform: uppercase;
	letter-spacing: 0.5px;
	color: rgba(255,255,255,0.4);
	margin-bottom: 5px;
}
.resultado-enfrentamiento {
	display: flex;
	align-items: center;
	gap: 10px;
}
.resultado-jugador {
	flex: 1;
	font-size: 13px;
	color: rgba(255,255,255,0.7);
	text-transform: capitalize;
}
.resultado-jugador.ganador {
	color: #a5d051;
	font-weight: 700;
}
.resultado-jugador.perdedor {
	color: rgba(255,255,255,0.3);
	text-decoration: line-through;
}
.resultado-jugador:last-child { text-align: right; }
.resultado-vs {
	font-size: 11px;
	font-weight: 700;
	color: #a5d051;
	white-space: nowrap;
	background: rgba(165,208,81,0.15);
	padding: 2px 8px;
	border-radius: 4px;
}
.resultado-fecha-prog {
	font-size: 11px;
	color: rgba(165,208,81,0.8);
	margin-top: 4px;
	display: flex;
	align-items: center;
	gap: 5px;
}
.resultado-badge-pendiente {
	font-size: 10px;
	color: rgba(255,255,255,0.3);
	text-align: center;
	margin-top: 3px;
	font-style: italic;
}
.resultado-categoria-header.mi-categoria {
	background: rgba(165,208,81,0.4) !important;
	border-left: 4px solid #a5d051 !important;
}
.mi-categoria-badge {
	float: right;
	font-size: 10px;
	background: #a5d051;
	color: #1a1a2e;
	padding: 2px 8px;
	border-radius: 10px;
	font-weight: 800;
	letter-spacing: 0.3px;
}
.resultado-vacio {
	text-align: center;
	padding: 40px 0;
	color: rgba(255,255,255,0.5);
}
.resultado-vacio i { font-size: 50px; color: #a5d051; margin-bottom: 15px; display: block; }
.resultado-vacio p { font-size: 18px; }
@media (max-width: 575.98px) {
	#resultados-page h1 { font-size: 26px; }
	.resultado-jugador { font-size: 11px; }
}
</style>

<script type="text/javascript">
var baseurl = '<?=base_url()?>';
var token = '<?=$token?>';
</script>
