<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<header>
	<div class="container">
		<div class="logo">
			<img src="<?=asset_url('img')?>/logo.png" alt="Logo">
		</div>
		<ul class="buttons">
			<li><a href="<?=base_url('menu')?>"><span class="icon-logout"><i class="fas fa-arrow-left"></i></span></a></li>
		</ul>
	</div>
</header>

<div class="page reserve-page">
	<div class="container" style="padding: 20px;">
		<h1 style="color: #fff; text-align: center; margin-bottom: 30px;">Draws</h1>

		<?php if($draws): ?>
			<?php foreach($draws as $d): ?>
				<div style="margin-bottom: 20px; background: rgba(0,0,0,0.4); padding: 15px; border-radius: 8px;">
					<h3 style="color: #a5d051; margin: 0 0 15px 0;">
						<?=$d->categoria?> (<?=$d->gender === 'X' ? 'Mixto' : ($d->gender === 'M' ? 'Caballeros' : 'Damas')?>)
					</h3>
					<div id="draw-<?=$d->category?>-<?=$d->gender?>" class="draw-content" style="color: #fff;"></div>
				</div>
			<?php endforeach; ?>
		<?php else: ?>
			<p style="color: #fff; text-align: center;">No hay draws sorteados</p>
		<?php endif; ?>
	</div>
</div>

<script>
$(function(){
	var baseurl = '<?=base_url()?>';
	<?php foreach($draws as $d): ?>
		loadDraw(<?=$d->category?>, '<?=$d->gender?>', '#draw-<?=$d->category?>-<?=$d->gender?>');
	<?php endforeach; ?>
});

function loadDraw(cat, gen, selector) {
	$.get(baseurl + 'drawspublic/getData?cat=' + cat + '&gen=' + gen, function(data) {
		var res = JSON.parse(data);
		if(!res.partidos || res.partidos.length === 0) {
			$(selector).html('<p>No hay datos</p>');
			return;
		}

		var html = '<table style="width: 100%; color: #fff;">';
		$.each(res.partidos, function(i, p) {
			var j1 = p.jugador1 || 'BYE';
			var j2 = p.jugador2 || 'BYE';
			var ganador = p.ganador || '';
			html += '<tr style="border-bottom: 1px solid rgba(165,208,81,0.2);">';
			html += '<td style="padding: 8px; text-align: left;">' + j1 + '</td>';
			html += '<td style="padding: 8px; text-align: center; color: #a5d051; font-weight: bold;">' + (p.score || 'vs') + '</td>';
			html += '<td style="padding: 8px; text-align: right;">' + j2 + '</td>';
			html += '</tr>';
		});
		html += '</table>';
		$(selector).html(html);
	});
}
</script>
