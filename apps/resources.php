<div class="window draggable-window" id="app-resources" style="display:none; top: 80px; left: 350px; width: 800px; height: 700px;">
    <div class="window-title-bar">
        <span class="title"><i class="fas fa-info-circle"></i> Centre d'Information NIRD</span>
        <div class="window-controls"><span class="close-btn" onclick="closeApp('app-resources')">✕</span></div>
    </div>
    <div class="window-content" style="display: flex; flex-direction: column; padding: 0; background: #1e1e1e;">
        
        <div class="nird-hero" style="background: linear-gradient(135deg, #1a2a6c, #b21f1f, #fdbb2d); padding: 30px; text-align: center; color: white; position: relative; overflow: hidden;">
            <div style="position: relative; z-index: 2;">
                <i class="fas fa-project-diagram" style="font-size: 3em; margin-bottom: 15px; text-shadow: 0 2px 10px rgba(0,0,0,0.3);"></i>
                <h2 style="margin: 0; font-size: 2em; text-shadow: 0 2px 5px rgba(0,0,0,0.5);">NIRD</h2>
                <p style="margin-top: 5px; opacity: 0.9;">Numérique Inclusif, Responsable et Durable</p>
            </div>
            <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.2); z-index: 1;"></div>
        </div>

        <div class="nird-content" style="padding: 25px; overflow-y: auto; flex: 1;">
            
            <div class="info-section" style="margin-bottom: 30px;">
                <h3 style="color: var(--color-accent); border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px; margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-bullseye"></i> À propos de la NIRD
                </h3>
                <p style="line-height: 1.6; color: #ddd; font-size: 1.05em;">
                    La démarche NIRD (Numérique Inclusif, Responsable et Durable) vise à accompagner les établissements scolaires vers une transformation numérique éthique. Elle repose sur trois piliers fondamentaux :
                    <ul style="margin-top: 10px; margin-left: 20px;">
                        <li style="margin-bottom: 8px;"><b>Inclusion (Social) :</b> Garantir un accès équitable au numérique pour tous les élèves, en adaptant les outils aux besoins éducatifs particuliers (accessibilité, DYS, handicap).</li>
                        <li style="margin-bottom: 8px;"><b>Responsabilité (Civique) :</b> Former des citoyens éclairés en privilégiant des logiciels libres, souverains et respectueux des données personnelles (conformité RGPD), loin des modèles commerciaux de surveillance.</li>
                        <li><b>Durabilité (Écologique) :</b> Lutter contre l'obsolescence programmée et réduire l'empreinte carbone en prolongeant la durée de vie du matériel informatique grâce à des systèmes d'exploitation légers (Linux) et au reconditionnement.</li>
                    </ul>
                </p>
            </div>

            <div class="info-section" style="margin-bottom: 30px;">
                <h3 style="color: var(--color-accent); border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px; margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-hands-helping"></i> Pourquoi et comment contribuer ?
                </h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div style="background: rgba(255,255,255,0.03); padding: 15px; border-radius: 8px;">
                        <h4 style="margin-top: 0; color: #4caf50;"><i class="fas fa-check-circle"></i> Pourquoi adopter NIRD ?</h4>
                        <ul style="margin: 0; padding-left: 20px; color: #ccc; font-size: 0.95em;">
                            <li><b>Économies substantielles :</b> Réutilisation du matériel existant, pas de licences logicielles coûteuses.</li>
                            <li><b>Souveraineté :</b> Indépendance vis-à-vis des GAFAM, maîtrise des données scolaires.</li>
                            <li><b>Pédagogie :</b> Outils conçus par et pour les enseignants, favorisant la créativité et la collaboration.</li>
                        </ul>
                    </div>
                    <div style="background: rgba(255,255,255,0.03); padding: 15px; border-radius: 8px;">
                        <h4 style="margin-top: 0; color: #2196f3;"><i class="fas fa-code"></i> Comment participer ?</h4>
                        <ul style="margin: 0; padding-left: 20px; color: #ccc; font-size: 0.95em;">
                            <li><b>Rejoindre la Forge :</b> Participez au développement des outils sur <a href="#" onclick="window.open('https://forge.apps.education.fr/', '_blank')" style="color: #64b5f6;">forge.apps.education.fr</a>.</li>
                            <li><b>Partager :</b> Mutualisez vos ressources pédagogiques libres.</li>
                            <li><b>Tester :</b> Installez Linux NIRD ou PrimTux et faites vos retours à la communauté.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="info-section">
                <h3 style="color: var(--color-accent); border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px; margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-database"></i> Renseignements
                </h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    
                    <div class="resource-card" onclick="window.open('https://docs.forge.apps.education.fr/', '_blank')" style="background: rgba(255,255,255,0.05); padding: 15px; border-radius: 8px; cursor: pointer; transition: transform 0.2s, background 0.2s;">
                        <div style="color: #e67e22; font-size: 1.5em; margin-bottom: 10px;"><i class="fas fa-code-branch"></i></div>
                        <strong style="display: block; margin-bottom: 5px; color: #fff;">La Forge des Communs Numériques Éducatifs</strong>
                        <small style="color: #aaa;">« L'union fait la forge ! », des ressources éducatives libres conçues par des enseignants et enseignantes.</small>
                    </div>

                    <div class="resource-card" onclick="window.open('https://tube-numerique-educatif.apps.education.fr/', '_blank')" style="background: rgba(255,255,255,0.05); padding: 15px; border-radius: 8px; cursor: pointer; transition: transform 0.2s, background 0.2s;">
                        <div style="color: #e74c3c; font-size: 1.5em; margin-bottom: 10px;"><i class="fas fa-video"></i></div>
                        <strong style="display: block; margin-bottom: 5px; color: #fff;">Médiathèque</strong>
                        <small style="color: #aaa;">Vidéos pédagogiques et tutoriels en ligne. Des questions ? Trouvez des réponses ici !</small>
                    </div>

                    <div class="resource-card" onclick="window.open('https://nird.forge.apps.education.fr/', '_blank')" style="background: rgba(255,255,255,0.05); padding: 15px; border-radius: 8px; cursor: pointer; transition: transform 0.2s, background 0.2s;">
                        <div style="color: #3498db; font-size: 1.5em; margin-bottom: 10px;"><i class="fas fa-file-alt"></i></div>
                        <strong style="display: block; margin-bottom: 5px; color: #fff;">La démarche NIRD.</strong>
                        <small style="color: #aaa;">Site officiel de la NIRD. Une adoption large de Linux est possible et bénéfique !</small>
                    </div>

                    <div class="resource-card" onclick="window.open('https://nird.forge.apps.education.fr/linux/', '_blank')" style="background: rgba(255,255,255,0.05); padding: 15px; border-radius: 8px; cursor: pointer; transition: transform 0.2s, background 0.2s;">
                        <div style="color: #9b59b6; font-size: 1.5em; margin-bottom: 10px;"><i class="fas fa-users"></i></div>
                        <strong style="display: block; margin-bottom: 5px; color: #fff;">Distributions Linux</strong>
                        <small style="color: #aaa;">Découvrez PrimTux pour le primaire et Linux NIRD pour secondaire.</small>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>