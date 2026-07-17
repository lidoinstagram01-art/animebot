<?php
ob_start();
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// 1. Telegramdan kelgan so'rovni Render va Telegram uchun xavfsiz qabul qilish
$input = file_get_contents('php://input');
$u = json_decode($input);

if (!$u) {
    // Agar to'g'ridan-to'g'ri brauzerdan kirilsa, server tirikligini ko'rsatadi
    echo "Bot is running perfectly!";
    exit();
}

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
$b = "animebot_k6e9_bot"; 

// 2. Telegram API bilan ishlovchi yagona xavfsiz cURL funksiyasi
function bot($m, $d = []) {
    $token = "8620310081:AAF3owdfWq4A8nJR1DF3LWPonZyFCm3tr0w";
    $u = "https://api.telegram.org/bot" . $token . "/" . $m;
    $c = curl_init($u);
    curl_setopt_array($c, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => (isset($d['photo']) && $d['photo'] instanceof CURLFile) ? $d : http_build_query($d),
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT => 10
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

// 3. Jarayonni boshlaymiz
if ($cid || $ccid) {
    try {
        // Ma'lumotlar omborini ulash
        if (!file_exists("pdo.php")) {
            throw new Exception("pdo.php fayli topilmadi!");
        }
        include "pdo.php";

        $current_id = $cid ?? $ccid;
        
        // Foydalanuvchini tekshirish
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

        // Papkalarni tekshirish
        if(!is_dir("step")) mkdir("step", 0777, true);
        if(!is_dir("data")) mkdir("data", 0777, true);
        
        $step = file_exists("step/$current_id.step") ? file_get_contents("step/$current_id.step") : '';
        $share_i = file_exists("data/share.txt") ? file_get_contents("data/share.txt") : 'false';
        $situation = file_exists("data/situation.txt") ? file_get_contents("data/situation.txt") : 'On';

        if ($txt) {
            if ($situation == "Off" && $current_id != $ITACHI_UCHIHA_SONO_SHARINGAN) {
                s($current_id, "⚠️ <b>Bot vaqtincha ishlamayapti!</b>\n\n<i>Hozirda texnik ishlar olib borilmoqda. Iltimos, keyinroq urinib ko'ring.</i> ✅");
                exit();
            }
        }

        // Yordamchi funksiyalar
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
                $title = "Kanal " . ($i + 1);
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
            $start_text = file_exists("data/start.txt") ? file_get_contents("data/start.txt") : "👋 Xush kelibsiz! Botimiz orqali sevimli animelaringizni topishingiz mumkin.";
            $message_id ? e($chat_id, $message_id, $start_text, $reply_markup) : s($chat_id, $start_text, $reply_markup);
        }

        function search($type, $id, $message_id = null) {
            $txt_search = "🔎 Animeni qanday izlaymiz ?";
            $k = json_encode(['inline_keyboard' => [
                [['text'=>"🗞 Nom orqali",'callback_data'=>"SearchByName"]],
                [['text'=>"🔢 Kod orqali",'callback_data'=>"SearchByCode"],['text'=>"💎 Janr orqali",'callback_data'=>"searchByGenre"]],
                [['text' => '🔙 Ortga', 'callback_data' => 'back']]]]);
            ($type == 'e' && $message_id) ? e($id, $message_id, $txt_search, $k) : s($id, $txt_search, $k);
        }

        // =========================================================================
        // ✨ BOT LOGIKASI
        // =========================================================================

        if ($txt == "/start") {
            @unlink("step/$cid.step");
            addUser($fid);
            if (jc($cid)) {
                showMainMenu($cid);
            }
            exit();
        }

        if ($d == 'c') {
            if (jc($ccid)) {
                bot('deleteMessage', ['chat_id' => $ccid, 'message_id' => $cmid]);
                showMainMenu($ccid);
            }
            exit();
        }
        
        $back = json_encode(['inline_keyboard'=>[[['text' => '🔙 Ortga', 'callback_data' => 'back']]]]);

        if ($d == 'back') {
            @unlink("step/$ccid.step");
            showMainMenu($ccid, $cmid);
            exit();
        }

        if ($txt == "/search" || $txt == "🔎 Anime izlash") {
            if (jc($cid)) {
                search('s', $cid);
            }
            exit();
        }

        if (mb_stripos($txt, "/start ") !== false) {
            $id = trim(str_replace('/start ', '', $txt));
            if (jc($cid)) {
                if (!ctype_digit($id)) {
                    s($cid, "❌ Noto‘g‘ri anime ID!");
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
                            $caption = "🍿 <b>{$rel['name']}</b> 🍿\n";
                            $caption .= "✨───────────────✨\n";
                            $caption .= "🎥 <b>Qism:</b> {$episode['episode']} / {$rel['episode']}\n";
                            $caption .= "✨───────────────✨\n";
                            $caption .= "🎬 <b>Anime ID:</b> {$id}\n";
                            $caption .= "📜 <b>Til:</b> {$rel['language']}";

                            bot('sendVideo', [
                                'chat_id' => $cid,
                                'video' => $episode['anime'],
                                'caption' => $caption,
                                'parse_mode' => 'HTML',
                                'protect_content' => ($share_i == 'true') ? true : false,
                            ]);
                        }
                        @unlink("step/$cid.step");
                    } else {
                        s($cid, "⚠️ Bu animeda hozircha qismlar joylanmagan.");
                    }
                } else {
                    s($cid, "❌ Bu ID ostida anime topilmadi.");
                }
            }
            exit();
        }

        if($d == 'SearchByName'){
            e($ccid, $cmid, "🔎 <b>Anime nomini kiriting!</b>\n\n📌 <i>Iltimos, anime nomini aniq va xatolarsiz kiriting:</i>\n\n📝 <b>Namuna:</b> <code>Naruto</code>", $back);
            file_put_contents("step/$ccid.step", 'searchname');
            exit();
        }

        if($step == 'searchname' && $txt != ''){
            $stmt = $pdo->prepare("SELECT * FROM anime WHERE name LIKE ? LIMIT 10");
            $stmt->execute(["%$txt%"]);
            $animes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if($animes){
                foreach($animes as $anime) {
                    $caption = "📺 <b>{$anime['name']}</b> 📺\n";
                    $caption .= "━━━━━━━━━━━━━━━━━━\n";
                    $caption .= "🎭 <b>Janr:</b> {$anime['genre']}\n";
                    $caption .= "🎬 <b>Epizodlar:</b> {$anime['episode']}\n";
                    $caption .= "━━━━━━━━━━━━━━━━━━\n";
                    $caption .= "🔗 <b>Anime ID:</b> {$anime['id']}";

                    bot('sendPhoto', [
                        'chat_id' => $cid,
                        'photo' => $anime['image'],
                        'caption' => $caption,
                        'parse_mode' => 'HTML',
                        'reply_markup' => json_encode([
                            'inline_keyboard' => [
                                [['text' => "▶️ Tomosha qilish", 'url' => "https://t.me/$b?start={$anime['id']}"]]
                            ]
                        ]),
                    ]);
                }
                @unlink("step/$cid.step");
            } else {
                s($cid, "❌ <b>Anime topilmadi!</b>\n\n🔍 Boshqa nom kiritib ko'ring.", $back);
            }
            exit();
        }

        if($d == 'SearchByCode'){
            e($ccid, $cmid, "🔎 <b>Anime kodini kiriting!</b>\n\n📝 <b>Namuna:</b> <code>1</code>", $back);
            file_put_contents("step/$ccid.step", 'searchcode');
            exit();
        }

        if($step == 'searchcode' && $txt != ''){
            if (!ctype_digit($txt)) {
                s($cid, "⚠️ <b>Faqat raqam kiriting!</b>", $back);
                exit();
            }

            $stmt = $pdo->prepare("SELECT * FROM anime WHERE id = ?");
            $stmt->execute([$txt]);
            $anime = $stmt->fetch(PDO::FETCH_ASSOC);

            if($anime){
                $caption = "📺 <b>{$anime['name']}</b> 📺\n━━━━━━━━━━━━━━━━━━\n🔗 <b>Anime ID:</b> {$anime['id']}";
                bot('sendPhoto', [
                    'chat_id' => $cid,
                    'photo' => $anime['image'],
                    'caption' => $caption,
                    'parse_mode' => 'HTML',
                    'reply_markup' => json_encode([
                        'inline_keyboard' => [[['text' => "▶️ Tomosha qilish", 'url' => "https://t.me/$b?start={$anime['id']}"]]]
                    ]),
                ]);
                @unlink("step/$cid.step"); 
            } else {
                s($cid, "❌ <b>Bu kod bilan anime topilmadi!</b>", $back);
            }
            exit();
        }

        if($d == 'searchByGenre'){
            e($ccid, $cmid, "🔎 <b>Anime janrini kiriting!</b>\n\n📌 <i>Masalan: Komediya, Jangovar...</i>", $back);
            file_put_contents("step/$ccid.step", 'searchgenre');
            exit();
        }

        if($step == 'searchgenre' && $txt != ''){
            $stmt = $pdo->prepare("SELECT * FROM anime WHERE genre LIKE ? LIMIT 15");
            $stmt->execute(["%$txt%"]);
            $animes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if($animes){
                $keyboard = [];
                foreach($animes as $anime){
                    $keyboard[] = [['text' => "📺 {$anime['name']}", 'url' => "https://t.me/$b?start={$anime['id']}"]];
                }
                $keyboard[] = [['text' => '🔙 Ortga', 'callback_data' => 'back']];

                bot('sendMessage', [
                    'chat_id' => $cid,
                    'text' => "🎭 <b>{$txt}</b> janridagi animelar:",
                    'parse_mode' => 'HTML',
                    'reply_markup' => json_encode(['inline_keyboard' => $keyboard]),
                ]);
                @unlink("step/$cid.step");
            } else {
                s($cid, "❌ <b>Bu janr bo‘yicha anime topilmadi!</b>", $back);
            }
            exit();
        }

        if ($txt == "/dev") {
            s($cid, "👨‍💻 Bot dasturchisi: @ITACHI_UCHIHA_SONO_SHARINGAN", json_encode(['inline_keyboard' => [[['text' => '🔙 Ortga', 'callback_data' => 'back']]]]));
            exit();
        }

        if($txt == "💎 Premium +" || $txt == "👤 Hisobim" || $txt == "✉️ Adminga murojaat"){
            s($cid, "ℹ️ Bu bo'lim hozircha sozlanmoqda...");
            exit();
        }

    } catch (Exception $e) {
        $current_chat = $cid ?? $ccid;
        bot('sendMessage', [
            'chat_id' => $current_chat,
            'text' => "⚠️ <b>Tizimda xatolik:</b>\n<code>" . htmlspecialchars($e->getMessage()) . "</code>",
            'parse_mode' => 'html'
        ]);
    }
    exit();
}
?>
