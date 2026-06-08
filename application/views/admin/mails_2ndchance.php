<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="page-content">
    <div class="content-inner">
        <div style="padding:20px 24px;background:#fff;border-bottom:1px solid #dee2e6;">
            <h2 style="margin:0;font-size:20px;"><i class="fas fa-envelope"></i> Invitación 2nd Chance</h2>
        </div>

        <div style="padding:20px;">
            <div class="row">

                <!-- 1. Destinatarios -->
                <div class="col-md-4 mb-4">
                    <div class="card" style="height:100%;">
                        <div class="card-header bg-light">
                            <strong><i class="fas fa-users mr-1"></i> 1. Destinatarios</strong>
                        </div>
                        <div class="card-body">
                            <div class="form-group mb-3">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="tipo" id="tipo-todos" value="todos" checked>
                                    <label class="form-check-label" for="tipo-todos"><strong>Todos inscriptos en 2nd Chance</strong></label>
                                </div>

                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="tipo" id="tipo-individual" value="individual">
                                    <label class="form-check-label" for="tipo-individual"><strong>Individual</strong></label>
                                </div>
                                <div id="filtro-individual-wrap" style="display:none; padding-left:24px;">
                                    <div style="position:relative;">
                                        <input type="text" id="buscar-partner" class="form-control form-control-sm" placeholder="Nombre o DNI..." autocomplete="off">
                                        <div id="ac-partner" style="display:none; position:absolute; top:100%; left:0; right:0; background:#fff; border:1px solid #ced4da; border-top:none; border-radius:0 0 4px 4px; z-index:1000; max-height:220px; overflow-y:auto; box-shadow:0 4px 8px rgba(0,0,0,0.1);"></div>
                                    </div>
                                    <input type="hidden" id="partner-id">
                                    <input type="hidden" id="partner-email">
                                    <div id="partner-seleccionado" style="display:none; margin-top:8px;" class="alert alert-success py-2 px-3 mb-0">
                                        <i class="fas fa-user-check"></i> <strong id="partner-nombre"></strong>
                                        <button class="btn btn-xs btn-outline-secondary ml-2" id="btn-cambiar-partner">Cambiar</button>
                                    </div>
                                </div>
                            </div>

                            <div style="display:flex; gap:8px;">
                                <button class="btn btn-outline-primary btn-sm" id="btn-preview-destinatarios">
                                    <i class="fas fa-search"></i> Ver destinatarios
                                </button>
                                <button class="btn btn-outline-info btn-sm" id="btn-ver-pendientes">
                                    <i class="fas fa-list"></i> Ver pendientes guardados
                                </button>
                            </div>

                            <!-- Lista destinatarios (colapsable) -->
                            <div id="panel-destinatarios" style="display:none; margin-top:16px;">
                                <div style="font-size:13px; color:#555; margin-bottom:12px; display:flex; justify-content:space-between; align-items:center; gap:8px;">
                                    <span><i class="fas fa-users"></i> <strong><span id="total-destinatarios">0</span> destinatarios</strong></span>
                                    <button class="btn btn-xs btn-outline-warning" id="btn-guardar-pendientes" title="Guardar lista antes de borrar de categorías">
                                        <i class="fas fa-save"></i> Guardar pendientes
                                    </button>
                                </div>
                                <div style="max-height:200px; overflow-y:auto; border:1px solid #dee2e6; border-radius:4px;">
                                    <table class="table table-sm mb-0">
                                        <tbody id="tabla-destinatarios"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Preview -->
                <div class="col-md-8 mb-4">
                    <div class="card" style="height:100%;">
                        <div class="card-header bg-light">
                            <strong><i class="fas fa-envelope-open-text mr-1"></i> 2. Vista previa del mail</strong>
                        </div>
                        <div class="card-body">
                            <div class="form-group mb-3">
                                <label class="font-weight-bold">Asunto</label>
                                <input type="text" id="mail-asunto" class="form-control" readonly style="background:#f8f9fa;" value="¿Querés seguir en el torneo? — 2nd Chance BALTC">
                            </div>
                            <label class="font-weight-bold">Vista previa</label>
                            <iframe id="email-preview-iframe" frameborder="0" style="width:100%; height:380px; border:1px solid #dee2e6; border-radius:6px; background:#f0f2f5;"></iframe>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Enviar -->
            <div class="card mb-3">
                <div class="card-body d-flex align-items-center justify-content-between flex-wrap" style="gap:12px;">
                    <div id="enviar-resumen" style="font-size:14px; color:#555;">
                        <span id="resumen-dest"><i class="fas fa-users mr-1"></i> <em>Sin destinatarios seleccionados</em></span>
                    </div>
                    <button class="btn btn-success btn-lg" id="btn-enviar-invitacion">
                        <i class="fas fa-paper-plane mr-1"></i> Enviar Invitación
                    </button>
                </div>
            </div>

            <!-- Resultado -->
            <div id="mail-resultado" class="alert mb-3" style="display:none;"></div>
        </div>
    </div>
</div>

