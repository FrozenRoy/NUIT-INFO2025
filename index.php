<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NIRD OS - Votre Web OS</title>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- CSS Modules -->
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/windows.css">
    <link rel="stylesheet" href="css/apps.css">
    <link rel="stylesheet" href="css/style_qcm.css">
</head>
<body>

    <!-- Mobile Warning Overlay -->
    <div id="mobile-warning">
        <div class="warning-content">
            <i class="fas fa-desktop fa-3x"></i>
            <h2>Appareil Incompatible</h2>
            <p>NIRD OS est conçu pour une expérience de bureau.</p>
            <p>Veuillez utiliser un ordinateur accéder à ce système.</p>
        </div>
    </div>

    <header id="top-panel">
        <div class="panel-left">
            <span class="activities-btn"><i class="fas fa-bullseye"></i> NIRD OS</span>
        </div>
        <div class="panel-center">
            <span id="clock"></span>
        </div>
        <div class="panel-right">
            <span class="eco-badge" title="Empreinte carbone page : Faible"><i class="fas fa-leaf"></i> 48kb</span>
            <i class="fas fa-power-off" title="Déconnexion"></i>
        </div>
    </header>

    <aside id="left-dock">
        <ul>
            <li onclick="openApp('app-terminal')" title="Terminal NIRD">
                <i class="fas fa-terminal icon-terminal"></i>
            </li>
            <li onclick="openApp('app-resources')" title="Données NIRD">
                <i class="fas fa-folder-open icon-folder"></i>
            </li>
            <li onclick="openApp('app-chatbox')" title="Aristo NIRD">
                <i class="fas fa-comments icon-chatbox"></i>
            </li>
            <li class="dock-separator"></li>
            <li onclick="openApp('app-quiz')" title="Testez vos connaissances">
                <i class="fas fa-brain icon-quiz"></i>
            </li>
            <li class="dock-separator"></li>
            <li onclick="openApp('app-help')" title="Aide NIRD OS">
                <i class="fas fa-question-circle icon-help"></i>
            </li>
            <li onclick="openApp('app-settings')" title="Paramètres NIRD OS">
                <i class="fas fa-cog icon-settings"></i>
            </li>
        </ul>
    </aside>

    <main id="desktop-area">
        
        <?php include 'apps/terminal.php'; ?>
        <?php include 'apps/resources.php'; ?>
        <?php include 'apps/snake.php'; ?>
        <?php include 'apps/chat.php'; ?>
        <?php include 'apps/quiz.php'; ?>
        <?php include 'apps/settings.php'; ?>
        <?php include 'apps/help.php'; ?>

        <!-- Eco Audit Modal -->
        <div id="eco-modal" class="modal-overlay" style="display: none;">
            <div class="modal-window">
                <div class="modal-header">
                    <h3><i class="fas fa-leaf"></i> Audit Écologique</h3>
                    <button onclick="refreshEcoStats()" style="background:none; border:none; color:white; cursor:pointer; margin-left:auto; margin-right:10px;" title="Recalculer"><i class="fas fa-sync-alt"></i></button>
                    <span class="close-btn" onclick="closeEcoModal()">✕</span>
                </div>
                <div class="modal-content">
                    <div class="eco-score-card">
                        <div class="score-circle">
                            <span id="eco-grade">A</span>
                            <small>Grade</small>
                        </div>
                        <div class="score-details">
                            <h4>Score EcoIndex</h4>
                            <span id="eco-score" class="big-number">--/100</span>
                        </div>
                    </div>
                    
                    <div class="eco-stats-grid">
                        <div class="stat-item">
                            <i class="fas fa-weight-hanging"></i>
                            <span>Poids Total</span>
                            <strong id="eco-weight">-- KB</strong>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-exchange-alt"></i>
                            <span>Requêtes</span>
                            <strong id="eco-requests">--</strong>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-images"></i>
                            <span>Images</span>
                            <strong id="eco-images">--</strong>
                        </div>
                    </div>

                    <div class="unused-files-section">
                        <h4><i class="fas fa-trash-alt"></i> Fichiers potentiellement inutilisés</h4>
                        <ul id="eco-unused" class="unused-list">
                            <li>Analyse en cours...</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <script src="js/window-manager.js" defer></script>
    <script src="js/apps.js" defer></script>
    <script src="js/terminal.js" defer></script>
    <script src="js/core.js" defer></script>
    <script src="js/chat.js" defer></script>
    <script src="js/script.js" defer></script>
    <script src="js/snake.js" defer></script>
    <script src="js/switch_theme.js" defer></script>
    <script src="js/script_qcm.js" defer></script>
</body>
</html>