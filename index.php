<?php
ob_start();

// 1. Telegramdan kelgan so'rovni Render va Telegram uchun xavfsiz qabul qilish
$u = json_decode(file_get_contents('php://input'));
$msg = $u->message ?? $u->callback_query->message ?? null;
$cid = $msg->chat->id ?? null;
$mid = $msg->message_id ?? null;
$txt = $msg->text ?? '';
$fid = $u->message->from->id ?? $u->callback_query->from->id ?? null;
$d = $u->callback_query->data ?? '';
$ccid = $u->callback_query->message->chat->id ?? $cid;
$cmid = $u->callback_query->message->message_id ?? $mid;
$cfid = $u->callback_query->from->id ?? $fid;

$photo = $msg->photo ?? null;
$file = $photo ? $photo[count($photo)-1]->file_id : null;

$ITACHI_UCHIHA_SONO_SHARINGAN = "6200478850";
$b = "animebot_k6e9_bot"; // Bu yerga botingiz usernamesini @ siz yozing

// 2. Telegram API bilan ishlovchi yagona xavfsiz cURL funksiyasi
function bot($m, $d = []) {
    $token = "8620310081:AAF3owdfWq4A8nJR1DF3LWPonZyFCm3tr0w";
    $u = "https://api.telegram.org/bot" . $token . "/" . $m;
    $c = curl_init($u);
    curl_setopt_array($c, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => (isset($d['photo']) && $d['photo'] instanceof CURLFile) ? $d : http_build_query($d)
    ]);
    $r = curl_exec($c);
    curl_close($c);
    return json_decode($r) ?: false;
}

function s($i, $t, $k = null) {
    bot('sendMessage', ['chat_id' => $i, 'text' => $t, 'reply_markup' => $k, 'parse_mode' => 'html']);
}

function e($i, $m, $t, $k = null) {
    bot('editMessageText', ['chat_id' => $i, 'message_id' => $m, 'text' => $t, 'reply_markup' => $k, 'parse_mode' => 'html']);
}

function del() {
    global $last_chat_id, $last_message_id;
    if ($last_chat_id && $last_message_id) bot('deleteMessage', ['chat_id' => $last_chat_id, 'message_id' => $last_message_id]);
}

