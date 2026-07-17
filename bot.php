<?php
ob_start();
define('API_TOKEN', '8620310081:AAF3owdfWq4A8nJR1DF3LWPonZyFCm3tr0w');
$ITACHI_UCHIHA_SONO_SHARINGAN = "6200478850";
/*
Ushbu kod 30.03.2025 10:10 da @ITACHI_UCHIHA_SONO_SHARINGAN  tomonidan tarqatildi kod mukammal tarzda tuzilgan ammo bu kodda muammolar mavjud !
Muammolar tog'irlangan kod @UZ_IT_CENTER kanalining VIP kanaliga joylanadi !

Manba: @UZ_IT_CENTER
Dasturchi: @ITACHI_UCHIHA_SONO_SHARINGAN

Foydalanuvchi Qulayliklari:
1. Anime izlash
  ● Nom orqali
  ● Kod orqali
  ● Janr orqali
2. Premium + obunasi
  ● Majburiy obuna so'ralmaydi
  ● Anime yuklash va anime uzatish har doim ochiq
  ● Bot tezligi 2x tezroq ishlaydi 
  ● Yangi anime qo'shilganda xabar yuboriladi 
  ● 30 kunlik narx 5.000 UZS
3 Hisobim
  ● Balance ni kuzatish
  ● Id raqamni ko'rish
  ● Status qandayligini bilish
4. Adminga murojaat
  ● Har qanday vaziyatda ham adminga murojat qila olish
Admin uchun qulayliklar:
1. Asosiy sozlamlar
  ● Uzatishni o'chirish yoki yoqish
  ● Hozirgi holatni kuzatish
  ● Botni vaqtinchalik to'xtatish yoki yoqish
2. Anime sozlamari
  ● Anime qo'shish
  ● Qism qo'shish
  ● Animelarni tahrirlash
3. Post tayyorlash
  ● Anime kanalga postni yuborish
4. Kanallar
  ● Majburiy obuna
    ● Qo'shish
    ● Ro'yhatni kuzatish 
    ● O'chirish
  ● Anime kanal
    ● Qo'shish
5. Statistika
  ● Botning o'rtacha tezligini kuzatish
  ● Barcha foydalanuvchilar sonini kuzatish
6. Xabar yuborish
  ● Userga
  ● Oddiy xabar
  ● Forwerd xabar
7. Foydalanuvchilarni boshqarish
  ● Pul qo'shish
  ● Pul ayirish
  ● Statusni o'zgartirish
  ● Adminlar
    ● Qo'shish
    ● Ro'yhat
    ● O'chirish
Doimiy qulayliklar:
1. Ortga tugamasi
  ● Tugma orqali doim ortga qayta olish imkoniyati !
Botdagi xatoliklar:
Foydalanuvchi uchun
1. Premium + 1 marta ulanilsa xech qachon tugamaydi !
Admin uchun noqulayliklar
1. Asosil sozlamlar
  ● Start matnini kiritish imkonsiz 
2. Anime sozlamlari
  ● Anime tahrirlash tuzatilmagan
3. Xabar yuborish
  ● Faqat 5.000 ta foydalanuvchigacha xech qanday qotishlarsiz xabar yuboradi 5.000 tdan keyin xabar yuborish ishlamasligi mumkun !
4. Statistika
  ● Bugun qo'shilgan foydalanuvchilarni kuzatish imkonsiz 
5. Foydalanuvchilarni boshqarish
  ● Pul kiritish ishlamaydi 
  ● Pul ayirish ishlamaydi
  ● Status o'zgartirish ishlamaydi
  ● Adminlar
    ● Qo'shish ishlamaydi
    ● Ro'yhat ishlamaydi
    ● O'chirish ishlamaydi
*/
require_once "pdo.php";

