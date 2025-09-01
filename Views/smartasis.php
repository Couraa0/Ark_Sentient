<?php
// File: Ark_Sentient/Views/smartasis.php

if (session_status() === PHP_SESSION_NONE) session_start();

// Ubah path ke file koneksi database sesuai struktur folder
require_once __DIR__ . '/../Config/config.php';

$nama_user = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Nama User'; // Mengubah default nama user agar sesuai dengan gambar
$inisial = strtoupper(substr($nama_user, 0, 1));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ark Sentient - Smart Assistant</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../Asset/css/smartasis.css" rel="stylesheet">
    <style>
        :root {
            --primary-green: #2c5530;
            --secondary-green: #1e3a21;
            --accent-lime: #AEEA00;
            --accent-green: #00C853;
            --light-gray: #f8f9fa;
            --text-dark: #333333;
            --text-muted: #666666;
            --white: #ffffff;
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .navbar {
            background: linear-gradient(135deg, var(--accent-lime) 0%, var(--accent-green) 100%) !important;
            backdrop-filter: blur(20px);
            box-shadow: var(--shadow);
            padding: 1rem 0;
            transition: var(--transition);
        }
        .navbar.scrolled {
            padding: 0.5rem 0;
            background: rgba(255, 255, 255, 0.95) !important;
        }
        .navbar-brand {
            font-weight: 700;
            color: var(--primary-green) !important;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .navbar-brand img {
            height: 32px;
            width: auto;
            transition: var(--transition);
        }
        .navbar-nav .nav-link {
            color: var(--primary-green) !important;
            font-weight: 500;
            padding: 0.5rem 1rem !important;
            border-radius: 8px;
            transition: var(--transition);
            position: relative;
        }
        .navbar-nav .nav-link:hover {
            background: rgba(44, 85, 48, 0.1);
            transform: translateY(-1px);
        }
        .navbar-nav .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: var(--primary-green);
            transition: var(--transition);
            transform: translateX(-50%);
        }
        .navbar-nav .nav-link:hover::after {
            width: 80%;
        }
    </style>
</head>
<body>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <button class="menu-btn" id="burgerBtn"><i class="fas fa-bars"></i></button>
            <!-- <button class="search-btn"><i class="fas fa-search"></i></button> -->
        </div>
        <div class="sidebar-content">
            <div class="new-chat" id="newChatButton">
                <i class="fas fa-pen"></i>
                <span>New Chat</span>
            </div>
            <div class="chat-history-section">
                <h3>Terbaru</h3>
                <div class="chat-history-list" id="chatHistoryList">
                    </div>
            </div>
        </div>
        <div class="spacer"></div>
        <div class="logo-bottom">
            <i class="fas fa-cow"></i>
            <span><b>ARK Sentient</b></span>
        </div>
    </div>
    <!-- Sidebar end -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand text-success d-flex align-items-center" href="home.php">
                <img src="../asset/img/Logo.png" alt="Logo" style="height:32px;width:auto;">
                ARK Sentient
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-store me-1"></i>
                            Marketplace
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="priksaternak.php">
                            <i class="fas fa-stethoscope me-1"></i>
                            Pemeriksaan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="smartasis.php">
                            <i class="fas fa-robot me-1"></i>
                            Smart Assistant
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../Views/about.php">
                            <i class="fas fa-info-circle me-1"></i>
                            About
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="cart.php" id="navbarCartLink">
                            <i class="fas fa-shopping-cart me-1"></i>
                            Cart
                            <?php
                            $cart_count = 0;
                            if (isset($_SESSION['user_id'])) {
                                $stmtCart = $conn->prepare("SELECT SUM(quantity) FROM cart WHERE user_id = ?");
                                $stmtCart->execute([$_SESSION['user_id']]);
                                $cart_count = (int)$stmtCart->fetchColumn();
                            }
                            if ($cart_count > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    <?php echo $cart_count; ?>
                                </span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i>
                            <?php
                            $user_display = isset($_SESSION['full_name']) && $_SESSION['full_name'] !== ''
                                ? htmlspecialchars($_SESSION['full_name'])
                                : (isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'User');
                            echo $user_display;
                            ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item" href="orders.php"><i class="fas fa-list me-2"></i>My Orders</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div style="height:80px"></div>
    <div class="main-content">
        <div class="background-logo">
            <img src="../Asset/icon/logoweb.png" alt="Logo">
        </div>
        <div class="content-wrapper">
            <div class="hello-text" id="helloText">
                Hello, <?php echo htmlspecialchars($nama_user); ?>
            </div>
            <div class="chat-display" id="chatDisplay">
            </div>
        </div>
    </div>
    <div class="input-area">
        <div class="input-box">
            <div class="input-row">
                <input
                    type="text"
                    class="input-label"
                    id="messageInput"
                    placeholder="Tanya smart asistant"
                />
                <button class="enter-btn" id="sendButton">
                    <i class="fas fa-arrow-up"></i>
                </button>
            </div>
            </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Referensi elemen DOM
        const sidebar = document.getElementById('sidebar');
        const burgerBtn = document.getElementById('burgerBtn');
        const messageInput = document.getElementById('messageInput');
        const sendButton = document.getElementById('sendButton');
        const chatDisplay = document.getElementById('chatDisplay');
        const helloText = document.getElementById('helloText');
        const newChatButton = document.getElementById('newChatButton');
        const chatHistoryList = document.getElementById('chatHistoryList');

        // Variabel untuk menyimpan riwayat chat dan ID percakapan saat ini
        let chatHistory = [];
        let currentConversationId = null;

        // Event listener untuk tombol burger sidebar
        burgerBtn.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
        });

        // Fungsi dan event listener terkait upload file dihapus sepenuhnya dari sini

        /**
         * Menambahkan pesan ke tampilan chat.
         * @param {string} sender - 'user' atau 'ai'.
         * @param {string} text - Isi pesan.
         * @param {boolean} isLoading - True jika ini adalah indikator loading.
         */
        function addMessageToChat(sender, text, isLoading = false) { 
            if (helloText.style.display !== 'none' && (sender === 'user' || sender === 'ai')) {
                helloText.style.display = 'none';
            }

            const messageDiv = document.createElement('div');
            messageDiv.className = `chat-message ${sender}`;
            
            if (isLoading) {
                messageDiv.classList.add('loading');
                messageDiv.textContent = 'Mengetik...';
            } else {
                const textNode = document.createElement('p');
                textNode.textContent = text;
                textNode.style.margin = '0';
                messageDiv.appendChild(textNode);

                chatHistory.push({ sender: sender, text: text });
            }
            
            chatDisplay.appendChild(messageDiv);
            chatDisplay.scrollTop = chatDisplay.scrollHeight;
            
            return messageDiv;
        }

        /**
         * Mengirim pesan pengguna ke API backend dan menampilkan balasan AI.
         */
        async function sendMessage() {
            const message = messageInput.value.trim();
            if (message === '') return; // Cek agar tidak mengirim pesan kosong

            addMessageToChat('user', message);
            messageInput.value = '';

            const loadingMessage = addMessageToChat('ai', '', true);
            sendButton.disabled = true;
            messageInput.disabled = true;

            try {
                const requestBody = {
                    message: message, 
                    history: chatHistory,
                    conversation_id: currentConversationId 
                };

                const response = await fetch('../api/chat.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(requestBody)
                });

                if (!response.ok) {
                    const errorText = await response.text();
                    console.error("Fetch response NOT OK:", errorText);
                    throw new Error(`HTTP error! status: ${response.status} - ${errorText}`);
                }

                const data = await response.json();
                console.log("Fetch successful, data:", data);

                loadingMessage.remove();
                addMessageToChat('ai', data.reply);

                if (currentConversationId === null && data.conversation_id) {
                    currentConversationId = data.conversation_id;
                    fetchConversations(); 
                }

            } catch (error) {
                console.error('Error in sendMessage catch block:', error);
                loadingMessage.remove();
                addMessageToChat('ai', 'Maaf, terjadi kesalahan saat berkomunikasi dengan AI. Silakan coba lagi.');
            } finally {
                sendButton.disabled = false;
                messageInput.disabled = false;
                messageInput.focus();
            }
        }

        // Event listener untuk tombol kirim (panah atas)
        sendButton.addEventListener('click', sendMessage);

        // Event listener untuk menekan tombol Enter di input pesan
        messageInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });

        // --- FUNGSI UNTUK MENGELOLA RIWAYAT CHAT DI SIDEBAR ---

        /**
         * Mengambil daftar percakapan dari backend dan menampilkannya di sidebar.
         */
        async function fetchConversations() {
            try {
                const response = await fetch('../api/list_conversations.php');
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();
                if (data.success) {
                    displayConversationList(data.conversations);
                } else {
                    console.error('Failed to fetch conversations:', data.message);
                }
            } catch (error) {
                console.error('Error fetching conversations:', error);
            }
        }

        /**
         * Menampilkan daftar percakapan di sidebar.
         * @param {Array} conversations - Daftar objek percakapan (id, title).
         */
        function displayConversationList(conversations) {
            chatHistoryList.innerHTML = '';
            conversations.forEach(conv => {
                // Container untuk item dan popup agar popup tidak terpotong frame
                const wrapper = document.createElement('div');
                wrapper.style.position = 'relative';
                wrapper.style.width = '100%';

                const convItem = document.createElement('div');
                convItem.className = 'chat-history-item';
                convItem.dataset.conversationId = conv.id;

                // Judul percakapan (klik untuk load)
                const titleSpan = document.createElement('span');
                titleSpan.className = 'title-span';
                // Batasi judul hanya 15 huruf/karakter
                let titleText = conv.title || '(Tanpa Judul)';
                if (titleText.length > 15) {
                    titleText = titleText.substring(0, 15) + '...';
                }
                titleSpan.textContent = titleText;
                titleSpan.title = conv.title || '(Tanpa Judul)';
                titleSpan.addEventListener('click', (e) => {
                    if (e.target.closest('.menu-dot-btn') || e.target.closest('.menu-popup')) return;
                    loadConversation(conv.id);
                });

                // Tombol titik tiga
                const menuBtn = document.createElement('button');
                menuBtn.className = 'menu-dot-btn';
                menuBtn.innerHTML = '<i class="fas fa-ellipsis-v"></i>';
                menuBtn.setAttribute('aria-label', 'Menu');
                menuBtn.type = 'button';

                // Menu popup (diluar convItem, di dalam wrapper)
                const menuPopup = document.createElement('div');
                menuPopup.className = 'menu-popup';
                menuPopup.style.right = '0';
                menuPopup.style.top = '110%';
                menuPopup.style.position = 'absolute';

                // Rename (ikon saja)
                const renameBtn = document.createElement('button');
                renameBtn.type = 'button';
                renameBtn.title = 'Ganti nama';
                renameBtn.innerHTML = '<i class="fas fa-pen"></i>';
                renameBtn.addEventListener('mousedown', async (e) => {
                    e.stopPropagation();
                    menuPopup.style.display = 'none';
                    const newTitle = prompt('Masukkan judul baru:', conv.title);
                    if (newTitle !== null && newTitle.trim() !== '' && newTitle !== conv.title) {
                        await renameConversation(conv.id, newTitle.trim());
                    }
                });
                menuPopup.appendChild(renameBtn);

                // Delete (ikon saja)
                const deleteBtn = document.createElement('button');
                deleteBtn.type = 'button';
                deleteBtn.title = 'Hapus';
                deleteBtn.innerHTML = '<i class="fas fa-trash"></i>';
                deleteBtn.className = 'delete-btn';
                deleteBtn.addEventListener('mousedown', async (e) => {
                    e.stopPropagation();
                    menuPopup.style.display = 'none';
                    if (confirm('Hapus percakapan ini?')) {
                        await deleteConversation(conv.id);
                    }
                });
                menuPopup.appendChild(deleteBtn);

                // --- tombol titik tiga tetap di dalam convItem, popup di luar convItem ---
                convItem.appendChild(titleSpan);
                convItem.appendChild(menuBtn);
                wrapper.appendChild(convItem);
                wrapper.appendChild(menuPopup);
                chatHistoryList.appendChild(wrapper);

                // Tampilkan/sembunyikan menu popup
                menuBtn.addEventListener('mousedown', (e) => {
                    e.stopPropagation();
                    document.querySelectorAll('.menu-popup').forEach(p => {
                        if (p !== menuPopup) p.style.display = 'none';
                    });
                    menuPopup.style.display = menuPopup.style.display === 'block' ? 'none' : 'block';
                });

                menuPopup.addEventListener('mousedown', (e) => {
                    e.stopPropagation();
                });

                if (!window._arkSentientMenuPopupListener) {
                    document.addEventListener('mousedown', function(e) {
                        document.querySelectorAll('.menu-popup').forEach(popup => {
                            if (!popup.parentElement.contains(e.target)) {
                                popup.style.display = 'none';
                            }
                        });
                    });
                    window._arkSentientMenuPopupListener = true;
                }
            });
        }

        // Fungsi rename conversation
        async function renameConversation(conversationId, newTitle) {
            try {
                const response = await fetch('../api/rename_conversation.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ conversation_id: conversationId, new_title: newTitle })
                });
                // --- Tambahan debug: tampilkan response mentah jika gagal parse JSON ---
               let rawText = await response.text();
                let data;
                try {
                    data = JSON.parse(rawText);
                } catch (e) {
                    alert('Gagal rename!\n\nResponse tidak valid JSON:\n\n' + rawText);
                    return;
                }
                if (data.success) {
                    fetchConversations();
                } else {
                    alert('Gagal rename: ' + (data.message || 'Unknown error'));
                }
            } catch (err) {
                alert('Gagal rename: ' + err.message);
            }
        }

        // Fungsi delete conversation
        async function deleteConversation(conversationId) {
            try {
                const response = await fetch('../api/delete_conversation.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ conversation_id: conversationId })
                });
                let rawText = await response.text();
                let data;
                try {
                    data = JSON.parse(rawText);
                } catch (e) {
                    alert('Gagal hapus!\n\nResponse tidak valid JSON:\n\n' + rawText);
                    return;
                }
                if (data.success) {
                    // Jika sedang melihat conversation yang dihapus, reset tampilan
                    if (currentConversationId == conversationId) startNewChat();
                    fetchConversations();
                } else {
                    alert('Gagal hapus: ' + (data.message || 'Unknown error'));
                }
            } catch (err) {
                alert('Gagal hapus: ' + err.message);
            }
        }

        /**
         * Memuat percakapan lama dari database dan menampilkannya di area chat utama.
         * @param {number} convId - ID percakapan yang akan dimuat.
         */
        async function loadConversation(convId) {
            currentConversationId = convId;
            chatHistory = []; // Reset riwayat chat JavaScript

            chatDisplay.innerHTML = '';
            helloText.style.display = 'none';

            try {
                const response = await fetch(`../api/load_conversation.php?conversation_id=${convId}`);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();
                if (data.success) {
                    data.messages.forEach(msg => {
                        addMessageToChat(msg.sender, msg.text);
                    });
                    chatDisplay.scrollTop = chatDisplay.scrollHeight;
                } else {
                    console.error('Failed to load conversation:', data.message);
                    addMessageToChat('ai', 'Maaf, gagal memuat percakapan ini.');
                }
            } catch (error) {
                console.error('Error loading conversation:', error);
                addMessageToChat('ai', 'Maaf, terjadi kesalahan saat memuat percakapan.');
            }
        }

        /**
         * Mereset tampilan chat untuk memulai percakapan baru.
         */
        function startNewChat() {
            currentConversationId = null;
            chatHistory = [];
            chatDisplay.innerHTML = '';
            helloText.style.display = 'block';
            messageInput.value = '';
            messageInput.focus();
            // fetchConversations(); // Optional: muat ulang daftar history jika ada perubahan
        }

        // Event listener untuk tombol "New Chat" di sidebar
        newChatButton.addEventListener('click', startNewChat);

        // Panggil fetchConversations saat halaman dimuat
        document.addEventListener('DOMContentLoaded', fetchConversations);

    </script>       
</body>
</html>