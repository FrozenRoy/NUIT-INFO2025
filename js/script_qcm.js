// === Navigation entre les fenêtres ===

function allerAuQCM() {
    const formInitial = document.getElementById('fenetre-form-initial');
    const qcm = document.getElementById('fenetre-qcm');

    if (formInitial && qcm) {
        formInitial.classList.remove('active-qcm');
        formInitial.classList.add('hidden-qcm');
        qcm.classList.remove('hidden-qcm');
        qcm.classList.add('active-qcm');
    }
}

function allerAuxResultats() {
    // Validation des champs
    const niveau = document.getElementById('niveau')?.value || '';
    const sport = document.getElementById('sport')?.value || '';
    const objectif = document.getElementById('objectif')?.value || '';
    const machines = document.getElementById('machines')?.value || '';
    const freq = document.getElementById('freq')?.value || '';

    console.log('🔍 Valeurs récupérées:', { niveau, sport, objectif, machines, freq });

    if (!niveau || !sport || !objectif || !machines || !freq) {
        alert('Veuillez remplir tous les champs');
        return;
    }

    // Stocker les données
    const profil = { niveau, sport, objectif, machines, freq };
    sessionStorage.setItem('profil', JSON.stringify(profil));
    console.log('💾 Profil sauvegardé:', profil);
    console.log('💾 SessionStorage:', sessionStorage.getItem('profil'));

    // Afficher les résultats
    const resultat = `
        <p><strong>Niveau :</strong> ${niveau}</p>
        <p><strong>Sport :</strong> ${sport}</p>
        <p><strong>Objectif :</strong> ${objectif}</p>
        <p><strong>Machines :</strong> ${machines}</p>
        <p><strong>Fréquence :</strong> ${freq}</p>
        <p><em>Conseil : Adaptez votre programme selon votre niveau et vos objectifs !</em></p>
    `;
    const resultatDiv = document.getElementById('resultat');
    if (resultatDiv) resultatDiv.innerHTML = resultat;

    // Charger les produits recommandés depuis la BDD
    chargerArticles(sport, objectif);

    // Navigation
    const qcm = document.getElementById('fenetre-qcm');
    const resultatFenetre = document.getElementById('fenetre-resultat');

    if (qcm && resultatFenetre) {
        qcm.classList.remove('active-qcm');
        qcm.classList.add('hidden-qcm');
        resultatFenetre.classList.remove('hidden-qcm');
        resultatFenetre.classList.add('active-qcm');
    }
}

function allerAuFormulaireFinal() {
    const resultatFenetre = document.getElementById('fenetre-resultat');
    const formFinal = document.getElementById('fenetre-form-final');

    if (resultatFenetre && formFinal) {
        resultatFenetre.classList.remove('active-qcm');
        resultatFenetre.classList.add('hidden-qcm');
        formFinal.classList.remove('hidden-qcm');
        formFinal.classList.add('active-qcm');
    }
}

