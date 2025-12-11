# JumpNote - Private Dashboard

Eine private Notiz- und Organisations-Website (Dashboard) mit PHP/Composer und MySQL, geschützt durch Discord OAuth2-Authentifizierung.

## Funktionen

- **Sichere Authentifizierung**: Zugriff nur über Discord OAuth2 für einen bestimmten Benutzer
- **Dashboard**: Startseite mit Suchleiste und anpassbaren Links
- **Shortlinks-Seite**: Verwaltung von Links, Headern und Ordnern in einer strukturierten Ansicht
- **Widget-Framework**: Platzhalter für zukünftige Widgets (Wetter, Kalender, etc.)

## Technologie-Stack

- **Backend**: PHP mit Composer
- **Datenbank**: MySQL
- **Frontend**: HTML, CSS, JavaScript mit Lucide Icons
- **Authentifizierung**: Discord OAuth2

## Projektstruktur

```
src/
├── assets/
│   ├── css/
│   ├── js/
│   └── img/
├── controllers/
├── includes/
├── models/
├── public/
│   ├── index.php (Dashboard)
│   ├── login.php (Authentifizierung)
│   ├── logout.php (Abmeldung)
│   └── shortlinks.php (Shortlinks-Seite)
├── views/
└── .env (Konfigurationsdatei)
```

## Installation

1. **Voraussetzungen**:

   - PHP 8.0 oder höher
   - MySQL-Datenbank
   - Composer (wird benötigt, sobald verfügbar)
   - Nginx Webserver
   - Certbot für Let's Encrypt

2. **Datenbank einrichten**:

   - Führen Sie das `setup.sql`-Skript aus, um die erforderlichen Tabellen zu erstellen

3. **Konfiguration**:

   - Bearbeiten Sie die `.env`-Datei in `src/` mit Ihren Discord OAuth2-Credentials und Datenbankzugangsdaten

4. **Webserver konfigurieren**:

   - Kopieren Sie die `nginx.conf` Datei in `/etc/nginx/sites-available/jumpnote`
   - Erstellen Sie einen Symlink: `sudo ln -s /etc/nginx/sites-available/jumpnote /etc/nginx/sites-enabled/`
   - Erstellen Sie die benötigten Verzeichnisse:
     - `sudo mkdir -p /var/www/jumpnote`
     - `sudo mkdir -p /var/www/certbot`
   - Kopieren Sie Ihre Anwendungsdateien in `/var/www/jumpnote`
   - Setzen Sie die richtigen Berechtigungen: `sudo chown -R www-data:www-data /var/www/jumpnote`
   - Testen Sie die nginx-Konfiguration: `sudo nginx -t`
   - Laden Sie nginx neu: `sudo systemctl reload nginx`

5. **Let's Encrypt SSL-Zertifikat einrichten**:

   - Installieren Sie Certbot: `sudo apt install certbot`
   - Fordern Sie ein Zertifikat an: `sudo certbot certonly --webroot -w /var/www/certbot -d jumpnote.jumpstone4477.de`
   - Automatisieren Sie die Erneuerung durch Hinzufügen eines Cron-Jobs:
     - `sudo crontab -e`
     - Fügen Sie hinzu: `0 12 * * * /bin/bash /var/www/jumpnote/letsencrypt-renew.sh`

6. **Systemd-Service einrichten (optional)**:
   - Kopieren Sie die `jumpnote.service` Datei nach `/etc/systemd/system/`
   - Aktivieren Sie den Service: `sudo systemctl enable jumpnote.service`
   - Starten Sie den Service: `sudo systemctl start jumpnote.service`

## Verwendung

1. Greifen Sie auf die Website zu, um zur Discord-Anmeldung weitergeleitet zu werden
2. Nach erfolgreicher Anmeldung gelangen Sie zum Dashboard
3. Verwenden Sie die "Bearbeiten"-Schaltfläche, um Links anzupassen
4. Navigieren Sie zur Shortlinks-Seite, um Links, Header und Ordner zu verwalten

## Discord OAuth2 Konfiguration

1. Erstellen Sie eine neue Anwendung im [Discord Developer Portal](https://discord.com/developers/applications)
2. Fügen Sie eine OAuth2-Umleitung hinzu: `http://IHRE_DOMAIN/login.php`
3. Notieren Sie sich die Client-ID und das Client-Secret
4. Tragen Sie diese Werte in die `.env`-Datei ein
5. Geben Sie Ihre Discord User ID in der `.env`-Datei an, um den exklusiven Zugriff festzulegen

## Datenbankschema

Das `setup.sql`-Skript erstellt zwei Haupttabellen:

1. `homepage_links`: Speichert benutzerdefinierte Links für das Dashboard
2. `shortlink_elements`: Speichert alle Elemente der Shortlinks-Seite (Links, Header, Ordner)

## Geplante Funktionen

- Drag-and-Drop-Reihenfolge für Dashboard-Links
- Vollständige CRUD-Funktionalität für Shortlinks-Elemente
- Erweiterte Widget-Unterstützung
- Dark/Light-Theme-Umschaltung

## Lizenz

Dieses Projekt ist proprietär und für den persönlichen Gebrauch durch den Eigentümer vorgesehen.
