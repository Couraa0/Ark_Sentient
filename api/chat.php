<?php
// File: Ark_Sentient/api/chat.php

// --- TAMBAH BAGIAN INI UNTUK MEMUAT KONEKSI DATABASE ---
// Memuat koneksi database dari config.php
require_once __DIR__ . '/../Config/config.php';

// Debugging: Melacak status koneksi database
error_log("DEBUG (chat.php): Memuat config.php dan mencoba mendapatkan koneksi DB.");
try {
    $conn = getDbConnection(); // Dapatkan koneksi database dari config.php
    error_log("DEBUG (chat.php): Koneksi database berhasil diperoleh.");
} catch (PDOException $e) {
    // Jika koneksi database gagal, hentikan eksekusi dan kirim error JSON
    error_log("FATAL ERROR (chat.php): Koneksi database GAGAL: " . $e->getMessage());
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Koneksi database gagal. Mohon periksa konfigurasi database Anda.']);
    exit(); // Hentikan eksekusi script
}
error_log("DEBUG (chat.php): Blok koneksi database selesai.");
// --- AKHIR TAMBAHAN KONEKSI DATABASE ---


// --- TAMBAHKAN KODE INI DI AWAL FILE UNTUK PHP DOTENV ---
// Memuat library phpdotenv dan environment variables dari file .env
// Pastikan file .env berada di folder Ark_Sentient/ (satu level di atas api/)
if (file_exists(__DIR__ . '/../.env')) {
    require_once __DIR__ . '/../vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..'); // Path ke folder Ark_Sentient/
    try {
        $dotenv->load();
        error_log("DEBUG (chat.php): File .env berhasil dimuat.");
        error_log("DEBUG (chat.php): GEMINI_API_KEY dari ENV: " . (isset($_ENV['GEMINI_API_KEY']) ? 'SET' : 'NOT SET'));
    } catch (Dotenv\Exception\InvalidFileException $e) {
        error_log("FATAL ERROR (chat.php): Format file .env tidak valid: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Gagal memuat konfigurasi .env: ' . $e->getMessage()]);
        exit();
    }
} else {
    require_once __DIR__ . '/../vendor/autoload.php';
    error_log("DEBUG (chat.php): File .env tidak ditemukan (menggunakan variabel lingkungan sistem).");
}
// --- AKHIR KODE TAMBAHAN UNTUK DOTENV ---


// Mengatur header untuk respons JSON dan mengizinkan CORS
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: ' . ($_SERVER['HTTP_ORIGIN'] ?? '*'));
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

// --- KONFIGURASI AI ---
$gemini_api_key = $_ENV['GEMINI_API_KEY'] ?? getenv('GEMINI_API_KEY');

if (empty($gemini_api_key)) {
    error_log("FATAL ERROR (chat.php): API Key Gemini kosong.");
    http_response_code(500);
    echo json_encode(['error' => 'API Key tidak ditemukan. Pastikan GEMINI_API_KEY diatur di file .env atau environment variable sistem.']);
    exit();
}

$gemini_model = 'gemini-1.5-flash';
$gemini_endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$gemini_model}:generateContent?key={$gemini_api_key}";


// Ambil raw POST data (body JSON) dari permintaan frontend
$input_data = file_get_contents('php://input');
$request_data = json_decode($input_data, true);

