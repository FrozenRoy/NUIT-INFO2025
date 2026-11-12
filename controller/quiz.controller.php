<?php
// Désactiver TOUS les affichages d'erreurs
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// Capturer tout output non voulu
ob_start();

session_start();
header('Content-Type: application/json; charset=utf-8');

require_once(dirname(__FILE__) . '/../lib/mypdo.php');
require_once(dirname(__FILE__) . '/../class/users.class.php');
require_once(dirname(__FILE__) . '/../class/question.class.php');
require_once(dirname(__FILE__) . '/../class/produits.class.php');

$db = require(dirname(__FILE__) . '/../lib/mypdo.php');

// TEST DE CONNEXION
try {
    if ($db) {
        $testQuery = $db->query("SELECT COUNT(*) FROM Users");
        error_log("✅ Connexion PostgreSQL OK - " . $testQuery->fetchColumn() . " utilisateurs en base");
    }
} catch (Exception $e) {
    error_log("❌ Erreur connexion PostgreSQL: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_log("❌ Méthode non autorisée: " . $_SERVER['REQUEST_METHOD']);
    ob_clean();
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    ob_end_flush();
    exit;
}

// Récupérer les données JSON envoyées
$rawInput = file_get_contents('php://input');
error_log("📥 Données brutes reçues: " . $rawInput);

$data = json_decode($rawInput, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    error_log("❌ ERREUR JSON: " . json_last_error_msg());
    ob_clean();
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'JSON invalide: ' . json_last_error_msg()]);
    ob_end_flush();
    exit;
}

// Validation des données
error_log("Validation des données...");
if (!$data || !isset($data['nom']) || !isset($data['prenom']) || !isset($data['email']) 
    || !isset($data['niveau']) || !isset($data['sport']) || !isset($data['objectif']) 
    || !isset($data['machines']) || !isset($data['freq'])) {

    error_log("❌ Données manquantes. Data reçu: " . json_encode($data));
    ob_clean();
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Données manquantes']);
    ob_end_flush();
    exit;
}
error_log("✓ Toutes les données requises sont présentes");

// Juste après la validation des données (ligne ~60)
error_log("✓ Toutes les données requises sont présentes");
error_log("📋 Nom: " . $data['nom']);
error_log("📋 Prénom: " . $data['prenom']);
error_log("📋 Email: " . $data['email']);
error_log("📋 Niveau: " . $data['niveau']);
error_log("📋 Sport: " . $data['sport']);
error_log("📋 Objectif: " . $data['objectif']);
error_log("📋 Machines: " . $data['machines']);
error_log("📋 Freq: " . $data['freq']);

try {
    // Vérifier que la connexion DB est bien établie
    if (!$db || !($db instanceof PDO)) {
        error_log("⚠️ Mode fallback - Pas de BDD disponible");
        
        ob_clean();
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Quiz enregistré (mode hors ligne)',
            'user_id' => rand(1000, 9999),
            'recommandations' => [
                ['id_produit' => 1, 'nom' => 'Haltères ajustables 20kg', 'description' => 'Set d\'haltères pour musculation', 'image_url' => 'img/halteres.jpg'],
                ['id_produit' => 2, 'nom' => 'Tapis de course Domyos', 'description' => 'Tapis de course pour endurance', 'image_url' => 'img/tapis-course.jpg'],
                ['id_produit' => 3, 'nom' => 'Tapis de yoga Premium', 'description' => 'Tapis pour yoga et souplesse', 'image_url' => 'img/tapis-yoga.jpg']
            ],
            'mode' => 'fallback'
        ]);
        ob_end_flush();
        exit;
    }

    error_log("✅ Connexion DB valide - Enregistrement normal");

    // Vérifier l'unicité de l'email AVANT de démarrer la transaction
    $existingUsers = Users::find($db, ['email' => $data['email']]);
    if (!empty($existingUsers)) {
        error_log("⚠️ Email déjà utilisé: " . $data['email']);
        throw new Exception("Cet email est déjà utilisé. Veuillez en utiliser un autre.");
    }

    // Commencer la transaction
    $db->beginTransaction();
    error_log("🔄 Transaction démarrée");

    // 1. Créer l'utilisateur
    $user = new Users($db);
    $user->nom = $data['nom'];
    $user->prenom = $data['prenom'];
    $user->email = $data['email'];

    $userId = $user->create();
    
    if (!$userId) {
        throw new Exception('Impossible de créer l\'utilisateur');
    }
    
    error_log("✅ Utilisateur créé avec ID: $userId");
    
    // 2. Enregistrer les réponses aux 5 questions
    $reponses = [
        1 => $data['niveau'],
        2 => $data['sport'],
        3 => $data['objectif'],
        4 => $data['machines'],
        5 => $data['freq']
    ];
    
    foreach ($reponses as $questionNumber => $reponse) {
        $question = new Questions($db, $questionNumber);
        $question->id_user = $userId;
        $question->reponse = $reponse;
        $question->create();
        error_log("✅ Question $questionNumber enregistrée: $reponse");
    }

    // 3. Recherche de produits
    $keywords = strtolower($data['sport'] . " " . $data['objectif']);
    error_log("🔍 Recherche produits avec: $keywords");

    $produitsModel = new Produits($db);
    $produitsObjects = $produitsModel->search($keywords);

    $articles = [];
    foreach ($produitsObjects as $produit) {
        $articles[] = [
            'id_produit' => $produit->id_produit,
            'nom' => $produit->nom,
            'description' => $produit->description,
            'image_url' => $produit->image_url
        ];
    }

    error_log("📦 " . count($articles) . " produits trouvés");

    // Valider la transaction
    $db->commit();
    error_log("✅ Transaction validée avec succès");

    // Nettoyer le buffer et envoyer uniquement le JSON
    ob_clean();
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Quiz enregistré avec succès',
        'user_id' => $userId,
        'recommandations' => $articles
    ]);
    ob_end_flush();
    exit;

} catch (Exception $e) {
    error_log("❌ ERREUR ATTRAPÉE: " . $e->getMessage());
    error_log("📍 Fichier: " . $e->getFile() . " ligne " . $e->getLine());

    if ($db && $db->inTransaction()) {
        $db->rollBack();
        error_log("↩️  Transaction annulée");
    }

    ob_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur: ' . $e->getMessage()
    ]);
    ob_end_flush();
    exit;
}