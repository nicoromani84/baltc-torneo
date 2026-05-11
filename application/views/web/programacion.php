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
	<div id="programacion-page" class="container">
		<div class="container">
			<h1>Programación</h1>
			<p>Torneo Interno de Singles</p>

			<?php if(!empty($partidos)): ?>
			<?php
				$grupo_actual = '';
				foreach($partidos as $p):
					$gen_label = $p->gender == 'M' ? 'Caballeros' : ($p->gender == 'F' ? 'Damas' : '');
					$icono = $p->gender == 'M' ? 'fa-male' : 'fa-female';
					$grupo = $p->categoria . '||' . $p->gender;
					if($grupo !== $grupo_actual):
						if($grupo_actual !== '') echo '</div></div>';
						$grupo_actual = $grupo;
			?>
			<div class="resultado-categoria">
				<div class="resultado-categoria-header">
					<i class="fas <?=$icono?>"></i> <?=$p->categoria?> — <?=$gen_label?>
				</div>
				<div class="resultado-lista">
			<?php endif; ?>

				<div class="prog-partido <?=$p->fecha ? 'con-fecha' : 'sin-fecha'?>">
					<div class="prog-fecha-hora">
						<?php if($p->fecha): ?>
						<div class="prog-fecha"><?=date('d/m', strtotime($p->fecha))?></div>
						<div class="prog-hora"><?=$p->hora ? substr($p->hora,0,5) : 'hora a confirmar'?></div>
						<?php else: ?>
						<div class="prog-fecha">—</div>
						<div class="prog-hora">a programar</div>
						<?php endif; ?>
					</div>
					<div class="prog-info">
						<div class="prog-ronda"><?=$p->ronda?></div>
						<div class="prog-enfrentamiento">
							<span class="prog-jugador <?=$p->ganador_id==$p->jugador1_id?'ganador':($p->ganador_id?'perdedor':'')?>">
								<?=strtolower($p->jugador1)?>
							</span>
							<span class="prog-vs"><?=$p->score ?: 'vs'?></span>
							<span class="prog-jugador <?=$p->ganador_id==$p->jugador2_id?'ganador':($p->ganador_id?'perdedor':'')?>">
								<?=strtolower($p->jugador2)?>
							</span>
						</div>
					</div>
					<?php if($p->ganador_id): ?>
					<div class="prog-resultado-badge"><i class="fas fa-check"></i></div>
					<?php endif; ?>
				</div>

			<?php endforeach; ?>
			<?php if($grupo_actual !== '') echo '</div></div>'; ?>

			<?php else: ?>
			<div class="resultado-vacio">
				<i class="fas fa-calendar-alt"></i>
				<p>Aún no hay partidos programados.</p>
			</div>
			<?php endif; ?>
		</div>
	</div>
</div>

<style>
#programacion-page {
	min-height: calc(100vh - 110px);
	display: table-cell;
	vertical-align: top;
	height: 100%;
	padding-top: 30px;
	padding-bottom: 30px;
}
#programacion-page h1 {
	text-align: center;
	color: #fff;
	text-transform: uppercase;
	font-weight: 700;
	font-size: 38px;
	text-shadow: 4px 4px 12px rgba(0,0,0,0.9);
	margin: 0;
}
#programacion-page > .container > p {
	color: #fff;
	text-align: center;
	font-size: 18px;
	text-shadow: 2px 2px 6px rgba(0,0,0,0.8);
	margin: 5px 0 25px 0;
}
.prog-partido {
	display: flex;
	align-items: center;
	gap: 12px;
	padding: 12px 15px;
	border-bottom: 1px solid rgba(255,255,255,0.07);
}
.prog-partido:last-child { border-bottom: none; }
.prog-fecha-hora {
	text-align: center;
	min-width: 52px;
	flex-shrink: 0;
}
.prog-fecha {
	font-size: 15px;
	font-weight: 800;
	color: #a5d051;
	line-height: 1.2;
}
.prog-hora {
	font-size: 11px;
	color: rgba(255,255,255,0.5);
}
.sin-fecha .prog-fecha { color: rgba(255,255,255,0.3); font-size: 18px; }
.prog-info { flex: 1; min-width: 0; }
.prog-ronda {
	font-size: 10px;
	text-transform: uppercase;
	letter-spacing: 0.5px;
	color: rgba(255,255,255,0.4);
	font-weight: 700;
	margin-bottom: 4px;
}
.prog-enfrentamiento {
	display: flex;
	align-items: center;
	gap: 8px;
}
.prog-jugador {
	flex: 1;
	font-size: 13px;
	font-weight: 600;
	color: rgba(255,255,255,0.85);
	text-transform: capitalize;
	white-space: nowrap;
	overflow: hidden;
	text-overflow: ellipsis;
}
.prog-jugador.ganador { color: #a5d051; font-weight: 800; }
.prog-jugador.perdedor { color: rgba(255,255,255,0.3); text-decoration: line-through; }
.prog-jugador:last-child { text-align: right; }
.prog-vs {
	font-size: 11px;
	font-weight: 700;
	color: rgba(255,255,255,0.3);
	flex-shrink: 0;
}
.prog-resultado-badge {
	width: 24px;
	height: 24px;
	background: #a5d051;
	border-radius: 50%;
	display: flex;
	align-items: center;
	justify-content: center;
	font-size: 11px;
	color: #1a1a2e;
	flex-shrink: 0;
}
.resultado-vacio { text-align:center; padding:40px 0; color:rgba(255,255,255,0.5); }
.resultado-vacio i { font-size:50px; color:#a5d051; margin-bottom:15px; display:block; }
.resultado-vacio p { font-size:18px; }
@media (max-width: 575.98px) {
	#programacion-page h1 { font-size: 26px; }
	.prog-jugador { font-size: 12px; }
}
</style>

<script type="text/javascript">
var baseurl = '<?=base_url()?>';
var token = '<?=$token?>';
</script>
