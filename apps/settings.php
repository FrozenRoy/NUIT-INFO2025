<div class="window draggable-window" id="app-settings" style="display:none; top: 120px; left: 400px; width: 600px;">
    <div class="window-title-bar">
        <span class="title">Paramètres Système</span>
        <div class="window-controls"><span class="close-btn" onclick="closeApp('app-settings')">✕</span></div>
    </div>
    <div class="window-content settings-content">
        
        <div class="settings-section">
            <h4><i class="fas fa-paint-brush"></i> Apparence & Thèmes</h4>
            <p class="setting-desc">Personnalisez l'interface de votre environnement NIRD.</p>
            
            <div class="theme-grid">
                <div class="theme-card active" id="theme-nirdos">
                    <div class="theme-preview dark"></div>
                    <span>NIRD OS (Défaut)</span>
                </div>
                
                <div class="theme-card" id="theme-nird95">
                    <div class="theme-preview light"></div>
                    <span>NIRD 95</span>
                </div>
            </div>
        </div>

        <div class="settings-footer">
            <button class="btn-nird secondary">Rétablir les défauts</button>
            <button class="btn-nird">Enregistrer</button>
        </div>

    </div>
</div>