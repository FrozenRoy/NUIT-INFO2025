<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Autoloader
require_once(dirname(__FILE__) . '/../vendor/autoload.php');
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__FILE__) . '/../');
$dotenv->load();

$OPENROUTER_API_KEY = $_ENV['OPENROUTER_API_KEY'];

if (empty($OPENROUTER_API_KEY)) {
    $_SESSION['mesgs']['errors'][] = 'ERREUR Configuration: les informations n\'ont pas pu être chargées.';
}

// Ne pas retourner une connexion existante - toujours en créer une nouvelle si nécessaire
// Charger l'autoloader seulement s'il n'est pas déjà chargé
if (!class_exists('Dotenv\Dotenv')) {
    $autoloadPath = dirname(__FILE__) . '/../vendor/autoload.php';
    if (file_exists($autoloadPath)) {
        require_once($autoloadPath);
    } else {
        error_log('Autoload introuvable à : ' . $autoloadPath);
        return null;
    }
}

// Charger les variables d'environnement
$envPath = dirname(__FILE__) . '/../';
$envFile = $envPath . '.env';

if (!file_exists($envFile)) {
    error_log('Fichier .env introuvable à : ' . $envFile);
    return null;
}

try {
    $dotenv = Dotenv\Dotenv::createImmutable($envPath);
    $dotenv->load();
} catch (Exception $e) {
    error_log('Erreur Dotenv: ' . $e->getMessage());
    return null;
}

// Récupération des informations présentes dans le fichier de conf .env
$db_host = $_ENV['DB_HOST'] ?? '';
$db_name = $_ENV['DB_NAME'] ?? '';
$db_port = $_ENV['DB_PORT'] ?? '5432';
$db_username = $_ENV['DB_USER'] ?? '';
$db_password = $_ENV['DB_PASS'] ?? '';

// Log des valeurs pour debug
error_log("Configuration DB:");
error_log("   HOST: " . ($db_host ?: '(vide)'));
error_log("   NAME: " . ($db_name ?: '(vide)'));
error_log("   PORT: " . $db_port);
error_log("   USER: " . ($db_username ?: '(vide)'));
error_log("   PASS: " . (empty($db_password) ? '(vide)' : '***'));

if (empty($db_host) || empty($db_name) || empty($db_username)) {
    $errorMsg = 'ERREUR Configuration: les informations n\'ont pas pu être chargées depuis .env';
    if (session_status() === PHP_SESSION_ACTIVE) {
        $_SESSION['mesgs']['errors'][] = $errorMsg;
    }
    error_log('Erreur Configuration: ' . $errorMsg);
    error_log('   HOST=' . $db_host . ', NAME=' . $db_name . ', USER=' . $db_username);
    return null;
}

// Ouverture de la connexion
$dsn = "pgsql:host=$db_host;port=$db_port;dbname=$db_name";
$db_options = array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_TIMEOUT => 5
);

try {
    $db = new PDO($dsn, $db_username, $db_password, $db_options);
    error_log("Connexion DB réussie à $db_host:$db_port/$db_name");
    
    // Test rapide de la connexion
    $test = $db->query("SELECT 1");
    error_log("Test de requête réussi");
    
    return $db;
    
} catch (PDOException $e) {
    $errorMsg = 'ERREUR Base de données: ' . $e->getMessage();
    if (session_status() === PHP_SESSION_ACTIVE) {
        $_SESSION['mesgs']['errors'][] = $errorMsg;
    }
    error_log('Erreur Base de données: ' . $errorMsg);
    error_log('DSN utilisé: ' . $dsn);
    error_log('Vérifiez que PostgreSQL est accessible à ' . $db_host . ':' . $db_port);
    
    return null;
}