// 3. Agar foydalanuvchidan yoki tugmadan so'rov kelsa, jarayonni boshlaymiz
if ($cid || $ccid) {
    try {
        // Ideal sozlangan pdo.php ni shu yerda ulaymiz
        include "pdo.php";

        // Ma'lumotlar omboridan foydalanuvchini tekshiramiz
        $current_id = $cid ?? $ccid;
        $stmt = $pdo->prepare("SELECT status, balance, vip_time FROM users WHERE user_id = :cid");
        $stmt->execute(['cid' => $current_id]);
        $rel = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $status = "Simple";
        $balance = 0;
        $end_vip_time = "00.00.0000 00:00";
        
        if($rel){
            $status = $rel['status'];
            $balance = $rel['balance'];
            $end_vip_time = $rel['vip_time'];
        }

        // Papka va fayllarni tekshirish
        if(!is_dir("step")) mkdir("step", 0777, true);
        if(!is_dir("data")) mkdir("data", 0777, true);
        
        $step = file_exists("step/$current_id.step") ? file_get_contents("step/$current_id.step") : '';
        
        $ads = file_exists("data/ads.txt") ? file_get_contents("data/ads.txt") : '';
        $share_i = file_exists("data/share.txt") ? file_get_contents("data/share.txt") : 'false';
        $anime_channel = file_exists("data/channel.txt") ? file_get_contents("data/channel.txt") : '@KinoLiveUz';
        $help = file_exists("data/help.txt") ? file_get_contents("data/help.txt") : '';
        $situation = file_exists("data/situation.txt") ? file_get_contents("data/situation.txt") : 'On';

        if ($txt) {
            if ($situation == "Off" && $current_id != $ITACHI_UCHIHA_SONO_SHARINGAN) {
                bot('sendMessage', [
                    'chat_id' => $current_id,
                    'text' => "⚠️ <b>Bot vaqtincha ishlamayapti!</b>\n\n<i>Hozirda texnik ishlar olib borilmoqda. Iltimos, keyinroq urinib ko'ring.</i> ✅",
                    'parse_mode' => 'HTML',
                ]);
                exit();
            }
        }

        // Qolgan funksiyalar
        function addUser($user_id) {
            global $pdo;
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE user_id = :user_id");
            $stmt->execute(['user_id' => $user_id]);
            if ($stmt->fetchColumn() == 0) {
                $stmt = $pdo->prepare("INSERT INTO users (user_id, date, status, balance, vip_time) VALUES (:user_id, NOW(), 'Simple', '0', '00.00.0000 00:00')");
                $stmt->execute(['user_id' => $user_id]);
            }
        }

        function jc($u) {
            global $pdo, $ITACHI_UCHIHA_SONO_SHARINGAN, $status;
            if($u == $ITACHI_UCHIHA_SONO_SHARINGAN || $status == "Premium +"){
                return true;
            }
            $channels = $pdo->query("SELECT channelId, channelLink FROM channels WHERE channelType = 'request'")->fetchAll(PDO::FETCH_ASSOC);
            if (!$channels) return true;

            $k = ['inline_keyboard' => []];
            $f = false;
            foreach ($channels as $i => $c) {
                $id = "-100" . $c['channelId'];
                $chatMember = bot('getChatMember', ['chat_id' => $id, 'user_id' => $u]);
                $res_status = $chatMember->result->status ?? 'left';
                $title = bot('getChat', ['chat_id' => $id])->result->title ?? explode('/', $c['channelLink'])[3];
                $k['inline_keyboard'][$i][0] = ['text' => in_array($res_status, ['creator', 'administrator', 'member']) ? "✅ $title" : "❌ $title", 'url' => $c['channelLink']];
                $f = $f || !in_array($res_status, ['creator', 'administrator', 'member']);
            }
            $k['inline_keyboard'][][0] = ['text' => '🔄 Tekshirish', 'callback_data' => 'c'];
            if ($f) {
                bot('sendMessage', ['chat_id' => $u, 'text' => "<b>⚠️Botdan to'liq foydlanish uchun kanallarga obuna bo'ling !</b>", 'reply_markup' => json_encode($k), 'parse_mode' => 'html']);
                return false;
            }
            return true;
        }

        function showMainMenu($chat_id, $message_id = null) {
            $keyboard = [[['text' => '🔎 Anime izlash']], [['text' => '💎 Premium +'], ['text' => '👤 Hisobim']], [['text' => '✉️ Adminga murojaat']]];
            $reply_markup = json_encode(['keyboard' => $keyboard, 'resize_keyboard' => true]);
            $start_text = file_exists("data/start.txt") ? file_get_contents("data/start.txt") : '❄️';
            $message_id ? e($chat_id, $message_id, $start_text, $reply_markup) : s($chat_id, $start_text, $reply_markup);
        }

        function showAdminPanel($chat_id, $message_id = null) {
            $k = json_encode(['inline_keyboard' => [
                [['text' => '🔧 Asosiy sozlamlar', 'callback_data' => 'main_settings']],
                [['text' => '🎥 Anime sozlamlari', 'callback_data' => 'anime_settings'],['text' => '📝 Post tayyorlash', 'callback_data' => 'createPost']],
                [['text' => '📣 Kanallar', 'callback_data' => 'channel_settings'],['text' => '📈 Statistika', 'callback_data' => 'stats']],
                [['text' => '✉️ Xabar yuborish', 'callback_data' => 'sendMessage']],
                [['text' => "👥 Foydalanuvchilarni boshqarish ", 'callback_data' => 'user_settings']]
            ]]);
            $message = "👨‍💼 <b>Admin panelga xush kelibsiz</b>\nBu yerda botni boshqarishingiz mumkin.";
            $message_id ? e($chat_id, $message_id, $message, $k) : s($chat_id, $message, $k);
        }

        function search($type, $id, $message_id = null) {
            $txt_search = "🔎 Animeni qanday izlaymiz ?";
            $k = json_encode(['inline_keyboard' => [
                [['text'=>"🗞 Nom orqali",'callback_data'=>"SearchByName"]],
                [['text'=>"🔢 Kod orqali",'callback_data'=>"SearchByCode"],['text'=>"💎 Janr orqali",'callback_data'=>"searchByGenre"]],
                [['text' => '🔙 Ortga', 'callback_data' => 'back']]]]);
            ($type == 'e' && $message_id) ? e($id, $message_id, $txt_search, $k) : s($id, $txt_search, $k);
            exit();
        }

        function sendToUser($admin_id, $user_id, $message) {
            if (!in_array($message, ['/start', '/panel', '/admin', '/search', '/rek', '/help', '/dev'])) {
                bot('sendMessage', ['chat_id' => $user_id, 'text' => $message, 'parse_mode' => 'html']);
                s($admin_id, "✅ Xabar muvaffaqiyatli yuborildi!");
            } else {
                s($admin_id, "❌ Komanda yuborish mumkin emas!");
            }
        }

        function broadcastMessage($admin_id, $message, $is_forward = false, $forward_mid = null) {
            global $pdo;
            if (!$is_forward && in_array($message, ['/start', '/panel', '/admin', '/search', '/rek', '/help', '/dev'])) {
                s($admin_id, "❌ Komanda yuborish mumkin emas!");
                return;
            }

            $users = $pdo->query("SELECT user_id FROM users")->fetchAll(PDO::FETCH_COLUMN);
            $total = count($users);
            $sent = 0;

            $msg_status = bot('sendMessage', ['chat_id' => $admin_id, 'text' => "Xabar yuborish boshlandi:\nXabar yuborilmoqda...\nYuborildi: 0/$total"]);
            $msg_id = $msg_status->result->message_id;

            foreach ($users as $user_id) {
                if ($is_forward && $forward_mid) {
                    bot('forwardMessage', ['chat_id' => $user_id, 'from_chat_id' => $admin_id, 'message_id' => $forward_mid]);
                } else {
                    bot('sendMessage', ['chat_id' => $user_id, 'text' => $message, 'parse_mode' => 'html']);
                }
                $sent++;
                if ($sent % 15 == 0 || $sent == $total) {
                    e($admin_id, $msg_id, "Xabar yuborish boshlandi:\nXabar yuborilmoqda...\nYuborildi: $sent/$total");
                    usleep(50000); 
                }
            }
            e($admin_id, $msg_id, "Xabar yuborish yakunlandi:\nYuborildi: $sent/$total");
        }

        // =========================================================================
        // 🌟 BOT SHARTLARI VA LOGIKALARI BOSHLANISHI 🌟
        // =========================================================================

        if ($txt == "/start") {
            unlink("step/$cid.step");
            addUser($fid);
            jc($cid) && showMainMenu($cid);
            exit();
        }
        
        $back = json_encode([
            'inline_keyboard'=>[
                [['text' => '🔙 Ortga', 'callback_data' => 'back']]
            ]
        ]);

        if (($txt == "/search" || $txt == "🔎 Anime izlash") && jc($cid) == 1) {
            if ($txt == "🔎 Anime izlash") {
                search('s', $cid);
            } else {
                search('e', $ccid, $cmid);
            }
            exit();
        }

        if ((mb_stripos($txt, "/start ") !== false) && jc($cid) == 1) {
            $id = str_replace('/start ', '', $txt);
            if (jc($ccid) == 1) {
                if (!ctype_digit($id)) {
                    s($ccid, "Noto‘g‘ri anime ID!");
                    exit();
                }

                $stmt = $pdo->prepare("SELECT * FROM anime WHERE id = :id");
                $stmt->execute(['id' => $id]);
                $rel = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($rel) {
                    $stmt = $pdo->prepare("SELECT * FROM anime_ep WHERE anime_id = :id ORDER BY episode ASC");
                    $stmt->execute(['id' => $id]);
                    $epizodlar = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    if ($epizodlar) {
                        foreach ($epizodlar as $episode) {
                            $caption = "🍿 *{$rel['name']}* 🍿\n";
                            $caption .= "✨───────────────✨\n";
                            $caption .= "🎥 *Qism:* {$episode['episode']} / {$rel['episode']}\n";
                            $caption .= "✨───────────────✨\n";
                            $caption .= "🎬 *Anime ID:* {$id}\n";
                            $caption .= "📜 *Til:* {$rel['language']}";

                            bot('sendVideo', [
                                'chat_id' => $ccid,
                                'video' => $episode['anime'],
                                'caption' => $caption,
                                'parse_mode' => 'Markdown',
                                'protect_content' => $share_i,
                                'reply_markup'=>json_encode([
                                    'inline_keyboard'=>[
                                        [['text'=>"Dasturchi",'url'=>"https://t.me/ITACHI_UCHIHA_SONO_SHARINGAN"]],
                                    ]
                                ]),
                            ]);
                        }
                        unlink("step/$ccid.step");
                    } else {
                        s($ccid, "Bu animeda qismlar topilmadi!");
                        exit();
                    }
                } else {
                    s($ccid, "Bu anime topilmadi!");
                }
            }
        }

        if($d == 'SearchByName'){
            e($ccid, $cmid, "🔎 <b>Anime nomini kiriting!</b>\n\n📌 <i>Iltimos, anime nomini aniq va xatolarsiz kiriting:</i>\n\n📝 <b>Namuna:</b> <code>Naruto Shippuden</code>");
            file_put_contents("step/$ccid.step", 'searchname');
        }

        if($step == 'searchname'){
            if($txt != ''){
                $stmt = $pdo->prepare("SELECT * FROM anime WHERE name LIKE ?");
                $stmt->execute(["%$txt%"]);
                $animes = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if($animes){
                    foreach($animes as $anime){
                        $caption = "📺 <b>{$anime['name']}</b> 📺\n";
                        $caption .= "━━━━━━━━━━━━━━━━━━\n";
                        $caption .= "🎭 <b>Janr:</b> {$anime['genre']}\n";
                        $caption .= "🎬 <b>Epizodlar:</b> {$anime['episode']}\n";
                        $caption .= "📝 <b>Tavsif:</b> <i>{$anime['description']}</i>\n";
                        $caption .= "━━━━━━━━━━━━━━━━━━\n";
                        $caption .= "🔗 <b>Anime ID:</b> {$anime['id']}";

                        bot('sendPhoto', [
                            'chat_id' => $cid,
                            'photo' => $anime['image'],
                            'caption' => $caption,
                            'parse_mode' => 'HTML',
                            'reply_markup' => json_encode([
                                'inline_keyboard' => [
                                    [['text' => "▶️ Tomosha qilish", 'url' => "https://t.me/$b?start={$anime['id']}"]],
                                    [['text'=>"Dasturchi",'url'=>"https://t.me/ITACHI_UCHIHA_SONO_SHARINGAN"]]
                                ]
                            ]),
                        ]);
                    }
                } else {
                    s($cid, "❌ <b>Anime topilmadi!</b>\n\n🔍 Iltimos, anime nomini to‘g‘ri kiriting yoki boshqa variantni sinab ko‘ring.", "html");
                }
            }
            unlink("step/$cid.step");
        }

        if($d == 'SearchByCode'){
            e($ccid, $cmid, "🔎 <b>Anime kodini kiriting!</b>\n\n📌 <i>Iltimos, anime kodini faqat raqamlarda kiriting:</i>\n\n📝 <b>Namuna:</b> <code>99</code>",$back);
            file_put_contents("step/$cid.step", 'searchcode');
        }

        if($step == 'searchcode'){
            if($txt != ''){
                if (!ctype_digit($txt)) {
                    s($cid, "⚠️ <b>Faqat raqam kiriting!</b>\n\n🔢 Namuna: <code>99</code>",$back);
                    exit();
                }

                $stmt = $pdo->prepare("SELECT * FROM anime WHERE id = ?");
                $stmt->execute([$txt]);
                $anime = $stmt->fetch(PDO::FETCH_ASSOC);

                if($anime){
                    $caption = "📺 <b>{$anime['name']}</b> 📺\n";
                    $caption .= "━━━━━━━━━━━━━━━━━━\n";
                    $caption .= "🎭 <b>Janr:</b> {$anime['genre']}\n";
                    $caption .= "🎬 <b>Epizodlar:</b> {$anime['episode']}\n";
                    $caption .= "📝 <b>Tavsif:</b> <i>{$anime['description']}</i>\n";
                    $caption .= "━━━━━━━━━━━━━━━━━━\n";
                    $caption .= "🔗 <b>Anime ID:</b> {$anime['id']}";

                    bot('sendPhoto', [
                        'chat_id' => $cid,
                        'photo' => $anime['image'],
                        'caption' => $caption,
                        'parse_mode' => 'HTML',
                        'reply_markup' => json_encode([
                            'inline_keyboard' => [
                                [['text' => "▶️ Tomosha qilish", 'url' => "https://t.me/$b?start={$anime['id']}"]],
                                [['text'=>"Dasturchi",'url'=>"https://t.me/ITACHI_UCHIHA_SONO_SHARINGAN"]]
                            ]
                        ]),
                    ]);
                    unlink("step/$cid.step"); 
                } else {
                    s($cid, "❌ <b>Anime topilmadi!</b>\n\n🔍 Iltimos, anime kodini to‘g‘ri kiriting yoki boshqa variantni sinab ko‘ring.", "html");
                }
            }
        }

        if($d == 'searchByGenre'){
            e($ccid, $cmid, "🔎 <b>Anime janrini kiriting!</b>\n\n📌 <i>Masalan: Action, Comedy, Drama...</i>",$back);
            file_put_contents("step/$cid.step", 'searchgenre');
        }

        if($step == 'searchgenre'){
            if($txt != ''){
                $stmt = $pdo->prepare("SELECT * FROM anime WHERE genre LIKE ? LIMIT 10");
                $stmt->execute(["%$txt%"]);
                $animes = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if($animes){
                    $keyboard = [];
                    foreach($animes as $anime){
                        $keyboard[] = [['text' => "📺 {$anime['name']}", 'callback_data' => "anime_{$anime['id']}"]];
                    }

                    bot('sendMessage', [
                        'chat_id' => $cid,
                        'text' => "🎭 <b>{$txt}</b> janriga mos animelar:\n\n📌 <i>Pastdan tanlang:</i>",
                        'parse_mode' => 'HTML',
                        'reply_markup' => json_encode(['inline_keyboard' => $keyboard]),
                    ]);
                    unlink("step/$cid.step");
                } else {
                    s($cid, "❌ <b>Bu janr bo‘yicha anime topilmadi!</b>\n\n🔍 Iltimos, boshqa janr kiriting.", "html");
                }
            }
        }

        if(strpos($d, "anime_") !== false){
            $anime_id = str_replace("anime_", "", $d);
            $stmt = $pdo->prepare("SELECT * FROM anime WHERE id = ?");
            $stmt->execute([$anime_id]);
            $anime = $stmt->fetch(PDO::FETCH_ASSOC);

            if($anime){
                $caption = "📺 <b>{$anime['name']}</b> 📺\n";
                $caption .= "━━━━━━━━━━━━━━━━━━\n";
                $caption .= "🎭 <b>Janr:</b> {$anime['genre']}\n";
                $caption .= "🎬 <b>Epizodlar:</b> {$anime['episode']}\n";
                $caption .= "📝 <b>Tavsif:</b> <i>{$anime['description']}</i>\n";
                $caption .= "━━━━━━━━━━━━━━━━━━\n";
                $caption .= "🔗 <b>Anime ID:</b> {$anime['id']}";

                bot('sendPhoto', [
                    'chat_id' => $cid,
                    'photo' => $anime['image'],
                    'caption' => $caption,
                    'parse_mode' => 'HTML',
                    'reply_markup' => json_encode([
                        'inline_keyboard' => [
                            [['text' => "▶️ Tomosha qilish", 'url' => "https://t.me/$b?start={$anime['id']}"]],
                            [['text'=>"Dasturchi",'url'=>"https://t.me/ITACHI_UCHIHA_SONO_SHARINGAN"]]
                        ]
                    ]),
                ]);
            }
        }

        if ($txt == "/dev" && jc($cid) == 1) {
            s($cid, "👨‍💻 Bot dasturchisi: @ITACHI_UCHIHA_SONO_SHARINGAN\nShuncha mexnatimni hurmat qilasizlar degan umi