if (json_last_error() !== JSON_ERROR_NONE || !isset($request_data['message'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON input or missing message.']);
    exit();
}

$user_message = $request_data['message'];
$chat_history_from_frontend = $request_data['history'] ?? [];
// Batasi history yang dikirim ke AI hanya 5 pesan terakhir
if (is_array($chat_history_from_frontend) && count($chat_history_from_frontend) > 5) {
    $chat_history_from_frontend = array_slice($chat_history_from_frontend, -5);
}
$conversation_id = $request_data['conversation_id'] ?? null;
$user_id = $_SESSION['user_id'] ?? null; // Ambil user_id dari sesi login

// Tambahkan pengecekan jika user_id tidak ditemukan (user belum login atau sesi sudah habis)
if (empty($user_id)) {
    error_log("ERROR (chat.php): Akses tanpa otentikasi. User ID kosong.");
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Anda harus login untuk mengakses fitur ini.']);
    exit();
}


$ai_reply = "Maaf, Smart Assistant sedang tidak bisa memproses permintaan Anda saat ini.";

try {
    // Debugging: Melacak penyimpanan pesan pengguna
    error_log("DEBUG (chat.php): Mencoba menyimpan pesan pengguna ke database.");
    if (empty($conversation_id)) {
        $stmt = $conn->prepare("INSERT INTO conversations (user_id, title) VALUES (?, ?)");
        $initial_title = substr($user_message, 0, 50);
        if (mb_strlen($user_message) > 50) {
            $initial_title .= "...";
        }
        $stmt->execute([$user_id, $initial_title]);
        $conversation_id = $conn->lastInsertId();
        error_log("DEBUG (chat.php): Percakapan baru dibuat dengan ID: " . $conversation_id);
    }

    // Simpan pesan pengguna ke database
    $stmt = $conn->prepare("INSERT INTO messages (conversation_id, sender, text) VALUES (?, ?, ?)");
    $stmt->execute([$conversation_id, 'user', $user_message]);
    error_log("DEBUG (chat.php): Pesan pengguna disimpan. Konvo ID: " . $conversation_id);


    // --- Bagian Panggilan Gemini API ---
    error_log("DEBUG (chat.php): Mencoba memanggil Gemini API.");
    $client = new Client();
    $gemini_contents = [];
    foreach ($chat_history_from_frontend as $chat_entry) {
        $gemini_contents[] = [
            'role' => ($chat_entry['sender'] === 'user') ? 'user' : 'model',
            'parts' => [['text' => $chat_entry['text']]]
        ];
    }
    $gemini_contents[] = [
        'role' => 'user',
        'parts' => [['text' => $user_message]]
    ];

    $system_instruction = [
        'parts' => [
            ['text' => 'Anda adalah Smart Assistant yang ramah dan informatif yang berfokus pada informasi ternak untuk platform ARK Sentient. Tanggapi pertanyaan pengguna dengan bahasa yang mudah dimengerti. Jika pertanyaan tidak terkait ternak atau fitur ARK Sentient (Marketplace Ternak, Pemeriksa Ternak, Sejarah), arahkan pengguna kembali ke topik ternak atau fitur yang relevan, atau jelaskan bahwa Anda tidak dapat membantu dengan topik tersebut.']
        ]
    ];

    $requestBody = [
        'system_instruction' => $system_instruction,
        'contents' => $gemini_contents,
        'generationConfig' => [
            'maxOutputTokens' => 300, 
            'temperature' => 0.5,     
            'topP' => 1,
            'topK' => 1,
        ],
    ];

    $response = $client->post($gemini_endpoint, [
        'json' => $requestBody,
        'headers' => [
            'Content-Type' => 'application/json',
        ],
        'verify' => false,
    ]);

    $statusCode = $response->getStatusCode();
    $responseData = json_decode($response->getBody()->getContents(), true);

    if ($statusCode === 200 && isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
        $ai_reply = $responseData['candidates'][0]['content']['parts'][0]['text'];

        // Simpan balasan AI ke database
        error_log("DEBUG (chat.php): Mencoba menyimpan balasan AI ke database.");
        $stmt = $conn->prepare("INSERT INTO messages (conversation_id, sender, text) VALUES (?, ?, ?)");
        $stmt->execute([$conversation_id, 'ai', $ai_reply]);
        error_log("DEBUG (chat.php): Balasan AI berhasil disimpan. Konvo ID: " . $conversation_id);

    } else {
        error_log("Gemini API Error (chat.php): Status Code {$statusCode}, Response: " . json_encode($responseData));
        $ai_reply = "Maaf, ada masalah dalam menghubungi Gemini API. Coba lagi nanti. (Kode: {$statusCode})";
        if (isset($responseData['error']['message'])) {
             $ai_reply .= " Detail: " . $responseData['error']['message'];
        }
    }

} catch (RequestException $e) {
    error_log("Guzzle Request Exception (chat.php): " . $e->getMessage());
    if ($e->hasResponse()) {
        error_log("Response Body: " . $e->getResponse()->getBody()->getContents());
    }
    $ai_reply = "Maaf, terjadi kesalahan koneksi dengan layanan AI. Pastikan koneksi internet stabil.";
} catch (PDOException $e) {
    error_log("Database PDO Error (chat.php): " . $e->getMessage());
    $ai_reply = "Maaf, terjadi masalah database saat menyimpan chat.";
} catch (Exception $e) {
    error_log("General Exception (chat.php): " . $e->getMessage());
    $ai_reply = "Terjadi kesalahan yang tidak terduga di sisi server.";
}

// Kirim balasan AI dan conversation_id kembali ke frontend
ob_clean();
echo json_encode(['reply' => $ai_reply ?? '', 'conversation_id' => $conversation_id]);
exit();

// Proses penyimpanan pesan user dan balasan AI ke database sudah sesuai:
// - Jika percakapan baru, buat judul di tabel conversations
// - Setiap pesan user dan balasan AI disimpan di tabel messages
?>