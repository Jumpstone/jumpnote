<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shortlinks - JumpNote</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="grid-bg"></div>
    
    <!-- Header with navigation -->
    <header class="header-nav">
        <div class="header-content">
            <a href="index.php" class="header-btn">
                <i data-lucide="home"></i> Dashboard
            </a>
            <button id="add-element" class="header-btn">
                <i data-lucide="plus"></i> Element hinzufügen
            </button>
            <a href="logout.php" class="header-btn">
                <i data-lucide="log-out"></i> Abmelden
            </a>
        </div>
    </header>

    <div class="container">
        <section class="hero">
            <h1><span class="hero-name">Shortlinks</span> & Lesezeichen</h1>
            <p>Organisieren Sie Ihre wichtigsten Links und Lesezeichen</p>
        </section>

        <!-- Shortlinks Elements -->
        <section id="shortlink-elements">
            <div class="elements-container" id="elements-container">
                <!-- Elements will be loaded here dynamically -->
            </div>
        </section>
    </div>

    <!-- Add/Edit Element Modal -->
    <div id="element-modal" class="edit-overlay hidden">
        <div class="edit-modal">
            <h3 id="modal-title">Neues Element hinzufügen</h3>
            <form id="element-form">
                <input type="hidden" id="element-id">
                <div class="form-group">
                    <label for="element-type">Typ:</label>
                    <select id="element-type" required>
                        <option value="link">Link/Karte</option>
                        <option value="header">Überschrift</option>
                        <option value="folder">Ordner</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="element-name">Name:</label>
                    <input type="text" id="element-name" required>
                </div>
                <div class="form-group" id="url-field">
                    <label for="element-url">URL:</label>
                    <input type="url" id="element-url">
                </div>
                <div class="form-group" id="icon-field">
                    <label for="element-icon">Icon URL:</label>
                    <input type="url" id="element-icon">
                </div>
                <div class="form-group" id="description-field">
                    <label for="element-description">Beschreibung:</label>
                    <textarea id="element-description" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label for="element-parent">Übergeordneter Ordner:</label>
                    <select id="element-parent">
                        <option value="">Keiner (Oberste Ebene)</option>
                        <!-- Parent folders will be loaded here -->
                    </select>
                </div>
                <div class="form-actions">
                    <button type="button" id="cancel-element">Abbrechen</button>
                    <button type="submit">Speichern</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="../assets/js/shortlinks.js"></script>
</body>
</html>