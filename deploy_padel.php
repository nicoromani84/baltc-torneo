<?php
// Script para crear carpeta de pádel y subir archivos
$sftp_host = 'ftp.baltc.net';
$sftp_user = 'baltc';
$sftp_pass = 'Baltc2020@';
$remote_path = '/public_html/torneo';

echo "Creando estructura de pádel en producción...\n";

// Crear contexto FTP
$ctx = stream_context_create(['ftp' => ['overwrite' => true]]);

$files = [
    'application/views/web/padel/reserva.php',
    'application/views/web/padel/inscripto.php',
    'application/views/web/padel/dashboard.php'
];

foreach ($files as $file) {
    $local = $file;
    $remote = "ftp://$sftp_user:$sftp_pass@$sftp_host$remote_path/$file";
    $remote_dir = dirname("ftp://$sftp_user:$sftp_pass@$sftp_host$remote_path/$file");

    if (!file_exists($local)) {
        echo "❌ $file - no existe localmente\n";
        continue;
    }

    // Intentar crear el archivo (FTP creará los directorios si es necesario)
    if (copy($local, $remote, $ctx)) {
        echo "✅ $file\n";
    } else {
        echo "⚠️  $file - error al copiar\n";
    }
}

echo "\n✅ Estructura de pádel deployed\n";
?>
