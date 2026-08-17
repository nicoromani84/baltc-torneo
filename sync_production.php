<?php
// Script de sincronización a producción
// Ejecutar: php sync_production.php desde la raíz del proyecto

$files = [
    'application/models/Administrator.php',
    'application/controllers/Menu.php',
    'application/models/Partido_model.php',
    'application/config/routes.php',
    'application/controllers/Admin.php'
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
