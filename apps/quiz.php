<div class="window draggable-window" id="app-quiz" style="display:none; top: 150px; left: 500px;">
    <div class="window-title-bar">
        <span class="title">QCM Decathlon</span>
        <div class="window-controls"><span class="close-btn" onclick="closeApp('app-quiz')">✕</span></div>
    </div>
    <div class="window-content quiz-content">
        <!-- Fenêtre d'accueil -->
        <section id="fenetre-form-initial" class="fenetre-qcm active-qcm">
            <h4>Bienvenue sur le QCM Décathlon</h4>
            <?php
                $facts = [
                    "Découvrez votre profil sportif personnalisé !",
                ];
                echo "<div class='fact-box'><i class='fas fa-lightbulb'></i> " . $facts[array_rand($facts)] . "</div>";
            ?>
            <button class="btn-nird" onclick="allerAuQCM()">Commencer le QCM</button>
        </section>

        <!-- Fenêtre QCM -->
        <section id="fenetre-qcm" class="fenetre-qcm hidden-qcm">
            <h4>QCM : Profil Sportif</h4>
            <div class="qcm-form">
                <label>Niveau sportif :</label>
                <select id="niveau" required>
                    <option value="">-- choisir --</option>
                    <option>Débutant</option>
                    <option>Intermédiaire</option>
                    <option>Avancé</option>
                </select>

                <label>Sport préféré :</label>
                <select id="sport" required>
                    <option value="">-- choisir --</option>
                    <option>Musculation</option>
                    <option>Course</option>
                    <option>Yoga</option>
                    <option>Fitness</option>
                </select>

                <label>Objectif :</label>
                <select id="objectif" required>
                    <option value="">-- choisir --</option>
                    <option>Perte de poids</option>
                    <option>Prise de muscle</option>
                    <option>Souplesse</option>
                    <option>Endurance</option>
                </select>

                <label>Machines utilisées fréquemment :</label>
                <select id="machines" required>
                    <option value="">-- choisir --</option>
                    <option>Vélo elliptique</option>
                    <option>Tapis de course</option>
                    <option>Rameur</option>
                    <option>Machine à abdominaux</option>
                    <option>Machine à jambes</option>
                </select>

                <label>Fréquence d'entraînement :</label>
                <select id="freq" required>
                    <option value="">-- choisir --</option>
                    <option>1 fois par semaine</option>
                    <option>3 fois par semaine</option>
                    <option>4 fois par semaine</option>
                </select>

                <button class="btn-nird" onclick="allerAuxResultats()">Voir les conseils</button>
            </div>
        </section>

        <!-- Fenêtre Résultats -->
        <section id="fenetre-resultat" class="fenetre-qcm hidden-qcm">
            <h4>Votre Profil & Conseils</h4>
            <div id="resultat"></div>
            
            <div style="margin-top: 25px;">
                <h4 style="color: #003451; margin-bottom: 15px;">🏋️ Produits recommandés pour vous</h4>
                <div id="liste-articles" style="min-height: 150px;">
                    <p style="text-align: center; color: #999; padding: 20px;">Chargement des produits...</p>
                </div>
            </div>
            
            <button class="btn-nird" onclick="allerAuFormulaireFinal()" style="margin-top: 20px;">Continuer</button>
        </section>

        <!-- Fenêtre Formulaire Final -->
        <section id="fenetre-form-final" class="fenetre-qcm hidden-qcm">
            <h4>Finalisez votre inscription</h4>
            <div class="qcm-form">
                <label>Nom :</label>
                <input type="text" id="nom" required>

                <label>Prénom :</label>
                <input type="text" id="prenom" required>

                <label>Email :</label>
                <input type="email" id="email" required>

                <button class="btn-nird" onclick="terminerQCM()">Terminer le QCM</button>
            </div>
        </section>

        <!-- Fenêtre Confirmation -->
        <section id="fenetre-confirmation" class="fenetre-qcm hidden-qcm">
            <h4>Merci d'avoir complété le QCM !</h4>
            <div id="info-utilisateur"></div>
            <div id="recap-profil"></div>
            <button class="btn-nird" onclick="recommencer()">Recommencer</button>
        </section>
    </div>
</div>