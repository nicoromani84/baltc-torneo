<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="page-content">
    <div class="content-inner">
        <div style="padding:20px 24px;background:#fff;border-bottom:1px solid #dee2e6;">
            <h2 style="margin:0;font-size:20px;">Registro de Accesos</h2>
        </div>
        <section id="reservas">
            <div class="container-fluid" style="padding:20px;">
                <table class="table table-hover table-sm" id="logsTable">
                    <thead class="thead-light">
                        <tr>
                            <th>DNI</th>
                            <th>Nombre</th>
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
    $('#logsTable').DataTable({
        dom: 'ltp',
        order: [[ 2, "DESC" ]],
        pageLength: 50
    });
});
</script>

<style>
table tbody tr:hover {
    background-color: #f9f9f9;
}
</style>