// <-- @ITACHI_UCHIHA_SONO_SHARINGAN --> \\
function bot($m, $d = []) {
    $u = "https://api.telegram.org/bot" . API_TOKEN . "/" . $m;
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
$b = bot('getme')->result->username;
$ads = file_get_contents("data/ads.txt");
$share_i = file_get_contents("data/share.txt");
$anime_channel = file_get_contents("data/channel.txt");
$help = file_get_contents("data/help.txt");
$situation = file_get_contents("data/situation.txt");
$photo = $msg->photo;
$file = $photo[count($photo)-1]->file_id;

if (!$cid || !$fid) exit;

if($cid) {
    $stmt = $pdo->prepare("SELECT status, balance, vip_time FROM users WHERE user_id = :cid");
        $stmt->execute(['cid' => $cid]);
        $rel = $stmt->fetch(PDO::FETCH_ASSOC);
        if($rel){
            $status = $rel['status'];
            $balance = $rel['balance'];
            $end_vip_time = $rel['vip_time'];
        }
} 

mkdir("step", 0777, true);
mkdir("data", 0777, true);
$step = file_get_contents("step/$cid.step") ?: '';

if ($txt) {
    if ($situation == "Off") {
        if ($cid == $ITACHI_UCHIHA_SONO_SHARINGAN) {
        } else {
            bot('sendMessage', [
                'chat_id' => $cid,
                'text' => "⚠️ <b>Bot vaqtincha ishlamayapti!</b>\n\n".
                          "<i>Hozirda texnik ishlar olib borilmoqda. Iltimos, keyinroq urinib ko'ring.</i> ✅",
                'parse_mode' => 'HTML',
            ]);
            exit();
        }
    }
}


function addUser($user_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $exists = $stmt->fetchColumn();

    if ($exists == 0) {
        $stmt = $pdo->prepare("INSERT INTO users (user_id, date, status,balance,vip_time) VALUES (:user_id, NOW(),'Simple','0','00.00.0000 00:00')");
        $stmt->execute(['user_id' => $user_id]);
    }
}

function jc($u) {
    global $pdo, $ITACHI_UCHIHA_SONO_SHARINGAN,$status;
    if($u == $ITACHI_UCHIHA_SONO_SHARINGAN || $status == "Premium +"){
        return true;
    }
    $channels = $pdo->query("SELECT channelId, channelLink FROM channels WHERE channelType = 'request'")->fetchAll(PDO::FETCH_ASSOC);
    if (!$channels) return true;

    $k = ['inline_keyboard' => []];
    $f = false;
    foreach ($channels as $i => $c) {
        $id = "-100" . $c['channelId'];
        $status = bot('getChatMember', ['chat_id' => $id, 'user_id' => $u])->result->status ?? 'left';
        $title = bot('getChat', ['chat_id' => $id])->result->title ?? explode('/', $c['channelLink'])[3];
        $k['inline_keyboard'][$i][0] = ['text' => in_array($status, ['creator', 'administrator', 'member']) ? "✅ $title" : "❌ $title", 'url' => $c['channelLink']];
        $f = $f || !in_array($status, ['creator', 'administrator', 'member']);
    }
    $k['inline_keyboard'][][0] = ['text' => '🔄 Tekshirish', 'callback_data' => 'c'];
    if ($f) {
        bot('sendMessage', ['chat_id' => $u, 'text' => "<b>⚠️Botdan to'liq foydlanish uchun kanallarga obuna bo'ling !</b>", 'reply_markup' => json_encode($k), 'parse_mode' => 'html']);
        return false;
    }
    return true;
}

function showMainMenu($chat_id, $message_id = null) {
    $keyboard = [
        ['🔎 Anime izlash'],
        ['💎 Premium +', '👤 Hisobim'], 
        ['✉️ Adminga murojaat']       
    ];

    $reply_markup = json_encode([
        'keyboard' => $keyboard,
        'resize_keyboard' => true,
        'one_time_keyboard' => false 
    ]);
$start_text = file_get_contents("data/start.txt");
    if ($message_id) {
        e($chat_id, $message_id, $start_text, $reply_markup);
    } else {
        s($chat_id, $start_text, $reply_markup);
    }
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
    $txt = "🔎 Animeni qanday izlaymiz ?";
    $k = json_encode(['inline_keyboard' => [
        [['text'=>"🗞 Nom orqali",'callback_data'=>"SearchByName"]],
        [['text'=>"🔢 Kod orqali",'callback_data'=>"SearchByCode"],['text'=>"💎 Janr orqali",'callback_data'=>"searchByGenre"]],
        [['text' => '🔙 Ortga', 'callback_data' => 'back']]]]);
    ($type == 'e' && $message_id) ? e($id, $message_id, $txt, $k) : s($id, $txt, $k);
    exit();
}
function sendToUser($ITACHI_UCHIHA_SONO_SHARINGAN_id, $user_id, $message) {
    if (!in_array($message, ['/start', '/panel', '/admin', '/search', '/rek', '/help', '/dev'])) {
        bot('sendMessage', ['chat_id' => $user_id, 'text' => $message, 'parse_mode' => 'html']);
        s($ITACHI_UCHIHA_SONO_SHARINGAN_id, "✅ Xabar muvaffaqiyatli yuborildi!");
    } else {
        s($ITACHI_UCHIHA_SONO_SHARINGAN_id, "❌ Komanda yuborish mumkin emas!");
    }
}

function broadcastMessage($ITACHI_UCHIHA_SONO_SHARINGAN_id, $message, $is_forward = false, $forward_mid = null) {
    global $pdo;
    if (!$is_forward && in_array($message, ['/start', '/panel', '/admin', '/search', '/rek', '/help', '/dev'])) {
        s($ITACHI_UCHIHA_SONO_SHARINGAN_id, "❌ Komanda yuborish mumkin emas!");
        return;
    }

    $users = $pdo->query("SELECT user_id FROM users")->fetchAll(PDO::FETCH_COLUMN);
    $total = count($users);
    $sent = 0;

    $msg = bot('sendMessage', ['chat_id' => $ITACHI_UCHIHA_SONO_SHARINGAN_id, 'text' => "Xabar yuborish boshlandi:\nXabar yuborilmoqda...\nYuborildi: 0/$total"]);
    $msg_id = $msg->result->message_id;

    foreach ($users as $user_id) {
        if ($is_forward && $forward_mid) {
            bot('forwardMessage', ['chat_id' => $user_id, 'from_chat_id' => $ITACHI_UCHIHA_SONO_SHARINGAN_id, 'message_id' => $forward_mid]);
        } else {
            bot('sendMessage', ['chat_id' => $user_id, 'text' => $message, 'parse_mode' => 'html']);
        }
        $sent++;
        if ($sent % 15 == 0 || $sent == $total) {
            e($ITACHI_UCHIHA_SONO_SHARINGAN_id, $msg_id, "Xabar yuborish boshlandi:\nXabar yuborilmoqda...\nYuborildi: $sent/$total");
            usleep(50000); 
        }
    }
    e($ITACHI_UCHIHA_SONO_SHARINGAN_id, $msg_id, "Xabar yuborish yakunlandi:\nYuborildi: $sent/$total");
}

if(file_exists("data/situation.txt")==false){
file_put_contents("data/situation.txt","On");
}
if(file_exists("data/channel.txt")==false){
file_put_contents("data/channel.txt","@KinoLiveUz");
}
if(file_exists("data/share.txt")==false){
file_put_contents("data/share.txt","false");
}
if(file_exists("data/start.txt")==false){
file_put_contents("data/start.txt","❄️");
}




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
        // file_put_contents("step/$ccid.step", 'search_anime');
    if ("$txt == 🔎 Anime izlash") {
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
    if(isset($txt)){
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
    if(isset($txt)){
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
    if(isset($txt)){
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

            unlink("step/$cid.step"); // Faqat anime topilganda o‘chirish
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
    jc($cid) && s($cid, "👨‍💻 Bot dasturchisi: @ITACHI_UCHIHA_SONO_SHARINGAN\nShuncha mexnatimni hurmat qilasizlar degan umiddaman!", json_encode(['inline_keyboard' => [[['text' => '🔙 Ortga', 'callback_data' => 'back']]]]));
    exit();
}

if($txt == "💎 Premium +"){
    if($status == 'Simple'){
        s($cid, "<i>❌ Siz hali <b>💎 Premium +</b> tarifiga obuna bo‘lmadingiz! ❌</i>\n\n".
            "🔥 <b>Premium + tarifining qulayliklari:</b> 🔥\n".
            "✅ <b>Majburiy kanalga obuna yo‘q!</b> 🚀\n".
            "📥 <b>Anime yuklash va ulashish doim ochiq!</b> 🆓\n".
            "⚡ <b>Bot tezligi 2x marta oshadi!</b> ⚡\n".
            "🔔 <b>Yangi anime qo‘shilganda sizga avtomatik bildirishnoma yuboramiz
