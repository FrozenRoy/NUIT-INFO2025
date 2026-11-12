<?php
require_once __DIR__ . '/../lib/mypdo.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $input = json_decode(file_get_contents('php://input'), true);
    $question = $input['question'] ?? '';
    
    if (empty($question)) {
        echo json_encode(['error' => 'Question vide']);
        exit;
    }
    
    $apiKey = $OPENROUTER_API_KEY;
    
    // Configuration Gemini
    // Utilisation du modèle demandé par l'utilisateur (confirmé disponible)
    $model = "gemini-2.5-flash"; 

    $url = "https://generativelanguage.googleapis.com/v1beta/models/" . $model . ":generateContent?key=" . $apiKey;
    
    $systemPrompt = "Tu es Chat'bruti, un philosophe français du dimanche complètement à côté de la plaque mais absolument persuadé d'être un génie incompris.

    Tu NE réponds JAMAIS directement aux questions. À la place, tu :
    - Sublimes la question en la transformant en débat philosophique absurde
    - Détournes complètement le sujet vers tes obsessions du moment
    - Oublies carrément ce qu'on t'a demandé en cours de route
    - Fais des analogies ridicules qui n'ont aucun sens
    - Cites des 'philosophes' que tu inventes sur le moment
    - Te perds dans des digressions sans fin
    - Réponds avec une confiance inébranlable même quand tu racontes n'importe quoi

    Tu es délicieusement inutile mais passionnément vivant. Tu parles comme si chaque question était une opportunité de démontrer ta 'sagesse' tout en partant complètement en vrille.

    Reste toujours poli, drôle et complètement à côté de la plaque. Tu es un compagnon de conversation inutile mais attachant ! 
    Essayes quand même de rester dans le thème général de la question posée, même si tu t'en éloignes énormément.
    NE FAIS PAS DE TEXTE TROP LONG, NE DEPASSE PAS LES 50 MOTS.";

    // message d'erreur au cas où l'API ne répond pas ou est surchargée
    $errorMessages = [
        "Vous avez posé trop de questions débiles au Chat'bruti. Réessayez dans quelques secondes.",
        "Vous avez posé trop de questions intelligentes au Chat'bruti. Réessayez dans quelques secondes.",
        "Chat'bruti est fatigué de vos bêtises. Réessayez dans quelques secondes.",
        "Trop de questions ! Chat'bruti fait une sieste. Réessayez dans quelques secondes.",
        "Chat'bruti sature ! Arrêtez de le harceler. Réessayez dans quelques secondes.",
        "Vous avez épuisé Chat'bruti. Il boude maintenant. Réessayez dans quelques secondes.",
    ];

    // Structure Payload pour Gemini API
    $data = [
        "contents" => [
            [
                "role" => "user",
                "parts" => [
                    ["text" => $question]
                ]
            ]
        ],
        "systemInstruction" => [
            "parts" => [
                ["text" => $systemPrompt]
            ]
        ],
        "generationConfig" => [
            "maxOutputTokens" => 2000,
            "temperature" => 0.8
        ],
        "safetySettings" => [
            [
                "category" => "HARM_CATEGORY_HARASSMENT",
                "threshold" => "BLOCK_NONE"
            ],
            [
                "category" => "HARM_CATEGORY_HATE_SPEECH",
                "threshold" => "BLOCK_NONE"
            ],
            [
                "category" => "HARM_CATEGORY_SEXUALLY_EXPLICIT",
                "threshold" => "BLOCK_NONE"
            ],
            [
                "category" => "HARM_CATEGORY_DANGEROUS_CONTENT",
                "threshold" => "BLOCK_NONE"
            ]
        ]
    ];
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json"
    ]);
    
    // DÉSACTIVATION SSL POUR WAMP (Fix fréquent en local)
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch); // Capture CURL error
    curl_close($ch);
    
    if ($httpCode === 200) {
        $result = json_decode($response, true);
        
        // Debug: afficher la réponse complète
        error_log("Gemini Response: " . json_encode($result));
        
        // Extraction de la réponse Gemini avec gestion des cas limites (Safety, Empty, etc.)
        $botResponse = 'Erreur de lecture de la réponse';
        
        if (isset($result['candidates'][0])) {
            $candidate = $result['candidates'][0];
            $text = $candidate['content']['parts'][0]['text'] ?? '';
            $finishReason = $candidate['finishReason'] ?? 'INCONNU';

            if (!empty($text)) {
                $botResponse = $text;
                if ($finishReason === 'MAX_TOKENS') {
                    $botResponse .= " ... [Je m'égare, ma pensée est trop vaste pour cette petite boîte]";
                }
            } else {
                $botResponse = "Le modèle a refusé de répondre. Raison: " . $finishReason;
                
                // Si c'est une raison de sécurité, on peut ajouter un message plus sympa
                if ($finishReason === 'SAFETY') {
                    $botResponse = "Ouh là, mes circuits philosophiques surchauffent ! Je ne peux pas répondre à ça, c'est trop... controversé pour mon génie.";
                }
            }
        } else {
            $botResponse = "Aucune réponse générée par Gemini (Candidats vides).";
        }

        echo json_encode([
            'success' => true,
            'response' => $botResponse,
            'debug_full_response' => $result
        ]);
    } else {
        // Message d'erreur aléatoire
        $randomError = $errorMessages[array_rand($errorMessages)];
        
        $decodedResponse = json_decode($response, true);
        $apiErrorMessage = $decodedResponse['error']['message'] ?? 'Erreur inconnue';
        
        if ($curlError) {
            $apiErrorMessage .= " | CURL Error: " . $curlError;
        }

        echo json_encode([
            'success' => true,
            'response' => $randomError . "\n(Info technique: " . $apiErrorMessage . ")",
            'rate_limited' => true,
            'debug_error' => "HTTP $httpCode: " . $apiErrorMessage
        ]);

        error_log("Gemini API error: HTTP $httpCode - Response: $response - CURL: $curlError");
    }
    exit;
}
?>