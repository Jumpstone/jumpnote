#!/bin/bash

# JumpNote Deployment Script

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Logging function
log() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

warn() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if running as root
if [[ $EUID -eq 0 ]]; then
   error "This script should not be run as root"
   exit 1
fi

# Check prerequisites
log "Checking prerequisites..."

# Check if required commands are available
for cmd in nginx php mysql certbot; do
    if ! command -v $cmd &> /dev/null; then
        error "$cmd is not installed. Please install it first."
        exit 1
    fi
done

log "All prerequisites met."

# Create necessary directories
log "Creating directories..."
sudo mkdir -p /var/www/jumpnote
sudo mkdir -p /var/www/certbot
sudo mkdir -p /var/log/nginx

# Copy application files
log "Copying application files..."
sudo cp -r src/* /var/www/web/jumpnote/
sudo cp nginx.conf /etc/nginx/sites-available/jumpnote
sudo ln -sf /etc/nginx/sites-available/jumpnote /etc/nginx/sites-enabled/

# Set permissions
log "Setting permissions..."
sudo chown -R www-data:www-data /var/www/web/jumpnote
sudo chmod +x letsencrypt-renew.sh
sudo cp letsencrypt-renew.sh /var/www/web/jumpnote/

# Test nginx configuration
log "Testing nginx configuration..."
sudo nginx -t
if [ $? -ne 0 ]; then
    error "Nginx configuration test failed"
    exit 1
fi

# Reload nginx
log "Reloading nginx..."
sudo systemctl reload nginx

# Setup cron job for Let's Encrypt renewal
log "Setting up Let's Encrypt renewal cron job..."
(crontab -l 2>/dev/null; echo "0 12 * * * /bin/bash /var/www/jumpnote/letsencrypt-renew.sh") | crontab -

log "Deployment completed successfully!"
log "Next steps:"
log "1. Update the .env file in /var/www/web/jumpnote/ with your configuration"
log "2. Run the database setup script"
log "3. Obtain SSL certificate with: sudo certbot certonly --webroot -w /var/www/certbot -d jumpnote.jumpstone4477.de"
log "4. Reload nginx: sudo systemctl reload nginx"
log "5. Restart PHP-FPM: sudo systemctl restart php8.2-fpm"