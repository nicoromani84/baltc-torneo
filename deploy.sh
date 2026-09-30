#!/bin/bash

# SFTP deployment script
SFTP_HOST="ftp.baltc.net"
SFTP_USER="baltc"
SFTP_PASS="Baltc2020@"
REMOTE_PATH="/public_html/torneo"

FILES=(
    "application/views/web/reserva.php"
    "application/views/web/inscripto.php"
    "application/views/web/menu.php"
)

echo "Deploying to production via SFTP..."

for file in "${FILES[@]}"; do
    remote_file="$REMOTE_PATH/$file"
    
    # Use ftp command via a here-document
    ftp -u -n "$SFTP_HOST" << FTPSCRIPT
user $SFTP_USER $SFTP_PASS
lcd $(dirname "$file")
cd $(dirname "$remote_file")
put $(basename "$file")
quit
FTPSCRIPT
    
    if [ $? -eq 0 ]; then
        echo "✅ $file"
    else
        echo "⚠️  $file - error al copiar"
    fi
done

echo "✅ Deployment completed"
