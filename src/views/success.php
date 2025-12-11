<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erfolg - JumpNote</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <div class="grid-bg"></div>
    
    <header class="header-nav">
        <div class="header-content">
            <a href="index.php" class="header-btn">
                <i data-lucide="home"></i> Dashboard
            </a>
        </div>
    </header>

    <div class="container">
        <section class="hero" style="min-height: 60vh;">
            <h1><span class="hero-name">Erfolg!</span></h1>
            <p>Ihre Aktion wurde erfolgreich ausgeführt.</p>
            <div style="margin-top: 2rem;">
                <a href="index.php" class="header-btn">
                    <i data-lucide="arrow-left"></i> Zurück zum Dashboard
                </a>
            </div>
        </section>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();
        });
    </script>
</body>
</html>