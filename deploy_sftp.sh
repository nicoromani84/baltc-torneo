#!/bin/bash

# SFTP deployment using curl
SFTP_HOST="sftp://ftp.baltc.net"
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
    remote_file="$SFTP_HOST$REMOTE_PATH/$file"
    
    curl -u "$SFTP_USER:$SFTP_PASS" -T "$file" "$remote_file" 2>/dev/null
    
    if [ $? -eq 0 ]; then
        echo "✅ $file"
    else
        echo "⚠️  $file - error al copiar"
    fi
done

echo "✅ Deployment completed"
