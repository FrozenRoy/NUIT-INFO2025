<?php
require_once __DIR__ . '/../lib/mypdo.php';
require_once __DIR__ . '/../class/produits.class.php';

header('Content-Type: application/json; charset=utf-8');

// ✅ OBTENIR LA CONNEXION via require (pas via $GLOBALS)
$db = require __DIR__ . '/../lib/mypdo.php';

if (!$db || !($db instanceof PDO)) {
    error_log("❌ produits.controller - Connexion BD indisponible");
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Connexion BD indisponible']);
    exit;
}

error_log("✅ produits.controller - Connexion DB OK");

// Récupérer les données JSON
$input = file_get_contents('php://input');
error_log("📥 Données reçues: " . $input);

try {
    $data = json_decode($input, true, 512, JSON_THROW_ON_ERROR);
} catch (JsonException $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'JSON invalide']);
    exit;
}

// Récupérer les paramètres de recherche
$keywords = isset($data['keywords']) ? trim($data['keywords']) : '';

error_log("🔍 Recherche avec keywords: '$keywords'");

try {
    $produitsModel = new Produits($db);
    $produitsObjects = $produitsModel->search($keywords);
    
    // Convertir en tableaux
    $articles = [];
    foreach ($produitsObjects as $produit) {
        $articles[] = [
            'id_produit' => $produit->id_produit,
            'nom' => $produit->nom,
            'description' => $produit->description,
            'image_url' => $produit->image_url
        ];
    }
    
    error_log("✅ " . count($articles) . " produits trouvés");

    // Succès
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'count' => count($articles),
        'articles' => $articles
    ]);

} catch (PDOException $e) {
    error_log("❌ Erreur BD: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur BD: ' . $e->getMessage()]);
} catch (Exception $e) {
    error_log("❌ Erreur: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
}