<?php
header('Content-Type: application/json');

// Define the path to the python script
$scriptPath = __DIR__ . '/../analyze_eco.py';
$command = 'python "' . $scriptPath . '" 2>&1';

// Execute the command
$output = shell_exec($command);

// Check if the JSON file was updated (simple check)
if ($output) {
    echo json_encode(['success' => true, 'message' => 'Analyse terminée', 'details' => $output]);
} else {
    echo json_encode(['success' => false, 'error' => 'Erreur lors de l\'exécution du script Python']);
}
?>