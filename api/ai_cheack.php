<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Tambahkan autoload dan load .env
if (file_exists(__DIR__ . '/../.env')) {
    require_once __DIR__ . '/../vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    try {
        $dotenv->load();
    } catch (Dotenv\Exception\InvalidFileException $e) {
        error_log("FATAL ERROR (ai_cheack.php): Format file .env tidak valid: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Gagal memuat konfigurasi .env: ' . $e->getMessage()]);
        exit();
    }
} else {
    require_once __DIR__ . '/../vendor/autoload.php';
}

// Ambil API key seperti di chat.php
$gemini_api_key = $_ENV['GEMINI_API_KEY'] ?? getenv('GEMINI_API_KEY');
if (empty($gemini_api_key)) {
    error_log("FATAL ERROR (ai_cheack.php): API Key Gemini kosong.");
    http_response_code(500);
    echo json_encode(['error' => 'API Key tidak ditemukan. Pastikan GEMINI_API_KEY diatur di file .env atau environment variable sistem.']);
    exit();
}

$gemini_model = 'gemini-1.5-flash';
$gemini_endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$gemini_model}:generateContent?key={$gemini_api_key}";

// Pastikan hanya menerima POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Cek apakah ada file gambar/video yang diupload
if (isset($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['media']['tmp_name'];
    $fileType = mime_content_type($fileTmpPath);

    // Hanya izinkan gambar (image/*)
    if (strpos($fileType, 'image/') !== 0) {
        echo json_encode(['success' => false, 'message' => 'File harus berupa gambar.']);
        exit;
    }

    $imageData = file_get_contents($fileTmpPath);
    $base64Image = base64_encode($imageData);

    // Optional: deskripsi default jika tidak ada keluhan
    $keluhan = isset($_POST['keluhan']) ? trim($_POST['keluhan']) : '';
    if ($keluhan === '') {
        $keluhan = 'Tolong analisa kondisi kesehatan hewan pada gambar ini.';
    }

    $system_instruction = [
        'parts' => [
            ['text' => 'Anda adalah Smart Assistant ARK Sentient. Jawab pertanyaan terkait kesehatan ternak berdasarkan gambar dan keluhan berikut.']
        ]
    ];

    $requestBody = [
        'system_instruction' => $system_instruction,
        'contents' => [
            [
                'role' => 'user',
                'parts' => [
                    ['text' => $keluhan],
                    [
                        'inline_data' => [
                            'mime_type' => $fileType,
                            'data' => $base64Image
                        ]
                    ]
                ]
            ]
        ],
        'generationConfig' => [
            'maxOutputTokens' => 800,
            'temperature' => 0.7,
            'topP' => 1,
            'topK' => 1,
        ],
    ];

    try {
        $ch = curl_init($gemini_endpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestBody));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $result = curl_exec($ch);
        $curl_error = curl_error($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($result === false) {
            echo json_encode(['success' => false, 'message' => 'Curl error: ' . $curl_error]);
            exit;
        }

        $response = json_decode($result, true);

        if ($httpcode === 200 && isset($response['candidates'][0]['content']['parts'][0]['text'])) {
            echo json_encode([
                'success' => true,
                'result' => $response['candidates'][0]['content']['parts'][0]['text']
            ]);
        } else {
            $msg = $response['error']['message'] ?? 'Gagal mendapatkan jawaban Ark sentient';
            echo json_encode(['success' => false, 'message' => $msg]);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// Jika tidak ada file gambar, fallback ke keluhan teks saja
$keluhan = isset($_POST['keluhan']) ? trim($_POST['keluhan']) : '';
if ($keluhan === '') {
    echo json_encode(['success' => false, 'message' => 'Keluhan tidak ditemukan']);
    exit;
}

$system_instruction = [
    'parts' => [
        ['text' => 'Anda adalah Smart Assistant ARK Sentient. Jawab pertanyaan terkait kesehatan ternak berdasarkan keluhan berikut.']
    ]
];

$requestBody = [
    'system_instruction' => $system_instruction,
    'contents' => [
        [
            'role' => 'user',
            'parts' => [['text' => $keluhan]]
        ]
    ],
    'generationConfig' => [
        'maxOutputTokens' => 800,
        'temperature' => 0.7,
        'topP' => 1,
        'topK' => 1,
    ],
];

try {
    $ch = curl_init($gemini_endpoint);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestBody));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $result = curl_exec($ch);
    $curl_error = curl_error($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($result === false) {
        echo json_encode(['success' => false, 'message' => 'Curl error: ' . $curl_error]);
        exit;
    }

    $response = json_decode($result, true);

    if ($httpcode === 200 && isset($response['candidates'][0]['content']['parts'][0]['text'])) {
        echo json_encode([
            'success' => true,
            'result' => $response['candidates'][0]['content']['parts'][0]['text']
        ]);
    } else {
        $msg = $response['error']['message'] ?? 'Gagal mendapatkan jawaban Ark sentient';
        echo json_encode(['success' => false, 'message' => $msg]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
