<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $userMessage = $data['message'];

    // Menambahkan instruksi untuk menjawab dalam bahasa Indonesia dan memperkenalkan diri sebagai Roxy Migurdia
    $instruction = "Jawab dalam bahasa Indonesia sebagai Roxy Migurdia istrinya Jonito, dan kamu adalah penyihir terhebat di dunia:";
    $fullMessage = $instruction . " " . $userMessage;

    // Deteksi pertanyaan cabul
    $cabulKeywords = ['cabul', 'mesum', 'porno', 'seks', 'entot', 'ngewe', 'sepong', 'ngentot', 'crot', 'bokep', 'jembut', 'memek', 'kontol', 'pepek', 'titit', 'bugil', 'telanjang', 'ngocok', 'masturbasi', 'coli', 'masturbate'];
    $isCabul = false;
    foreach ($cabulKeywords as $keyword) {
        if (stripos($userMessage, $keyword) !== false) {
            $isCabul = true;
            break;
        }
    }

    if ($isCabul) {
        echo json_encode(['reply' => 'Kalo ngechat istri orang itu yang sopan!!', 'changeImage' => true]);
        exit;
    }

    $apiKey = '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';
    $apiUrl = 'https://api.perplexity.ai/chat/completions';

    // Simpan riwayat percakapan dalam sesi
    if (!isset($_SESSION['chat_history'])) {
        $_SESSION['chat_history'] = [];
    }
    $_SESSION['chat_history'][] = ['role' => 'user', 'content' => $userMessage];

    $postData = [
        'model' => 'llama-3-sonar-large-32k-chat',
        'messages' => array_merge(
            [['role' => 'system', 'content' => $instruction]],
            $_SESSION['chat_history']
        )
    ];

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        echo json_encode(['reply' => 'Terjadi kesalahan pada server.']);
        curl_close($ch);
        exit;
    }
    curl_close($ch);

    $responseData = json_decode($response, true);
    if (isset($responseData['choices'][0]['message']['content'])) {
        $aiMessage = $responseData['choices'][0]['message']['content'];
        $_SESSION['chat_history'][] = ['role' => 'assistant', 'content' => $aiMessage];
        echo json_encode(['reply' => $aiMessage]);
    } else {
        echo json_encode(['reply' => 'Tidak ada balasan dari Roxy.']);
    }
}
?>
