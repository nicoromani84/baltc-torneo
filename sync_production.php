<?php
// Script de sincronización a producción
// Ejecutar: php sync_production.php desde la raíz del proyecto

$files = [
    'application/models/Administrator.php',
    'application/models/Partido_model.php',
    'application/models/Protect.php',
    'application/models/Common.php',
    'application/controllers/Menu.php',
    'application/controllers/Admin.php',
    'application/controllers/Mipartido.php',
    'application/controllers/Draws.php',
    'application/config/routes.php',
    'application/config/autoload.php',
    'application/views/admin/partidos.php',
    'application/views/web/draws.php',
    'application/views/web/partidos.php',
    'sync_production.php'
];

// SFTP credentials
$sftp_host = 'ftp.baltc.net';
$sftp_user = 'baltc';
$sftp_pass = 'Baltc2020@';
$remote_path = '/public_html/torneo';

echo "Conectando a SFTP: $sftp_host\n";

// Crear contexto para permitir sobrescritura
$ctx = stream_context_create(['ftp' => ['overwrite' => true]]);

foreach ($files as $file) {
    $local = $file;
    $remote = "ftp://$sftp_user:$sftp_pass@$sftp_host$remote_path/$file";

    if (!file_exists($local)) {
        echo "❌ $file - no existe\n";
        continue;
    }

    if (copy($local, $remote, $ctx)) {
        echo "✅ $file\n";
    } else {
        echo "⚠️  $file - error al copiar\n";
    }
}

echo "\n✅ Sincronización completada\n";
?>
