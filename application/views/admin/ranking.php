<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="page-content">
    <div class="content-inner">
        <div style="padding:20px 24px;background:#fff;border-bottom:1px solid #dee2e6;display:flex;justify-content:space-between;align-items:center;">
            <div>
                <h2 style="margin:0;font-size:20px;"><i class="fas fa-trophy"></i> Ranking de Jugadores</h2>
                <p style="margin:8px 0 0;font-size:13px;color:#666;">Posiciones basadas en victorias ponderadas por ronda y categoría</p>
            </div>
            <a href="<?=base_url('admin/descargarRankingPDF')?>" class="btn btn-primary" title="Descargar explicación del ranking en PDF">
                <i class="fas fa-download"></i> Descargar PDF
            </a>
        </div>
            <div style="margin:12px 0 0; padding:10px 12px; background:#f0f7ff; border-left:3px solid #0066cc; border-radius:3px; font-size:12px; color:#333;">
                <strong>Sistema de puntuación con movilidad entre categorías:</strong>
                <div style="margin:6px 0 0 0; line-height:1.5;">
                    • <strong>Puntos base:</strong> 1ra +270 | 2da +150 | 3era +100<br>
                    • <strong>Fórmula:</strong> (puntos_ronda × multiplicador) ÷ divisor<br>
                    • <strong>Ejemplo Final (80 pts):</strong> 1ra: 240÷1=240 | 2da: 160÷1.3=123 | 3era: 80÷4.8=17<br>
                    • <strong>Movilidad garantizada:</strong> Finalista 2da (273) > peor 1ra (270) ✓ | Mejor 3era máx (267) < peor 1ra (270) ✓ | 3era nunca salta 2 categorías
                </div>
            </div>
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
                            <div id="loading-caballeros" class="text-center py-3">
                                <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                                <p class="text-muted mt-2 mb-0">Cargando...</p>
                            </div>
                            <div id="ranking-caballeros" style="display:none;">
                                <table class="table table-sm table-hover mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width:60px;text-align:center;">Pos</th>
                                            <th style="width:60px;text-align:center;">Cat</th>
                                            <th>Jugador</th>
                                            <th style="width:100px;text-align:center;"><strong>Puntos</strong></th>
                                            <th style="width:100px;text-align:center;">Victorias</th>
                                            <th>Desglose</th>
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
                            <div id="loading-damas" class="text-center py-3">
                                <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                                <p class="text-muted mt-2 mb-0">Cargando...</p>
                            </div>
                            <div id="ranking-damas" style="display:none;">
                                <table class="table table-sm table-hover mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width:60px;text-align:center;">Pos</th>
                                            <th style="width:60px;text-align:center;">Cat</th>
                                            <th>Jugador</th>
                                            <th style="width:100px;text-align:center;"><strong>Puntos</strong></th>
                                            <th style="width:100px;text-align:center;">Victorias</th>
                                            <th>Desglose</th>
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

                    var catLabel = r.categoria.toLowerCase();
                    var catBadge = '';
                    if(catLabel.includes('1')) {
                        catBadge = '<span class="badge badge-primary" style="font-size:11px;">1ra</span>';
                    } else if(catLabel.includes('2')) {
                        catBadge = '<span class="badge badge-info" style="font-size:11px;">2da</span>';
                    } else if(catLabel.includes('3')) {
                        catBadge = '<span class="badge badge-secondary" style="font-size:11px;">3era</span>';
                    }

                    var categorias_html = '<div style="display:flex; flex-wrap:wrap; gap:6px;">';
                    if(desglose[r.id]) {
                        desglose[r.id].forEach(function(d){
                            var cat_label = '';
                            var cat_color = 'primary';
                            if(d.categoria.toLowerCase().includes('1')) {
                                cat_label = '1ra';
                                cat_color = 'primary';
                            } else if(d.categoria.toLowerCase().includes('2')) {
                                cat_label = '2da';
                                cat_color = 'info';
                            } else if(d.categoria.toLowerCase().includes('3')) {
                                cat_label = '3era';
                                cat_color = 'secondary';
                            }
                            categorias_html += '<span class="badge badge-'+cat_color+'" style="font-size:11px;">'
                                + cat_label + ': ' + d.victorias_categoria + 'v = <strong>' + d.puntos_categoria + 'pts</strong></span>';
                        });
                    }
                    categorias_html += '</div>';

                    tbody += '<tr>';
                    tbody += '<td style="text-align:center; font-weight:bold;">' + medal + posicion + '</td>';
                    tbody += '<td style="text-align:center; min-width:40px;">' + catBadge + '</td>';
                    tbody += '<td style="text-transform:capitalize;">' + r.name.toLowerCase() + '</td>';
                    tbody += '<td style="text-align:center;"><strong style="font-size:16px; color:#2c3e50;">' + (r.puntos || 0) + '</strong></td>';
                    tbody += '<td style="text-align:center;">' + (r.victorias || 0) + '</td>';
                    tbody += '<td>' + categorias_html + '</td>';
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
