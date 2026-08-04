<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="page-content">
    <div class="content-inner">
        <div style="padding:20px 24px;background:#fff;border-bottom:1px solid #dee2e6;">
            <h2 style="margin:0;font-size:20px;">Registro de Accesos</h2>
        </div>
        <div style="padding:15px 20px;background:#f8f9fa;border-bottom:1px solid #dee2e6;">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" href="#" data-filter="todos" style="cursor:pointer;">Todos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-filter="no-inscriptos" style="cursor:pointer;">No Inscriptos</a>
                </li>
            </ul>
        </div>
        <section id="reservas">
            <div class="container-fluid" style="padding:20px;">
                <table class="table table-hover table-sm" id="logsTable">
                    <thead class="thead-light">
                        <tr>
                            <th>DNI</th>
                            <th>Nombre</th>
                            <th>Dobles</th>
                            <th>Fecha/Hora Entrada</th>
                            <th>Fecha/Hora Salida</th>
                            <th>Tiempo Conectado</th>
                            <th>IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($logs)): ?>
                            <?php foreach($logs as $log): ?>
                            <tr>
                                <td><strong><?=$log->dni?></strong></td>
                                <td style="text-transform:capitalize;"><?=strtolower($log->name)?></td>
                                <td>
                                    <?php if($log->is_inscripto_dobles == 'YES'): ?>
                                        <span class="badge badge-success">✓ Dobles</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><?=$log->login_time?></td>
                                <td><?=$log->logout_time ? $log->logout_time : '<span style="color:#999;">Aún conectado</span>'?></td>
                                <td>
                                    <?php
                                    if($log->logout_time) {
                                        $login = new DateTime($log->login_time);
                                        $logout = new DateTime($log->logout_time);
                                        $interval = $login->diff($logout);
                                        echo $interval->format('%H:%I:%S');
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </td>
                                <td><small style="color:#999;"><?=$log->ip_address?></small></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">No hay registros de acceso.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>

<script type="text/javascript">
$(function() {
    // Agregar función de búsqueda personalizada ANTES de inicializar DataTables
    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        var filterValue = $('.nav-link.active').data('filter');
        var columnContent = data[2]; // Columna "Dobles"

        if (filterValue === 'todos') {
            return true;
        } else if (filterValue === 'no-inscriptos') {
            // Busca por "-" que es el contenido de la celda para NO inscriptos
            return columnContent.trim() === '-';
        }
        return true;
    });

    // Inicializar DataTables
    var table = $('#logsTable').DataTable({
        dom: 'ltp',
        order: [[ 3, "DESC" ]],
        pageLength: 50
    });

    // Manejar clicks en las pestañas
    $('.nav-link').on('click', function(e) {
        e.preventDefault();
        console.log('Filtro seleccionado:', $(this).data('filter'));

        // Actualizar clase activa
        $('.nav-link').removeClass('active');
        $(this).addClass('active');

        // Redibujar tabla con filtro
        table.draw();
    });
});
</script>

<style>
table tbody tr:hover {
    background-color: #f9f9f9;
}
</style>
