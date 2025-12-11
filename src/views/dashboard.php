<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Neuer Tab</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <div class="grid-bg"></div>
    
    <header class="header-nav">
        <div class="header-content">
            <button id="edit-toggle" class="header-btn">
                <i data-lucide="edit"></i> Bearbeiten
            </button>
            <a href="logout.php" class="header-btn">
                <i data-lucide="log-out"></i> Abmelden
            </a>
        </div>
    </header>

    <div class="container">
        <section class="hero">
            <div class="search-container centered">
                <form id="search-form" action="https://www.google.com/search" method="get" target="_blank">
                    <input type="text" name="q" id="search-input" placeholder="Suche in Google..." autocomplete="off">
                    <button type="submit" class="search-btn">
                        <i data-lucide="search"></i>
                    </button>
                </form>
            </div>
        </section>

        <section id="custom-links" class="centered-links">
            <div class="links-grid" id="links-container">
                </div>
        </section>

        <section id="widgets">
            <div class="widgets-container">
                <div class="widget-placeholder">
                    <p>Widget-Bereich</p>
                </div>
            </div>
        </section>
    </div>

    <div id="edit-overlay" class="edit-overlay hidden">
        <div class="edit-modal">
            <h3>Link bearbeiten</h3>
            <form id="edit-link-form">
                <input type="hidden" id="edit-link-id">
                <div class="form-group">
                    <label for="edit-link-name">Name:</label>
                    <input type="text" id="edit-link-name" required>
                </div>
                <div class="form-group">
                    <label for="edit-link-url">URL:</label>
                    <input type="url" id="edit-link-url" required>
                </div>
                <div class="form-group">
                    <label for="edit-link-icon">Icon URL:</label>
                    <input type="url" id="edit-link-icon">
                </div>
                <div class="form-actions">
                    <button type="button" id="cancel-edit">Abbrechen</button>
                    <button type="submit">Speichern</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="/assets/js/dashboard.js"></script>
</body>
</html>