<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="page-content">
    <div class="content-inner">
        <div style="padding:20px 24px;background:#fff;border-bottom:1px solid #dee2e6;">
            <h2 style="margin:0;font-size:20px;"><i class="fas fa-trophy"></i> Ranking de Jugadores</h2>
            <p style="margin:8px 0 0;font-size:13px;color:#666;">Posiciones basadas en victorias ponderadas por categoría (1ra=3pts, 2da=2pts, 3era=1pt)</p>
        </div>

        <div style="padding:20px;">
            <ul class="nav nav-tabs mb-4" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="tab-caballeros" data-toggle="tab" href="#content-caballeros" role="tab" aria-selected="true">
                        <i class="fas fa-male mr-2"></i><strong>Caballeros</strong>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab-damas" data-toggle="tab" href="#content-damas" role="tab" aria-selected="false">
                        <i class="fas fa-female mr-2"></i><strong>Damas</strong>
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <!-- CABALLEROS -->
                <div class="tab-pane fade show active" id="content-caballeros" role="tabpanel">
                    <div class="card">
                        <div class="card-body">
                            <div id="loading-caballeros" class="text-center py-4">
                                <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                            </div>
                            <div id="ranking-caballeros" style="display:none;">
                                <table class="table table-sm table-hover mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width:60px;text-align:center;">Posición</th>
                                            <th>Jugador</th>
                                            <th style="width:120px;text-align:center;">Puntos</th>
                                            <th style="width:120px;text-align:center;">Victorias</th>
                                            <th style="width:250px;">Desglose por categoría</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tabla-caballeros"></tbody>
                                </table>
                            </div>
                            <div id="empty-caballeros" style="display:none;" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-3" style="opacity:0.3; display:block;"></i>
                                Sin datos disponibles
                            </div>
                        </div>
                    </div>
                </div>

                <!-- DAMAS -->
                <div class="tab-pane fade" id="content-damas" role="tabpanel">
                    <div class="card">
                        <div class="card-body">
                            <div id="loading-damas" class="text-center py-4">
                                <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                            </div>
                            <div id="ranking-damas" style="display:none;">
                                <table class="table table-sm table-hover mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width:60px;text-align:center;">Posición</th>
                                            <th>Jugador</th>
                                            <th style="width:120px;text-align:center;">Puntos</th>
                                            <th style="width:120px;text-align:center;">Victorias</th>
                                            <th style="width:250px;">Desglose por categoría</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tabla-damas"></tbody>
                                </table>
                            </div>
                            <div id="empty-damas" style="display:none;" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-3" style="opacity:0.3; display:block;"></i>
                                Sin datos disponibles
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const adminurl = '<?=base_url('admin')?>';
const token    = '<?=$token?>';

$(function(){
    cargarRanking('M', 'caballeros');
    cargarRanking('F', 'damas');

    function cargarRanking(gender, tipo) {
        $.ajax({
            url: adminurl + '/getRankingData',
            type: 'POST',
            data: { gender: gender },
            headers: {'X-Auth-Token': token},
            success: function(res){
                if(!res.action) return;

                var ranking = res.ranking || [];
                var detailed = res.detailed || [];

                if(!ranking.length) {
                    $('#loading-'+tipo).hide();
                    $('#empty-'+tipo).show();
                    return;
                }

                // Agrupar detalles por jugador
                var desglose = {};
                detailed.forEach(function(d){
                    if(!desglose[d.id]) desglose[d.id] = [];
                    desglose[d.id].push(d);
                });

                var tbody = '';
                ranking.forEach(function(r, idx){
                    var posicion = idx + 1;
                    var medal = '';
                    if(posicion === 1) medal = '<i class="fas fa-medal" style="color:#ffd700; font-size:18px;"></i> ';
                    else if(posicion === 2) medal = '<i class="fas fa-medal" style="color:#c0c0c0; font-size:18px;"></i> ';
                    else if(posicion === 3) medal = '<i class="fas fa-medal" style="color:#cd7f32; font-size:18px;"></i> ';

                    var desglose_html = '';
                    if(desglose[r.id]) {
                        desglose[r.id].forEach(function(d){
                            var cat_badge = '';
                            if(d.categoria.toLowerCase().includes('1')) {
                                cat_badge = '<span class="badge badge-primary">1ra</span>';
                            } else if(d.categoria.toLowerCase().includes('2')) {
                                cat_badge = '<span class="badge badge-info">2da</span>';
                            } else if(d.categoria.toLowerCase().includes('3')) {
                                cat_badge = '<span class="badge badge-secondary">3era</span>';
                            }
                            desglose_html += cat_badge + ' (' + d.victorias_categoria + 'v × ' + d.puntos_categoria + 'pts) ';
                        });
                    }

                    tbody += '<tr>';
                    tbody += '<td style="text-align:center; font-weight:bold;">' + medal + posicion + '</td>';
                    tbody += '<td style="text-transform:capitalize;">' + r.name.toLowerCase() + '</td>';
                    tbody += '<td style="text-align:center;"><strong>' + (r.puntos || 0) + '</strong></td>';
                    tbody += '<td style="text-align:center;">' + (r.victorias || 0) + '</td>';
                    tbody += '<td style="font-size:12px;">' + desglose_html + '</td>';
                    tbody += '</tr>';
                });

                $('#tabla-'+tipo).html(tbody);
                $('#loading-'+tipo).hide();
                $('#ranking-'+tipo).show();
            }
        });
    }
});
</script>
