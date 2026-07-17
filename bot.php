<?php
// 1. Kelgan so'rovni xavfsiz qabul qilish
$update = json_decode(file_get_contents('php://input'), true);

// 2. Chat ID va matnni ajratib olish
$chat_id = $update['message']['chat']['id'] ?? null;
$text = $update['message']['text'] ?? '';

// 3. Agar Telegramdan so'rov kelgan bo'lsa, darhol javob qaytarish
if ($chat_id) {
    $token = "8620310081:AAF3owdfWq4A8nJR1DF3LWPonZyFCm3tr0w";
    $reply = "Salom! Men ishlayapman. Siz yozdingiz: " . $text;
    
    // cURL orqali xavfsiz yuborish
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.telegram.org/bot" . $token . "/sendMessage");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, [
        'chat_id' => $chat_id,
        'text' => $reply
    ]);
    curl_exec($ch);
    curl_close($ch);
    
    exit();
}
