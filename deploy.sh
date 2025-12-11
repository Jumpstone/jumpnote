#!/bin/bash

# JumpNote Deployment Script

# === Configuration ===
WEBROOT="/var/www/jumpnote"   # <-- individuell anpassbar
CERTBOT_ROOT="/var/www/certbot"
NGINX_CONF="/etc/nginx/sites-available/jumpnote"

# === Colors ===
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# === Logging ===
log()  { echo -e "${GREEN}[INFO]${NC} $1"; }
warn() { echo -e "${YELLOW}[WARN]${NC} $1"; }
error(){ echo -e "${RED}[ERROR]${NC} $1"; }

# === Root Check ===
if [[ $EUID -eq 0 ]]; then
   error "This script should not be run as root"
   exit 1
fi

# === Prerequisites ===
log "Checking prerequisites..."

for cmd in nginx php mysql certbot; do
    if ! command -v "$cmd" >/dev/null 2>&1; then
        error "$cmd is not installed. Please install it first."
        exit 1
    fi
done
log "All prerequisites met."

# === Directories ===
log "Creating directories..."
sudo mkdir -p "$WEBROOT"
sudo mkdir -p "$CERTBOT_ROOT"
sudo mkdir -p /var/log/nginx

# === Copy application files ===
log "Copying application files..."
sudo cp -r src/* "$WEBROOT/"
sudo cp nginx.conf "$NGINX_CONF"
sudo ln -sf "$NGINX_CONF" /etc/nginx/sites-enabled/

# === Permissions ===
log "Setting permissions..."
sudo chown -R www-data:www-data "$WEBROOT"
sudo chmod +x letsencrypt-renew.sh
sudo cp letsencrypt-renew.sh "$WEBROOT/"

# === Nginx Test ===
log "Testing nginx configuration..."
if ! sudo nginx -t; then
    error "Nginx configuration test failed"
    exit 1
fi

# === Reload ===
log "Reloading nginx..."
sudo systemctl reload nginx

# === Cronjob ===
log "Setting up Let's Encrypt renewal cron job..."
(
    crontab -l 2>/dev/null
    echo "0 12 * * * /bin/bash $WEBROOT/letsencrypt-renew.sh"
) | crontab -

log "Deployment completed successfully!"
log "Next steps:"
log "1. Update the .env file in $WEBROOT"
log "2. Run the database setup script"
log "3. Obtain SSL certificate with: sudo certbot certonly --webroot -w $CERTBOT_ROOT -d jumpnote.jumpstone4477.de"
log "4. Reload nginx: sudo systemctl reload nginx"
log "5. Restart PHP-FPM: sudo systemctl restart php8.2-fpm"