function terminerQCM() {
    const nom = document.getElementById('nom')?.value || '';
    const prenom = document.getElementById('prenom')?.value || '';
    const email = document.getElementById('email')?.value || '';

    if (!nom || !prenom || !email) {
        alert('Veuillez remplir tous les champs');
        return;
    }

    // Récupérer le profil
    const profil = JSON.parse(sessionStorage.getItem('profil'));
    if (!profil) {
        alert("Profil introuvable, recommencez le QCM.");
        return;
    }

    // Préparer les données à envoyer
    const quizData = { 
        nom: nom,           // ✓
        prenom: prenom,     // ✓
        email: email,       // ✓
        niveau: profil.niveau,     // ← Vérifiez que profil existe
        sport: profil.sport,       // ← Vérifiez que profil existe
        objectif: profil.objectif, // ← Vérifiez que profil existe
        machines: profil.machines, // ← Vérifiez que profil existe
        freq: profil.freq          // ← Vérifiez que profil existe
    };

    console.log('📤 Données à envoyer:', quizData);
    console.log('📤 Profil récupéré:', profil);

    console.log('📤 Envoi des données au serveur:', quizData);

    // Envoyer les données au serveur
    fetch('./controller/quiz.controller.php', {
        method: 'POST',
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(quizData)
    })
    .then(response => {
        console.log('📥 Réponse HTTP reçue:', response.status, response.statusText);
        
        // 🔍 AFFICHER LA RÉPONSE BRUTE AVANT DE PARSER
        return response.text().then(text => {
            console.log('📄 RÉPONSE BRUTE:', text);
            console.log('📄 LONGUEUR:', text.length);
            console.log('📄 PREMIERS CARACTÈRES:', text.substring(0, 200));
            
            // Essayer de parser en JSON
            try {
                const data = JSON.parse(text);
                console.log('✅ JSON valide:', data);
                return data;
            } catch (e) {
                console.error('❌ Erreur parsing JSON:', e);
                console.error('❌ Texte reçu:', text);
                throw new Error('Le serveur n\'a pas renvoyé du JSON valide');
            }
        });
    })
    .then(data => {
        console.log('✅ Données JSON décodées:', data);
        if (data.success) {
            const infoUtilisateur = document.getElementById('info-utilisateur');
            const recapProfil = document.getElementById('recap-profil');
            const formFinal = document.getElementById('fenetre-form-final');
            const confirmation = document.getElementById('fenetre-confirmation');

            if (infoUtilisateur) {
                infoUtilisateur.innerHTML = `
                    <p><strong>Nom :</strong> ${nom}</p>
                    <p><strong>Prénom :</strong> ${prenom}</p>
                    <p><strong>Email :</strong> ${email}</p>
                `;
            }

            if (recapProfil) {
                recapProfil.innerHTML = `
                    <p><strong>Profil sportif :</strong></p>
                    <p>Niveau : ${profil.niveau}</p>
                    <p>Sport : ${profil.sport}</p>
                    <p>Objectif : ${profil.objectif}</p>
                    <p class="success-message">✓ Vos réponses ont été enregistrées avec succès !</p>
                `;
            }

            if (formFinal) {
                formFinal.classList.remove('active-qcm');
                formFinal.classList.add('hidden-qcm');
            }

            if (confirmation) {
                confirmation.classList.remove('hidden-qcm');
                confirmation.classList.add('active-qcm');
            }
        } else {
            alert('Erreur lors de l\'enregistrement : ' + data.message);
        }
    })
    .catch(error => {
        console.error('❌ Erreur complète:', error);
        alert('Une erreur est survenue lors de l\'enregistrement de vos réponses. Vérifiez la console (F12).');
    });
}

function recommencer() {
    sessionStorage.clear();

    const confirmation = document.getElementById('fenetre-confirmation');
    const formInitial = document.getElementById('fenetre-form-initial');

    if (confirmation) {
        confirmation.classList.remove('active-qcm');
        confirmation.classList.add('hidden-qcm');
    }

    if (formInitial) {
        formInitial.classList.remove('hidden-qcm');
        formInitial.classList.add('active-qcm');
    }

    // Réinitialiser les champs
    ['niveau', 'sport', 'objectif', 'machines', 'freq', 'nom', 'prenom', 'email'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });
}

// === Recherche et affichage des articles ===

async function chargerArticles(sport, objectif) {
    try {
        const keywords = `${sport} ${objectif}`;
        console.log("🔍 Recherche de produits avec:", keywords);

        const response = await fetch("controller/produits.controller.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ keywords: keywords.toLowerCase() })
        });

        console.log("📡 Réponse reçue, status:", response.status);

        const responseText = await response.text();
        console.log("📄 Contenu brut de la réponse:", responseText);

        let data;
        try {
            data = JSON.parse(responseText);
            console.log("📦 Données parsées:", data);
        } catch (parseError) {
            console.error("❌ Erreur de parsing JSON:", parseError);
            console.error("📄 Réponse brute:", responseText);
            throw new Error("La réponse du serveur n'est pas du JSON valide");
        }

        if (data.success) {
            console.log(`✅ ${data.count} produit(s) trouvé(s)`);
            afficherArticles(data.articles);
        } else {
            console.error('❌ Erreur lors du chargement des produits:', data.message);
            afficherArticles([]);
        }
    } catch (error) {
        console.error('💥 Erreur:', error);
        afficherArticles([]);
    }
}

function afficherArticles(articles) {
    const listeArticles = document.getElementById("liste-articles");

    if (!listeArticles) {
        console.error("Élément #liste-articles introuvable");
        return;
    }

    if (!articles || articles.length === 0) {
        listeArticles.innerHTML = "<p class='no-products'>Aucun produit disponible pour votre profil actuellement. 😔</p>";
        return;
    }

    let html = "<div class='produits-grid'>";
    articles.forEach(article => {
        html += `
        <div class="produit-card">
            <img src="${article.image_url || 'img/default-product.png'}" alt="${article.nom}">
            <h5>${article.nom}</h5>
            <p class="produit-description">${article.description || ''}</p>
        </div>`;
    });
    html += "</div>";

    listeArticles.innerHTML = html;
}
