<?php
// 1. Kelgan so'rovni qabul qilish
$update = json_decode(file_get_contents('php://input'), true);
$chat_id = $update['message']['chat']['id'] ?? null;
$text = $update['message']['text'] ?? '';

// Telegramga xabar yuborish funksiyasi
function sendMessage($chat_id, $text) {
    $token = "8620310081:AAF3owdfWq4A8nJR1DF3LWPonZyFCm3tr0w";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.telegram.org/bot" . $token . "/sendMessage");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, ['chat_id' => $chat_id, 'text' => $text]);
    curl_exec($ch);
    curl_close($ch);
}

// 2. Telegramdan xabar kelgan bo'lsa
if ($chat_id) {
    try {
        // pdo.php faylini shu yerga ulaymiz
        include "pdo.php";

        // --- BU YERDAN KEYIN ASOSIY BOT KODLARINGIZ YOZILADI ---
        // $pdo o'zgaruvchisi endi bemalol ishlaydi

        if ($text == "/start") {
            sendMessage($chat_id, "Ura! Bot va alohida pdo.php faylidagi Aiven bazasi muvaffaqiyatli bog'landi! 🚀");
        } else {
            sendMessage($chat_id, "Siz yozdingiz: " . $text);
        }

    } catch (PDOException $e) {
        // Agar pdo.php ulanishda xato bersa, Telegramga xabar boradi
        sendMessage($chat_id, "❌ Baza ulanishida muammo (pdo.php): " . $e->getMessage());
    }
    exit();
}
