<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="page-content">
    <div class="content-inner">
        <div style="padding:20px 24px;background:#fff;border-bottom:1px solid #dee2e6;">
            <h2 style="margin:0;font-size:20px;"><i class="fas fa-envelope"></i> Envío de Mails</h2>
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
                                    <label class="form-check-label" for="tipo-todos"><strong>Todos los inscriptos</strong></label>
                                </div>

                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="tipo" id="tipo-categoria" value="categoria">
                                    <label class="form-check-label" for="tipo-categoria"><strong>Por categoría</strong></label>
                                </div>
                                <div id="filtro-categoria-wrap" style="display:none; padding-left:24px; margin-bottom:12px;">
                                    <select id="mail-categoria" class="form-control form-control-sm">
                                        <option value="">Seleccioná una categoría...</option>
                                        <?php foreach($categories as $cat): ?>
                                        <option value="<?=$cat->id?>"><?=htmlspecialchars($cat->name)?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="tipo" id="tipo-individual" value="individual">
                                    <label class="form-check-label" for="tipo-individual"><strong>Individual</strong></label>
                                </div>
                                <div id="filtro-individual-wrap" style="display:none; padding-left:24px;">
                                    <div style="position:relative;">
                                        <input type="text" id="buscar-mail-partner" class="form-control form-control-sm" placeholder="Nombre o DNI..." autocomplete="off">
                                        <div id="ac-mail-partner" style="display:none; position:absolute; top:100%; left:0; right:0; background:#fff; border:1px solid #ced4da; border-top:none; border-radius:0 0 4px 4px; z-index:1000; max-height:220px; overflow-y:auto; box-shadow:0 4px 8px rgba(0,0,0,0.1);"></div>
                                    </div>
                                    <input type="hidden" id="mail-partner-id">
                                    <div id="mail-partner-seleccionado" style="display:none; margin-top:8px;" class="alert alert-success py-2 px-3 mb-0">
                                        <i class="fas fa-user-check"></i> <strong id="mail-partner-nombre"></strong>
                                        <button class="btn btn-xs btn-outline-secondary ml-2" id="btn-cambiar-mail-partner">Cambiar</button>
                                    </div>
                                </div>

                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="tipo" id="tipo-2ndchance" value="2ndchance">
                                    <label class="form-check-label" for="tipo-2ndchance"><strong>Inscriptos en 2nd Chance</strong></label>
                                </div>
                            </div>

                            <button class="btn btn-outline-primary btn-sm" id="btn-preview-destinatarios">
                                <i class="fas fa-search"></i> Ver destinatarios
                            </button>

                            <!-- Lista destinatarios (colapsable) -->
                            <div id="panel-destinatarios" style="display:none; margin-top:16px;">
                                <div style="font-size:13px; color:#555; margin-bottom:12px; display:flex; justify-content:space-between; align-items:center; gap:8px; flex-wrap:wrap;">
                                    <span><i class="fas fa-users"></i> <strong><span id="total-destinatarios">0</span> destinatarios</strong></span>
                                    <div style="display:flex; gap:6px;">
                                        <button class="btn btn-xs btn-outline-warning" id="btn-guardar-pendientes" style="display:none;" title="Guardar lista para identificar y borrar después">
                                            <i class="fas fa-save"></i> Guardar pendientes
                                        </button>
                                        <button class="btn btn-xs btn-outline-info" id="btn-descargar-destinatarios" style="display:none;">
                                            <i class="fas fa-download"></i> Descargar Excel
                                        </button>
                                    </div>
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

                <!-- 2. Template -->
                <div class="col-md-8 mb-4">
                    <div class="card" style="height:100%;">
                        <div class="card-header bg-light">
                            <strong><i class="fas fa-envelope-open-text mr-1"></i> 2. Elegir mail</strong>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label class="font-weight-bold">Template</label>
                                <select id="mail-template" class="form-control">
                                    <option value="">— Seleccioná un mail —</option>
                                </select>
                            </div>
                            <div id="template-asunto-wrap" style="display:none;" class="form-group mb-3">
                                <label class="font-weight-bold">Asunto</label>
                                <input type="text" id="mail-asunto" class="form-control" readonly style="background:#f8f9fa;">
                            </div>
                            <div id="template-preview-wrap" style="display:none;">
                                <label class="font-weight-bold">Vista previa</label>
                                <iframe id="email-preview-iframe" frameborder="0" style="width:100%; height:380px; border:1px solid #dee2e6; border-radius:6px; background:#f0f2f5;"></iframe>
                            </div>
                            <div id="template-placeholder" class="text-center text-muted py-5">
                                <i class="fas fa-envelope fa-3x mb-3" style="opacity:0.2; display:block;"></i>
                                Seleccioná un mail para ver la vista previa
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Enviar -->
            <div class="card mb-3">
                <div class="card-body d-flex align-items-center justify-content-between flex-wrap" style="gap:12px;">
                    <div id="enviar-resumen" style="font-size:14px; color:#555;">
                        <span id="resumen-dest"><i class="fas fa-users mr-1"></i> <em>Sin destinatarios seleccionados</em></span>
                        &nbsp;·&nbsp;
                        <span id="resumen-template"><i class="fas fa-envelope mr-1"></i> <em>Sin mail seleccionado</em></span>
                    </div>
                    <button class="btn btn-success btn-lg" id="btn-enviar-notificacion">
                        <i class="fas fa-paper-plane mr-1"></i> Enviar
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

    // Cargar templates
    $.ajax({
        url: adminurl + '/getEmailTemplates',
        type: 'POST',
        headers: {'X-Auth-Token': token},
        success: function(res){
            if(!res.action || !res.templates) return;
            res.templates.forEach(function(t){
                $('#mail-template').append('<option value="'+t.id+'">'+t.nombre+'</option>');
            });
        }
    });

    // Seleccionar template → cargar preview
    $('#mail-template').on('change', function(){
        var id = $(this).val();
        if(!id){
            $('#template-preview-wrap, #template-asunto-wrap').hide();
            $('#template-placeholder').show();
            $('#resumen-template').html('<i class="fas fa-envelope mr-1"></i> <em>Sin mail seleccionado</em>');
            return;
        }
        $.ajax({
            url: adminurl + '/previewEmailTemplate',
            type: 'POST',
            data: { template_id: id },
            headers: {'X-Auth-Token': token},
            success: function(res){
                if(!res.action) return;
                $('#mail-asunto').val(res.asunto);
                $('#email-preview-iframe')[0].srcdoc = res.html;
                $('#template-asunto-wrap, #template-preview-wrap').show();
                $('#template-placeholder').hide();
                $('#resumen-template').html('<i class="fas fa-envelope mr-1"></i> <strong>'+$('#mail-template option:selected').text()+'</strong>');
            }
        });
    });

    // Toggle filtros
    $('input[name="tipo"]').on('change', function(){
        var tipo = $(this).val();
        $('#filtro-categoria-wrap').toggle(tipo === 'categoria');
        $('#filtro-individual-wrap').toggle(tipo === 'individual');
        $('#panel-destinatarios').hide();
        _totalDestinatarios = 0;
        $('#total-destinatarios,#btn-total').text(0);
        $('#resumen-dest').html('<i class="fas fa-users mr-1"></i> <em>Sin destinatarios seleccionados</em>');
        $('#mail-resultado').hide();
        // Auto-cargar destinatarios para 2nd chance
        if(tipo === '2ndchance'){
            $('#btn-preview-destinatarios').trigger('click');
        }
    });

    // Autocomplete individual
    var _acTimer = null;
    $('#buscar-mail-partner').on('input', function(){
        var q = $.trim($(this).val());
        clearTimeout(_acTimer);
        if(q.length < 2){ $('#ac-mail-partner').hide(); return; }
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
                            html += '<div class="ac-mail-item" style="padding:9px 14px;cursor:pointer;border-bottom:1px solid #f0f0f0;display:flex;align-items:center;justify-content:space-between;" data-id="'+p.id+'" data-name="'+p.name+'">'
                                  + '<span style="font-size:13px;text-transform:capitalize;font-weight:600;">'+p.name.toLowerCase()+'</span>'
                                  + '<span style="font-size:12px;color:#888;">'+p.dni+badge+'</span>'
                                  + '</div>';
                        });
                    }
                    $('#ac-mail-partner').html(html).show();
                }
            });
        }, 250);
    });
    $(document).on('click', function(e){
        if(!$(e.target).closest('#buscar-mail-partner,#ac-mail-partner').length) $('#ac-mail-partner').hide();
    });
    $(document).on('mouseenter','.ac-mail-item',function(){ $(this).css('background','#f0f4ff'); });
    $(document).on('mouseleave','.ac-mail-item',function(){ $(this).css('background','#fff'); });
    $(document).on('click','.ac-mail-item',function(){
        var d = $(this).data();
        $('#mail-partner-id').val(d.id);
        $('#mail-partner-nombre').text(d.name.toLowerCase());
        $('#buscar-mail-partner').val('');
        $('#ac-mail-partner').hide();
        $('#mail-partner-seleccionado').show();
    });
    $('#btn-cambiar-mail-partner').on('click', function(){
        $('#mail-partner-id').val('');
        $('#mail-partner-seleccionado').hide();
        $('#buscar-mail-partner').val('').focus();
    });

    // Ver destinatarios
    $('#btn-preview-destinatarios').on('click', function(){
        var tipo       = $('input[name="tipo"]:checked').val();
        var category   = $('#mail-categoria').val();
        var partner_id = $('#mail-partner-id').val();
        if(tipo === 'categoria' && !category){ alert('Seleccioná una categoría.'); return; }
        if(tipo === 'individual' && !partner_id){ alert('Seleccioná un jugador.'); return; }
        $.ajax({
            url: adminurl + '/getDestinatariosNotificacion',
            type: 'POST',
            data: { tipo: tipo, category: category, partner_id: partner_id },
            headers: {'X-Auth-Token': token},
            success: function(res){
                if(!res.action) return;
                _totalDestinatarios = res.total;
                _destinatarios = res.destinatarios || [];
                _tipoActual = tipo;
                $('#total-destinatarios').text(res.total);
                var tbody = '';
                if(!res.total){
                    tbody = '<tr><td class="text-center text-muted py-2">Sin destinatarios con email válido.</td></tr>';
                } else {
                    res.destinatarios.forEach(function(d){
                        tbody += '<tr><td style="text-transform:capitalize;font-size:13px;">'+d.name.toLowerCase()+'</td>'
                               + '<td style="font-size:12px;color:#888;">'+d.email+'</td></tr>';
                    });
                }
                $('#tabla-destinatarios').html(tbody);
                $('#panel-destinatarios').show();
                var es2ndchance = tipo === '2ndchance';
                if(es2ndchance){
                    $('#btn-descargar-destinatarios').show();
                    $('#btn-guardar-pendientes').show();
                } else {
                    $('#btn-descargar-destinatarios').hide();
                    $('#btn-guardar-pendientes').hide();
                }
                $('#resumen-dest').html('<i class="fas fa-users mr-1"></i> <strong>'+res.total+' destinatario(s)</strong>');
                $('#mail-resultado').hide();
            }
        });
    });

    // Guardar pendientes de envío para 2nd chance
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
                        .html('<i class="fas fa-check-circle"></i> <strong>¡Guardado!</strong> Se guardaron '+res.total+' destinatarios como pendientes. Ahora podés borrarlos de las categorías en el backend.').show();
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

    // Descargar lista de destinatarios en Excel
    $('#btn-descargar-destinatarios').on('click', function(){
        if(!_destinatarios.length){ alert('Sin destinatarios.'); return; }
        var html = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
        html += '<head><meta charset="UTF-8"></head><body>';
        html += '<table border="1">';
        html += '<tr><th>Nombre</th><th>Email</th><th>Categoría</th></tr>';
        _destinatarios.forEach(function(d){
            html += '<tr>';
            html += '<td>'+d.name.toLowerCase()+'</td>';
            html += '<td>'+d.email+'</td>';
            html += '<td>'+(d.categoria||'')+'</td>';
            html += '</tr>';
        });
        html += '</table></body></html>';
        var blob = new Blob([html], {type: 'application/vnd.ms-excel;charset=UTF-8'});
        var url = window.URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url;
        a.download = '2ndchance_inscriptos_' + new Date().toISOString().split('T')[0] + '.xls';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
    });

    // Enviar
    $('#btn-enviar-notificacion').on('click', function(){
        var template_id = $('#mail-template').val();
        var asunto      = $('#mail-asunto').val();
        if(!template_id){ alert('Seleccioná un mail en el paso 2.'); return; }
        if(!_totalDestinatarios){ alert('Seleccioná destinatarios en el paso 1 y hacé click en "Ver destinatarios".'); return; }
        if(!confirm('¿Enviar "'+asunto+'" a '+_totalDestinatarios+' destinatario(s)?')) return;

        var tipo       = $('input[name="tipo"]:checked').val();
        var category   = $('#mail-categoria').val();
        var partner_id = $('#mail-partner-id').val();
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Enviando...');

        $.ajax({
            url: adminurl + '/enviarNotificacion',
            type: 'POST',
            data: { tipo: tipo, category: category, partner_id: partner_id, template_id: template_id },
            headers: {'X-Auth-Token': token},
            success: function(res){
                $btn.prop('disabled', false).html('<i class="fas fa-paper-plane mr-1"></i> Enviar');
                if(res.action){
                    $('#mail-resultado').removeClass('alert-danger').addClass('alert-success')
                        .html('<i class="fas fa-check-circle"></i> <strong>¡Listo!</strong> Se enviaron '+res.enviados+' correo(s) correctamente.').show();
                } else {
                    $('#mail-resultado').removeClass('alert-success').addClass('alert-danger')
                        .html('<i class="fas fa-times-circle"></i> Error: '+(res.msg||'No se pudieron enviar.')).show();
                }
                $('html,body').animate({scrollTop: $('#mail-resultado').offset().top - 80}, 300);
            },
            error: function(){
                $btn.prop('disabled', false).html('<i class="fas fa-paper-plane mr-1"></i> Enviar');
                $('#mail-resultado').removeClass('alert-success').addClass('alert-danger')
                    .html('<i class="fas fa-times-circle"></i> Error de conexión.').show();
            }
        });
    });
});
</script>