<script>
const adminurl = '<?=base_url('admin')?>';
const token    = '<?=$token?>';
var _totalDestinatarios = 0;
var _destinatarios = [];
var _tipoActual = '';

$(function(){

    // Cargar preview
    cargarPreview();

    function cargarPreview(){
        $.ajax({
            url: adminurl + '/preview2ndChanceEmail',
            type: 'POST',
            headers: {'X-Auth-Token': token},
            success: function(res){
                if(res.action){
                    $('#email-preview-iframe')[0].srcdoc = res.html;
                }
            }
        });
    }

    // Toggle filtros
    $('input[name="tipo"]').on('change', function(){
        var tipo = $(this).val();
        $('#filtro-individual-wrap').toggle(tipo === 'individual');
        $('#panel-destinatarios').hide();
        _totalDestinatarios = 0;
        $('#total-destinatarios').text(0);
        $('#resumen-dest').html('<i class="fas fa-users mr-1"></i> <em>Sin destinatarios seleccionados</em>');
        $('#mail-resultado').hide();
    });

    // Autocomplete individual
    var _acTimer = null;
    $('#buscar-partner').on('input', function(){
        var q = $.trim($(this).val());
        clearTimeout(_acTimer);
        if(q.length < 2){ $('#ac-partner').hide(); return; }
        _acTimer = setTimeout(function(){
            $.ajax({
                url: adminurl + '/buscarPartner',
                type: 'POST',
                data: { q: q },
                headers: {'X-Auth-Token': token},
                success: function(res){
                    var html = '';
                    if(!res.partners || !res.partners.length){
                        html = '<div style="padding:10px 14px;color:#888;font-size:13px;">Sin resultados</div>';
                    } else {
                        res.partners.forEach(function(p){
                            var badge = p.gender === 'M'
                                ? '<span style="background:#1a6fad;color:#fff;border-radius:10px;padding:1px 6px;font-size:10px;font-weight:700;margin-left:5px;">M</span>'
                                : '<span style="background:#ad1a6f;color:#fff;border-radius:10px;padding:1px 6px;font-size:10px;font-weight:700;margin-left:5px;">F</span>';
                            html += '<div class="ac-partner-item" style="padding:9px 14px;cursor:pointer;border-bottom:1px solid #f0f0f0;display:flex;align-items:center;justify-content:space-between;" data-id="'+p.id+'" data-name="'+p.name+'" data-email="'+p.email+'">'
                                  + '<span style="font-size:13px;text-transform:capitalize;font-weight:600;">'+p.name.toLowerCase()+'</span>'
                                  + '<span style="font-size:12px;color:#888;">'+p.dni+badge+'</span>'
                                  + '</div>';
                        });
                    }
                    $('#ac-partner').html(html).show();
                }
            });
        }, 250);
    });
    $(document).on('click', function(e){
        if(!$(e.target).closest('#buscar-partner,#ac-partner').length) $('#ac-partner').hide();
    });
    $(document).on('mouseenter','.ac-partner-item',function(){ $(this).css('background','#f0f4ff'); });
    $(document).on('mouseleave','.ac-partner-item',function(){ $(this).css('background','#fff'); });
    $(document).on('click','.ac-partner-item',function(){
        var d = $(this).data();
        $('#partner-id').val(d.id);
        $('#partner-email').val(d.email);
        $('#partner-nombre').text(d.name.toLowerCase());
        $('#buscar-partner').val('');
        $('#ac-partner').hide();
        $('#partner-seleccionado').show();
    });
    $('#btn-cambiar-partner').on('click', function(){
        $('#partner-id').val('');
        $('#partner-email').val('');
        $('#partner-seleccionado').hide();
        $('#buscar-partner').val('').focus();
    });

    // Ver pendientes guardados
    $('#btn-ver-pendientes').on('click', function(){
        $.ajax({
            url: adminurl + '/verPendientes2ndChance',
            type: 'POST',
            headers: {'X-Auth-Token': token},
            success: function(res){
                if(!res.action || !res.pendientes) {
                    alert('No hay pendientes guardados.');
                    return;
                }
                _totalDestinatarios = res.pendientes.length;
                _destinatarios = res.pendientes;
                $('#total-destinatarios').text(res.pendientes.length);
                var tbody = '';
                res.pendientes.forEach(function(d){
                    tbody += '<tr><td style="text-transform:capitalize;font-size:13px;">'+d.name.toLowerCase()+'</td>'
                           + '<td style="font-size:12px;color:#888;">'+d.email+'</td></tr>';
                });
                $('#tabla-destinatarios').html(tbody);
                $('#panel-destinatarios').show();
                $('#resumen-dest').html('<i class="fas fa-users mr-1"></i> <strong>'+res.pendientes.length+' pendientes guardados</strong>');
            }
        });
    });

    // Ver destinatarios
    $('#btn-preview-destinatarios').on('click', function(){
        var tipo       = $('input[name="tipo"]:checked').val();
        var partner_id = $('#partner-id').val();
        if(tipo === 'individual' && !partner_id){ alert('Seleccioná un jugador.'); return; }

        if(tipo === 'todos'){
            // Obtener de 2nd chance
            $.ajax({
                url: adminurl + '/get2ndChanceInscriptos',
                type: 'POST',
                headers: {'X-Auth-Token': token},
                success: function(res){
                    if(!res.action) return;
                    _totalDestinatarios = res.inscriptos.length;
                    _destinatarios = res.inscriptos.map(function(i){
                        return {id: i.id, name: i.name, email: '', categoria: i.categoria};
                    });
                    mostrarDestinatarios();
                }
            });
        } else if(tipo === 'individual' && partner_id){
            // Usar el jugador ya seleccionado
            var nombre = $('#partner-nombre').text();
            var email = $('#partner-email').val();
            _totalDestinatarios = 1;
            _destinatarios = [{id: partner_id, name: nombre, email: email, categoria: ''}];
            mostrarDestinatarios();
        }
    });

    function mostrarDestinatarios(){
        $('#total-destinatarios').text(_totalDestinatarios);
        var tbody = '';
        if(!_totalDestinatarios){
            tbody = '<tr><td class="text-center text-muted py-2">Sin destinatarios con email válido.</td></tr>';
        } else {
            _destinatarios.forEach(function(d){
                tbody += '<tr><td style="text-transform:capitalize;font-size:13px;">'+d.name.toLowerCase()+'</td>'
                       + '<td style="font-size:12px;color:#888;">'+d.email+'</td></tr>';
            });
        }
        $('#tabla-destinatarios').html(tbody);
        $('#panel-destinatarios').show();
        $('#resumen-dest').html('<i class="fas fa-users mr-1"></i> <strong>'+_totalDestinatarios+' destinatario(s)</strong>');
        $('#mail-resultado').hide();
    }

    // Guardar destinatarios como pendientes
    $('#btn-guardar-pendientes').on('click', function(){
        if(!_destinatarios.length){ alert('Sin destinatarios.'); return; }
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Guardando...');
        $.ajax({
            url: adminurl + '/guardarPendientes2ndChance',
            type: 'POST',
            data: { destinatarios: JSON.stringify(_destinatarios) },
            headers: {'X-Auth-Token': token},
            success: function(res){
                $btn.prop('disabled', false).html('<i class="fas fa-save"></i> Guardar pendientes');
                if(res.action){
                    $('#mail-resultado').removeClass('alert-danger').addClass('alert-success')
                        .html('<i class="fas fa-check-circle"></i> <strong>¡Guardado!</strong> Se guardaron '+res.total+' destinatarios. Ahora podés borrar los jugadores de las categorías sin perder esta lista.').show();
                } else {
                    $('#mail-resultado').removeClass('alert-success').addClass('alert-danger')
                        .html('<i class="fas fa-times-circle"></i> Error: '+(res.msg||'No se pudo guardar.')).show();
                }
                $('html,body').animate({scrollTop: $('#mail-resultado').offset().top - 80}, 300);
            },
            error: function(){
                $btn.prop('disabled', false).html('<i class="fas fa-save"></i> Guardar pendientes');
                $('#mail-resultado').removeClass('alert-success').addClass('alert-danger')
                    .html('<i class="fas fa-times-circle"></i> Error de conexión.').show();
            }
        });
    });

    // Enviar invitación
    $('#btn-enviar-invitacion').on('click', function(){
        if(!_totalDestinatarios){ alert('Seleccioná destinatarios en el paso 1 y hacé click en "Ver destinatarios".'); return; }
        if(!confirm('¿Enviar invitación 2nd Chance a '+_totalDestinatarios+' destinatario(s)?')) return;

        var tipo       = $('input[name="tipo"]:checked').val();
        var partner_id = $('#partner-id').val();
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Enviando...');

        var data = { tipo: tipo };
        if(tipo === 'individual') data.partner_id = partner_id;

        $.ajax({
            url: adminurl + '/enviar2ndChance',
            type: 'POST',
            data: data,
            headers: {'X-Auth-Token': token},
            success: function(res){
                $btn.prop('disabled', false).html('<i class="fas fa-paper-plane mr-1"></i> Enviar Invitación');
                if(res.action){
                    $('#mail-resultado').removeClass('alert-danger').addClass('alert-success')
                        .html('<i class="fas fa-check-circle"></i> <strong>¡Listo!</strong> Se enviaron '+res.enviados+' invitación(es) correctamente.').show();
                } else {
                    $('#mail-resultado').removeClass('alert-success').addClass('alert-danger')
                        .html('<i class="fas fa-times-circle"></i> Error: '+(res.msg||'No se pudieron enviar.')).show();
                }
                $('html,body').animate({scrollTop: $('#mail-resultado').offset().top - 80}, 300);
            },
            error: function(){
                $btn.prop('disabled', false).html('<i class="fas fa-paper-plane mr-1"></i> Enviar Invitación');
                $('#mail-resultado').removeClass('alert-success').addClass('alert-danger')
                    .html('<i class="fas fa-times-circle"></i> Error de conexión.').show();
            }
        });
    });
});
</script>
