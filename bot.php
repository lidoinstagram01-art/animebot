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
            "🔔 <b>Yangi anime qo‘shilganda sizga avtomatik bildirishnoma yuboramiz!</b> 📨\n\n".
            "📅 <b>1 oylik 💎 Premium + obunasini sotib olish uchun pastdagi tugmani bosing:</b> 👇", 
        json_encode([
            'inline_keyboard' => [
                [['text' => "💳 30 kun 🕐 5.000 UZS", 'callback_data' => 'plus_vip']],
                [['text' => "🔙 Ortga qaytish", 'callback_data' => 'back']]
            ]
        ]));
    } elseif($status == "Premium +") {
        s($cid, "🎉 Siz allaqachon <b>💎 Premium +</b> tarifiga obuna bo‘lgansiz! 🎊\n\n".
            "📅 <b>Obunangizning tugash muddati:</b> $end_vip_time 📆\n".
            "⛔ Obunani uzaytirish yoki bekor qilish imkoniyati mavjud emas.");
    }
    exit();
}

if ($d == "plus_vip") {
    if ($balance >= 5000) {
        $new_balance = $balance - 5000;
        $vip_time = date("Y-m-d H:i:s", strtotime("+30 days"));

        $stmt = $pdo->prepare("UPDATE users SET balance = :new_balance, status = :status, vip_time = :vip_time WHERE user_id = :ccid");
        $stmt->execute([
            'new_balance' => $new_balance,
            'status' => 'Premium +',
            'vip_time' => $vip_time,
            'ccid' => $ccid
        ]);

        e($ccid, $cmid, "✅ Siz muvaffaqiyatli <b>Premium +</b> tarif obunasiga o'tdingiz! Balansingiz yangilandi. 🏆\n\n📅 Obunangiz tugash sanasi: <b>$vip_time</b>");
    } else {
        e($ccid, $cmid, "⚠️ Sizning balancingizda yetarlicha mablag' mavjud emas! Iltimos, hisobingizni to'ldiring.");
    }
    exit();
}



if($txt == "👤 Hisobim"){
    s($cid,"🧑‍💻 <b>Sizning shaxsiy hisobingiz</b> 🏦\n\n".
        "💰 <b>Balansingiz:</b> $balance UZS 💵\n".
        "🆔 <b>ID raqamingiz:</b> <code>$cid</code>\n".
        "💎 <b>Statusingiz:</b> $status 🏅", json_encode([
        'inline_keyboard' => [
            [['text' => "➕ Pul kiritish 💳", 'callback_data' => 'plus_money']],
            [['text' => "🔙 Ortga qaytish", 'callback_data' => 'back']]
        ],
    ]));
}


if($d == "plus_money" || $txt == "/pay"){
    if($d == 'plus_money'){
        e($ccid,$cmid,"💳 <b>Botga pul kiritish</b> 💰\n\n📌 Pastdagi ko'rsatilgan karta raqamiga kerakli summada pul tashlang va \"✅ To'lov qildim\" tugmasini bosing.\n📎 Qancha miqdorda pul kiritganingizni va to'lov chekini ko'rsatilgan tarzda yuboring.\n\n💳 <b>Karta raqami:</b>\n<code>4073420044765125</code>\n👤 <b>Karta egasi:</b> <b>Rahmatillo B.</b>",json_encode([
        'inline_keyboard'=>[
            [['text'=>"✅ To'lov qildim",'callback_data'=>'payed']],
            [['text'=>"🔙 Ortga",'callback_data'=>"back"]],
        ]
    ]));
    } else {
        s($cid,"💳 <b>Botga pul kiritish</b> 💰\n\n📌 Pastdagi ko'rsatilgan karta raqamiga kerakli summada pul tashlang va \"✅ To'lov qildim\" tugmasini bosing.\n📎 Qancha miqdorda pul kiritganingizni va to'lov chekini ko'rsatilgan tarzda yuboring.\n\n💳 <b>Karta raqami:</b>\n<code>4073420044765125</code>\n👤 <b>Karta egasi:</b> <b>Rahmatillo B.</b>",json_encode([
        'inline_keyboard'=>[
            [['text'=>"✅ To'lov qildim",'callback_data'=>'payed']],
            [['text'=>"🔙 Ortga",'callback_data'=>"back"]],
        ]
    ]));
    }
    exit();
}
if($d == 'payed'){
    e($cid,$mid,"💰 <b>Qancha miqdorda to'lov qilganingizni kiriting!</b> ✍️",json_encode([
        'inline_keyboard'=>[
            [['text'=>"🔙 Ortga",'callback_data'=>"back"]],
        ]
    ]));
    file_put_contents("step/$cid.step",'payed');
    exit();
}

if($step == 'payed'){
    if(isset($txt)){
        file_put_contents("step/$cid.money",$txt);
        s($cid,"📸 <b>To'lov chekini rasm ko'rinishida yuboring!</b> 🖼️",json_encode([
            'inline_keyboard'=>[
                [['text'=>"🔙 Ortga️",'callback_data'=>"back"]],
            ]
        ]));
        file_put_contents("step/$cid.step",'image');
    }
    exit();
}

if($step == 'image'){
    $miqdor = file_get_contents("step/$cid.money");

    if(isset($msg->photo) && is_array($msg->photo)){
        $photo = end($msg->photo)->file_id;

        bot('sendMessage', [
            'chat_id' => $cid,
            'text' => "✅ <b>To'lovni tasdiqlash arizasi muvaffaqiyatli adminga yuborildi!</b>",
            'parse_mode' => "HTML"
        ]);

        bot('sendPhoto', [
            'chat_id' => $ITACHI_UCHIHA_SONO_SHARINGAN, 
            'photo' => $photo,
            'caption' => "👤 <b>Foydalanuvchi:</b> $cid 🆔\n💰 <b>To'lov summasi:</b> $miqdor UZS 💵\n📩 <b>To'lovni tasdiqlash uchun ariza yubordi.</b>\n✅ <b>To'lovni tasdiqlaysizmi?</b>",
            'parse_mode' => "HTML",
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => "✅ Tasdiqlash", 'callback_data' => 'correct_pay'], 
                     ['text' => "❌ Bekor qilish", 'callback_data' => 'incorrect_pay']],
                     [['text' => '🔙 Ortga', 'callback_data' => 'back']]
                ]
            ])
        ]);

        unlink("step/$cid.step");
        unlink("step/$cid.money");
    }
    exit();
}

if($txt == "✉️ Adminga murojaat" || $d == 'add_ques'){
    if($d == 'add_ques'){
        s($ccid, "Adminga yubormoqchi bo'lgan xabaringizni kiriting!", $back);
    } else {
        s($cid, "Adminga yubormoqchi bo'lgan xabaringizni kiriting!", $back);
    }
    file_put_contents("step/$cid.step", 'admin_s');
    exit();
}

if($step == 'admin_s'){
    if(!empty($txt)){
        bot('sendMessage', [
            'chat_id' => $ITACHI_UCHIHA_SONO_SHARINGAN,
            'text' => "📩 Yangi xabar:\n\n👤 Foydalanuvchi ID: <b>$cid</b>\n💬 Xabar: $txt",
            'parse_mode' => "HTML",
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => "✍️ Javob berish", 'callback_data' => "answer_user|$cid"]]
                ]
            ])
        ]);
        s($cid,"✅ Xabaringiz adminga yuborildi. Tez orada javob olasiz!");
        unlink("step/$cid.step");
    } else {
        s($cid, "⚠️ Iltimos, faqat matnlardan foydalaning!");
    }
    exit();
}

if(isset($d) && strpos($d, 'answer_user|') !== false){
    $user_id = explode("|", $d)[1]; 
    file_put_contents("step/$cfid.step", "admin_reply|$user_id");
    
    bot('sendMessage', [
        'chat_id' => $cfid,
        'text' => "✍️ Foydalanuvchiga javob yozing:",
        'parse_mode' => "HTML"
    ]);
}

if(strpos($step, "admin_reply|") !== false){
    $user_id = explode("|", $step)[1];

    if(!empty($txt)){
        bot('sendMessage', [
            'chat_id' => $user_id,
            'text' => "📩 <b>Admin javobi:</b>\n\n$txt",
            'parse_mode' => "HTML",
            'reply_markup'=>json_encode([
                'inline_keyboard'=>[
                    [['text'=>"Qo'shimcha savol berish",'callback_data'=>'add_ques']],
                    ]
                ]),
        ]);

        bot('sendMessage', [
            'chat_id' => $cfid,
            'text' => "✅ Javob foydalanuvchiga yuborildi!",
            'parse_mode' => "HTML"
        ]);

        unlink("step/$cfid.step"); // Adminning holatini tozalash
    } else {
        bot('sendMessage', [
            'chat_id' => $cfid,
            'text' => "⚠️ Iltimos, javob yozing!",
            'parse_mode' => "HTML"
        ]);
    }
}



if ($d == "c" && jc($cfid) == 1) {
    del();
    jc($cfid) && showMainMenu($ccid, $cmid);
    exit();
}

if ($step == 'search_anime' && jc($cid) == 1) {
    if(isset($txt)){
        if (is_numeric($txt)) {
            $stmt = $pdo->prepare("SELECT * FROM anime WHERE id = :id");
            $stmt->execute(['id' => $txt]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                bot('sendPhoto', [
                    'chat_id' => $cid,
                    'photo' => $result['image'],
                    'caption' => "📺 <b>{$result['name']}</b>\n━━━━━━━━━━━━━━━━━━\n🎭 <b>Janr:</b> {$result['genre']}\n🎬 <b>Epizodlar:</b> {$result['episode']}\n📝 <b>Tavsif:</b> <i>{$result['description']}</i>\n━━━━━━━━━━━━━━━━━━\n🔗 <b>Anime ID:</b> {$result['id']}",
                    'parse_mode' => 'HTML',
                    'reply_markup' => json_encode([
                        'inline_keyboard' => [
                            [['text' => "▶️ Tomosha qilish", 'url' => "https://t.me/$b?start={$result['id']}"]],
                            [['text'=>"Dasturchi",'url'=>"https://t.me/ITACHI_UCHIHA_SONO_SHARINGAN"]]
                        ]
                    ]),
                ]);
            } else {
                s($cid, "❌ Bunday ID bilan anime topilmadi!");
            }
        } 
        else {
            $stmt = $pdo->prepare("SELECT * FROM anime WHERE name LIKE :name LIMIT 10");
            $stmt->execute(['name' => "%$txt%"]);
            $animes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($animes) {
                $keyboard = [];
                foreach ($animes as $anime) {
                    $keyboard[] = [['text' => "📺 {$anime['name']}", 'callback_data' => "anime_{$anime['id']}"]];
                }

                bot('sendMessage', [
                    'chat_id' => $cid,
                    'text' => "🔎 <b>{$txt}</b> bo‘yicha topilgan animelar:\n\n📌 <i>Pastdan tanlang:</i>",
                    'parse_mode' => 'HTML',
                    'reply_markup' => json_encode(['inline_keyboard' => $keyboard]),
                ]);
            } else {
                s($cid, "❌ <b>Bunday nom bilan anime topilmadi!</b>");
            }
        }
        unlink("step/$cid.step");
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


// Admin Panel 
if (in_array($txt, ["/admin", "/panel"])) {
    if ($fid == $ITACHI_UCHIHA_SONO_SHARINGAN) {
        showAdminPanel($cid, null);
        exit();
    } else {
        s($cid, "❌ Siz admin emassiz!");
        exit();
    }
}

if ($d == "stats" && $cfid == $ITACHI_UCHIHA_SONO_SHARINGAN) {
    $user_count = $pdo->query("SELECT COUNT(*) as user_count FROM users")->fetch(PDO::FETCH_ASSOC)['user_count'];
    $ping = sys_getloadavg()[0];
    e($ccid, $cmid, "💡 <b>O'rtacha ping: <i>$ping</i></b>\n\n👤 Barcha Foydalanuvchilar: $user_count", json_encode(['inline_keyboard' => [[['text' => '🔙 Ortga', 'callback_data' => 'back']]]]));
    exit();
}

if ($d == 'anime_settings' && $cfid == $ITACHI_UCHIHA_SONO_SHARINGAN) {
    $k = json_encode(['inline_keyboard' => [
        [['text' => '🎥 Anime qo\'shish', 'callback_data' => 'add_anime']],
        [['text' => '📀 Qism qo\'shish', 'callback_data' => 'add-episode']],
        [['text' => '💡 Anime sozlash', 'callback_data' => 'edit-movie']],
        [['text' => '🔙 Ortga', 'callback_data' => 'back']]
    ]]);
    e($ccid, $cmid, "O'zingizga kerakli bo'lgan menuni tanlang", $k);
    exit();
}

if ($d == 'channel_settings' && $cfid == $ITACHI_UCHIHA_SONO_SHARINGAN) {
    $k = json_encode(['inline_keyboard' => [
        [['text' => '🔐 Majburiy obunalar', 'callback_data' => 'mandatory_subscriptions']],
        [['text' => '🎥 Anime kanal', 'callback_data' => 'anime_channel']],
        [['text' => '🔙 Ortga', 'callback_data' => 'back']]
    ]]);
    e($ccid, $cmid, "O'zingizga kerakli bo'lgan menuni tanlang", $k);
    exit();
}

if ($d == 'main_settings' && $cfid == $ITACHI_UCHIHA_SONO_SHARINGAN) {
    $file_share = file_get_contents("data/share.txt");
    if ($file_share == 'true') {
        $share = "✅  Uzatishni yoqish";
    } elseif ($file_share == 'false') {
        $share = "❌ Uzatishni o'chirish";
    }

$k = json_encode(['inline_keyboard' => [
    [['text' => $share, 'callback_data' => 'share']],
    [['text' => "📝 Hozirgi holat", 'callback_data' => 'now_about'],['text'=>"🤖 Bot holati",'callback_data'=>'bot_info']],
    [['text' => '🔙 Ortga', 'callback_data' => 'back']]
]]);


    e($ccid, $cmid, "⚙️ Asosiy sozlamalar bo'limiga xush kelibsiz !", $k);
    exit();
}

if($d == 'ads_text'){
    e($ccid,$cmid,"/rek komandasi uchun reklama matnini kiriting !");
    file_put_contents("step/$ccid.step",ads_text);
    exit();
}
if($step == 'ads_text'){
    if(isset($txt)){
        file_put_contents("data/ads.txt",$txt);
    }
    s($cid,"✅ Reklama matni muvoffaqatli qabul qilindi !");
    exit();
}
if($d == 'help_text'){
    e($ccid,$cmid,"/help komandasi uchun Yordam matnini kiriting !");
    file_put_contents("step/$ccid.step",help_text);
    exit();
}
if($step == 'help_text'){
    if(isset($txt)){
        file_put_contents("data/help.txt",$txt);
    }
    s($cid,"✅ Yordam matni muvoffaqatli qabul qilindi !");
    exit();
}
if($d == 'share'){
    $file_share = file_get_contents("data/share.txt");
    if ($file_share == 'true') {
        file_put_contents("data/share.txt",'false');
    e($ccid,$cmid,"Kino uzatish muvoffaqatli o'chirildi ✅",json_encode(['inline_keyboard'=>[[['text' => '🔙 Ortga', 'callback_data' => 'back']]]]));
    } elseif ($file_share == 'false') {
        file_put_contents("data/share.txt",'true');
    e($ccid,$cmid,"Kino uzatish muvoffaqatli yoqildi ✅",json_encode(['inline_keyboard'=>[[['text' => '🔙 Ortga', 'callback_data' => 'back']]]]));
    }
    exit();
}
if ($d == 'now_about') {
if($share_i == 'false'){
    $uzatish = "Yoqilgan ✅";
} elseif($share_id == 'true'){
    $uzatish = "O'chirilgan ❌";
}
e($ccid, $cmid, "<b>Bot ma'lumotlari:</b>\n\n<b>🎥 Kino kanal:</b><i>$anime_channel</i> ",json_Encode(['inline_keyboard'=>[[['text'=>"Dasturchi",'url'=>"https://t.me/ITACHI_UCHIHA_SONO_SHARINGAN"]],[['text' => '🔙 Ortga', 'callback_data' => 'back']]]]));
$anime_channel = nl2br(file_get_contents("data/channel.txt"));
}
$situation = file_get_contents("data/situation.txt");
if($situation == 'On'){
    $bot_holat = 'Yoqilgan ✅';
} elseif($situation == 'Off'){
    $bot_holat = "O'chirilgan ❌";
}
if($situation == 'On'){
    $bot_situation = "O'chirish ❌";
} elseif($situation == 'Off'){
    $bot_situation = 'Yoqish ✅';
}

if($d == 'bot_info'){
    e($ccid,$cmid,"<b>Bot holati:</b> <i>$bot_holat</i>",json_encode([
        'inline_keyboard'=>[
            [['text'=>$bot_situation,'callback_data'=>'bot_change']],
            [['text' => '🔙 Ortga', 'callback_data' => 'back']],
            ],
        ]));
}
if($d == 'bot_change'){
    if($situation == 'On'){
    file_put_contents("data/situation.txt",'Off');
    e($ccid,$cmid,"Bot muvoffaqatli o'chirildi ✅",json_encode([
        'inline_keyboard'=>[
            [['text'=>$bot_situation,'callback_data'=>'bot_change']],
            ],
        ]));
} elseif($situation == 'Off'){
    file_put_contents("data/situation.txt",'On');
    e($ccid,$cmid,"Bot muvoffaqatli yoqildi ✅",json_encode([
        'inline_keyboard'=>[
            [['text'=>$bot_situation,'callback_data'=>'bot_change']],
            [['text' => '🔙 Ortga', 'callback_data' => 'back']]
            ],
        ]));
}
}

if($d == 'anime_channel'){
    e($ccid,$cmid,"Post yuboriladigan kino kanal userini kiriting\n<b>Na'muna: @KinoLiveUz</b>",json_encode([
            'inline_keyboard'=>[
                [['text' => '🔙 Ortga', 'callback_data' => 'back']],
                ],
            ]));
        file_put_contents("step/$ccid.step",'anime_channel');
        exit();
}

if ($step == 'anime_channel') {
    if (isset($txt) && empty($d)) { 
        file_put_contents("data/channel.txt", $txt);
        s($cid, "✅ Kino kanal muvaffaqiyatli qo'shildi!");
        unlink("step/$cid.step"); 
    }
    exit();
}

if ($d == 'mandatory_subscriptions') {
    e($ccid, $cmid, "O'zingizga kerakli tugmalardan birini tanlang", json_encode([
        'inline_keyboard' => [
            [['text' => "➕ Qo'shish", 'callback_data' => 'add_m_channel']],
            [['text' => "📃 Ro'yxat", 'callback_data' => 'list_m_channels'], ['text' => "🗑 O'chirish", 'callback_data' => 'delete_m_channel']],
            [['text' => '🔙 Ortga', 'callback_data' => 'back']],
        ]
    ]));
}

if ($d == 'add_m_channel') {
    e($ccid, $cmid, "Kanal ID sini kiriting (-100 qo'ymasdan)");
    file_put_contents("step/$ccid.step", "chann1");
}

if ($step == 'chann1' && !empty($txt)) {
    file_put_contents("step/$ccid.channel_id", $txt);
    s($cid, "Kanal havolasini kiriting (https://t.me bilan)");
    file_put_contents("step/$ccid.step", "chann2");
}

if ($step == 'chann2' && !empty($txt)) {
    $channelId = file_get_contents("step/$ccid.channel_id");
    $channelLink = $txt;
    $stmt = $pdo->prepare("INSERT INTO channels (channelId, channelLink, channelType) VALUES (?, ?, 'request')");
    $stmt->execute([$channelId, $channelLink]);
    s($cid, "✅ Kanal muvaffaqiyatli qo'shildi!");
    unlink("step/$ccid.step");
    unlink("step/$ccid.channel_id");
}

if ($d == 'list_m_channels') {
    $stmt = $pdo->query("SELECT id, channelLink FROM channels WHERE channelType = 'request'");
    $channels = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!$channels) {
        e($ccid, $cmid, "📭 Majburiy obuna kanallari yo'q!");
        exit();
    }
    
    $text = "📃 Majburiy obuna kanallari:\n\n";
    foreach ($channels as $i => $channel) {
        $text .= ($i + 1) . ". <a href='" . $channel['channelLink'] . "'>Kanal</a>\n";
    }
    e($ccid, $cmid, $text, json_encode(['parse_mode' => 'HTML']));
}

if ($d == 'delete_m_channel') {
    $stmt = $pdo->query("SELECT id, channelId, channelLink FROM channels WHERE channelType = 'request'");
    $channels = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!$channels) {
        e($ccid, $cmid, "📭 O'chirish uchun kanal yo'q!");
        exit();
    }
    
    $buttons = [];
    $text = "🗑 O'chirish uchun kanallarni tanlang:\n\n";
    
    foreach ($channels as $i => $channel) {
        $text .= ($i + 1) . ". <b>" . htmlspecialchars($channel['channelLink']) . "</b>\n"; 
        $buttons[] = [['text' => (string) ($i + 1), 'callback_data' => 'del_m_channel_' . $channel['id']]];
    }
    
    e($ccid, $cmid, $text, json_encode(['inline_keyboard' => $buttons]), 'html');
}

if (strpos($d, 'del_m_channel_') === 0) {
    $channelId = str_replace('del_m_channel_', '', $d);
    $stmt = $pdo->prepare("DELETE FROM channels WHERE id = ?");
    $stmt->execute([$channelId]);
    e($ccid, $cmid, "✅ Kanal muvaffaqiyatli o'chirildi!");
}

if ($d == 'add_anime') {
    e($cid, $cmid, "🎥 Anime nomini kiriting:");
    file_put_contents("step/$cid.step", "anime_name");
}

if ($step == 'anime_name') {
    if (!empty($txt)) {
        file_put_contents("data/anime_name_$cid.txt", $txt);
        s($cid, "💀 Anime qismlar sonini kiriting:");
        file_put_contents("step/$cid.step", "anime_episode");
    } else {
        s($cid, "❌ Iltimos, anime nomini kiriting!");
    }
}

if ($step == 'anime_episode') {
    if (!empty($txt) && is_numeric($txt)) {
        file_put_contents("data/anime_episode_$cid.txt", $txt);
        s($cid, "🌍 Anime chiqarilgan davlatni kiriting:");
        file_put_contents("step/$cid.step", "anime_country");
    } else {
        s($cid, "❌ Iltimos, faqat son kiriting!");
    }
}

if ($step == 'anime_country') {
    if (!empty($txt)) {
        file_put_contents("data/anime_country_$cid.txt", $txt);
        s($cid, "🔦 Anime tilini kiriting:");
        file_put_contents("step/$cid.step", "anime_language");
    } else {
        s($cid, "❌ Iltimos, anime chiqarilgan davlatni kiriting!");
    }
}

if ($step == 'anime_language') {
    if (!empty($txt)) {
        file_put_contents("data/anime_language_$cid.txt", $txt);
        s($cid, "📜 Anime tavsifini kiriting:");
        file_put_contents("step/$cid.step", "anime_description");
    } else {
        s($cid, "❌ Iltimos, anime tilini kiriting!");
    }
}

if ($step == 'anime_description') {
    if (!empty($txt)) {
        file_put_contents("data/anime_description_$cid.txt", $txt);
        s($cid, "🎭 Anime janrini kiriting:");
        file_put_contents("step/$cid.step", "anime_genre");
    } else {
        s($cid, "❌ Iltimos, anime tavsifini kiriting!");
    }
}

if ($step == 'anime_genre') {
    if (!empty($txt)) {
        file_put_contents("data/anime_genre_$cid.txt", $txt);
        s($cid, "🖼 Anime rasmi yuboring:");
        file_put_contents("step/$cid.step", "anime_image");
    } else {
        s($cid, "❌ Iltimos, anime janrini kiriting!");
    }
}

if ($step == 'anime_image') {
    if (isset($msg->photo)) {
        $file_id = $msg->photo[count($msg->photo)-1]->file_id;
        $name = file_get_contents("data/anime_name_$cid.txt");
        $episode = file_get_contents("data/anime_episode_$cid.txt");
        $country = file_get_contents("data/anime_country_$cid.txt");
        $language = file_get_contents("data/anime_language_$cid.txt");
        $description = file_get_contents("data/anime_description_$cid.txt");
        $genre = file_get_contents("data/anime_genre_$cid.txt");
        $create_at = date("Y-m-d H:i:s");
        $status = 'ongoing';
        $rating = 0.0;
        $views = 0;

        try {
            $stmt = $pdo->prepare("INSERT INTO anime (name, episode, country, language, image, description, genre, rating, status, views, create_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $episode, $country, $language, $file_id, $description, $genre, $rating, $status, $views, $create_at]);
            $anime_id = $pdo->lastInsertId();

            unlink("data/anime_name_$cid.txt");
            unlink("data/anime_episode_$cid.txt");
            unlink("data/anime_country_$cid.txt");
            unlink("data/anime_language_$cid.txt");
            unlink("data/anime_description_$cid.txt");
            unlink("data/anime_genre_$cid.txt");
            unlink("step/$cid.step");

            s($cid, "<b>✅ Anime muvaffaqiyatli qo‘shildi!</b>\n\n"
                . "🎮 <b>Anime nomi:</b> $name\n"
                . "💀 <b>Qismlar:</b> $episode\n"
                . "🌍 <b>Davlat:</b> $country\n"
                . "🔦 <b>Til:</b> $language\n"
                . "📜 <b>Tavsif:</b> $description\n"
                . "🎭 <b>Janr:</b> $genre\n"
                . "⭐ <b>Reyting:</b> $rating\n"
                . "🕒 <b>Yaratilgan vaqt:</b> $create_at\n\n"
                . "🄚 <b>Anime kodi:</b> <code>$anime_id</code>");
        } catch (PDOException $e) {
            s($cid, "<b>⚠️ Xatolik!</b>\n\n<code>" . $e->getMessage() . "</code>");
            unlink("data/anime_name_$cid.txt");
            unlink("data/anime_episode_$cid.txt");
            unlink("data/anime_country_$cid.txt");
            unlink("data/anime_language_$cid.txt");
            unlink("data/anime_description_$cid.txt");
            unlink("data/anime_genre_$cid.txt");
        }
    } else {
        s($cid, "❌ Iltimos, anime rasmini yuboring!");
    }
    exit();
}

if ($d == "add-episode") {
    del();
    e($ccid, $cmid, "<b>🔢 Anime ID sini kiriting:</b>");
    file_put_contents("step/$ccid.step", "anime-code");
}

if ($step == "anime-code") {
    if (is_numeric($txt)) {
        $txt = $pdo->quote($txt);
        file_put_contents("step/test.txt", $txt);
        s($cid, "<b>🎥 Ushbu anime uchun epizod videosini yuboring:</b>");
        file_put_contents("step/$cid.step", "anime-video");
        exit();
    }
}

if ($step == "anime-video") {
    $msg = json_decode(json_encode($msg), true); 
    
    if (isset($msg['video'])) {  
        $file_id = $msg['video']['file_id']; 
        $id = trim(file_get_contents("step/test.txt"), "'"); 
        
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM anime_ep WHERE anime_id = ?");
        $stmt->execute([$id]);
        $count = $stmt->fetchColumn();

        $qismi = ($count > 0) ? $count + 1 : 1;

        $stmt = $pdo->prepare("INSERT INTO anime_ep (anime_id, anime, episode) VALUES (?, ?, ?)");
        if ($stmt->execute([$id, $file_id, $qismi])) {
            s($cid, "<b>✅ $id raqamli anime uchun $qismi-qism yuklandi!</b>\n\n<i>Keyingi epizodni yuklash uchun shunchaki yana yuboring</i>");
        } else {
            s($cid, "<b>⚠️ Xatolik!</b>\n\n<code>" . implode(" ", $stmt->errorInfo()) . "</code>");
            unlink("step/$cid.step");
            unlink("step/test.txt");
            exit();
        }
    } else {
        s($cid, "Iltimos, faqat video yuboring!");
    }
    exit();
}




if ($d == "edit-movie") {
    e($ccid, $cmid, "<b>Tahrirlamoqchi bo'lgan movieni tanlang:</b>", json_encode([
        'inline_keyboard' => [
            [['text' => "Anime ma'lumotlarini", 'callback_data' => "editType-movies"]],
            [['text' => "Anime qismini", 'callback_data' => "editType-movie_datas"]]
        ]
    ]));
    file_put_contents("step/$ccid.step", "edit-movie");
}

if (mb_stripos($d, "editType-") !== false) {
    $ex = explode("-", $d)[1];
    file_put_contents("step/$ccid.tip", $ex);
    e($ccid, $cmid, "<b>Kino kodini kiriting:</b>");
    file_put_contents("step/$ccid.step", "edit-movies");
}

if ($step == "edit-movies") {
    $tip = file_get_contents("step/$ccid.tip");

    $stmt = $pdo->prepare("SELECT * FROM movie WHERE id = :id");
    $stmt->execute(['id' => $txt]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        if ($tip == "movies") {
            $kb = json_encode([
                'inline_keyboard' => [
                    [['text' => "Nomini tahrirlash", 'callback_data' => "editmovie-nom-$txt"]],
                    [['text' => "Qismini tahrirlash", 'callback_data' => "editmovie-qismi-$txt"]],
                    [['text' => "Davlatini tahrirlash", 'callback_data' => "editmovie-davlat-$txt"]],
                    [['text' => "Tilini tahrirlash", 'callback_data' => "editmovie-tili-$txt"]],
                    [['text' => "Yilini tahrirlash", 'callback_data' => "editmovie-yili-$txt"]],
                    [['text' => "Janrini tahrirlash", 'callback_data' => "editmovie-janri-$txt"]],
                ]
            ]);
            e($ccid, $cmid, "<b>❓ Nimani tahrirlamoqchisiz?</b>", $kb);
        } else {
            e($ccid, $cmid, "<b>Qism raqamini yuboring:</b>");
            file_put_contents("step/$ccid.step", "movie-epEdit=$txt");
        }
    } else {
        s($ccid, "❗ movie mavjud emas, qayta urinib ko'ring!");
    }
}

if (mb_stripos($step, "movie-epEdit=") !== false) {
    $ex = explode("=", $step);
    $id = $ex[1];

    $stmt = $pdo->prepare("SELECT * FROM movie_datas WHERE id = :id AND qism = :qism");
    $stmt->execute(['id' => $id, 'qism' => $txt]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $kb = json_encode([
            'inline_keyboard' => [
                [['text' => "movie kodini tahrirlash", 'callback_data' => "editEpisode-id-$id-$txt"]],
                [['text' => "Qismini tahrirlash", 'callback_data' => "editEpisode-qism-$id-$txt"]],
                [['text' => "Videoni tahrirlash", 'callback_data' => "editEpisode-file_id-$id-$txt"]],
            ]
        ]);
        e($ccid, $cmid, "<b>❓ Nimani tahrirlamoqchisiz?</b>", $kb);
    } else {
        s($ccid, "❗ Ushbu movieda $txt-qism mavjud emas, qayta urinib ko'ring.");
    }
}

if (mb_stripos($d, "editmovie-") !== false) {
    e($ccid, $cmid, "<b>Yangi qiymatini kiriting:</b>");
    file_put_contents("step/$ccid.step", $d);
}

if (mb_stripos($step, "editmovie-") !== false) {
    $ex = explode("-", $step);
    $tip = $ex[1];
    $id = $ex[2];

    if ($tip == "qismi" || $tip == "yili") {
        if (is_numeric($txt)) {
            $stmt = $pdo->prepare("UPDATE movielar SET `$tip` = :val WHERE id = :id");
            $stmt->execute(['val' => $txt, 'id' => $id]);
            s($ccid, "✅ Saqlandi.");
        } else {
            s($ccid, "❗ Faqat raqamlardan foydalaning.");
        }
    } else {
        $stmt = $pdo->prepare("UPDATE movielar SET `$tip` = :val WHERE id = :id");
        $stmt->execute(['val' => $txt, 'id' => $id]);
        s($ccid, "✅ Saqlandi.");
    }
    unlink("step/$ccid.step");
}

if (mb_stripos($d, "editEpisode-") !== false) {
    e($ccid, $cmid, "<b>Yangi qiymatini kiriting:</b>");
    file_put_contents("step/$ccid.step", $d);
}

if (mb_stripos($step, "editEpisode-") !== false) {
    $ex = explode("-", $step);
    $tip = $ex[1];
    $id = $ex[2];
    $qism_raqami = $ex[3];

    if ($tip == "file_id") {
        if (isset($message->video)) {
            $file_id = $message->video->file_id;
            $stmt = $pdo->prepare("UPDATE movie_datas SET `file_id` = :file_id WHERE id = :id AND qism = :qism");
            $stmt->execute(['file_id' => $file_id, 'id' => $id, 'qism' => $qism_raqami]);
            s($ccid, "✅ Saqlandi.");
        } else {
            s($ccid, "❗ Faqat videodan foydalaning.");
        }
    } else {
        if (is_numeric($txt)) {
            $stmt = $pdo->prepare("UPDATE movie_datas SET `$tip` = :val WHERE id = :id AND qism = :qism");
            $stmt->execute(['val' => $txt, 'id' => $id, 'qism' => $qism_raqami]);
            s($ccid, "✅ Saqlandi.");
        } else {
            s($ccid, "❗ Faqat raqamlardan foydalaning.");
        }
    }
    unlink("step/$ccid.step");
}

if ($d == 'createPost') {
    e($ccid, $cmid, "📺 Anime ID sini kiriting:");
    file_put_contents("step/$ccid.step", "anime_post_create");
    exit();
}

if ($step == 'anime_post_create') {
    $id = trim($txt);
    $stmt = $pdo->prepare("SELECT * FROM anime WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $anime = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($anime !== false && !empty($anime)) {
        $caption = "📺 *{$anime['name']}* 📺\n";
        $caption .= "┏━━━━━━━━━━━━━━━┓\n";
        $caption .= "┃ 🎞 *Qismlar:* {$anime['episode']}\n";
        $caption .= "┃ 🌍 *Davlat:* {$anime['country']}\n";
        $caption .= "┃ 🗣 *Til:* {$anime['language']}\n";
        $caption .= "┃ 🔖 *Janr:* {$anime['genre']}\n";
        $caption .= "┃ ⭐ *Reyting:* {$anime['rating']}\n";
        $caption .= "┗━━━━━━━━━━━━━━━┛\n";
        $caption .= "📌 *Tavsif:* {$anime['description']}";

        bot('sendPhoto', [
            'chat_id' => $ccid,
            'photo' => $anime['image'],
            'caption' => $caption,
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => "$anime_channel ga yuborish", 'callback_data' => "sendPost1=$id"]],
                ]
            ]),
        ]);
        exit();
    } elseif(empty($anime)) {
       if(!isset($d)){
            s($ccid, "❌ Ushbu ID bilan anime topilmadi, qayta urinib ko‘ring!");
       }
    }
}

if (mb_stripos($d, "sendPost1=") !== false) {
    $id = str_replace("sendPost1=", "", $d);
    $stmt = $pdo->prepare("SELECT * FROM anime WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $anime = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($anime !== false && !empty($anime)) {
        $caption = "📺 *{$anime['name']}* 📺\n";
        $caption .= "┏━━━━━━━━━━━━━━━┓\n";
        $caption .= "┃ 🎞 *Qismlar:* {$anime['episode']}\n";
        $caption .= "┃ 🌍 *Davlat:* {$anime['country']}\n";
        $caption .= "┃ 🗣 *Til:* {$anime['language']}\n";
        $caption .= "┃ 🔖 *Janr:* {$anime['genre']}\n";
        $caption .= "┃ ⭐ *Reyting:* {$anime['rating']}\n";
        $caption .= "┗━━━━━━━━━━━━━━━┛\n";
        $caption .= "📌 *Tavsif:* {$anime['description']}";

        bot('sendPhoto', [
            'chat_id' => $anime_channel,
            'photo' => $anime['image'],
            'caption' => $caption,
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => "🔷 Tomosha qilish 🔷", 'url' => "https://t.me/$b?start=$id"]],
                ]
            ]),
        ]);
        s($ccid, "✅ Postingiz muvaffaqiyatli kanalga yuborildi!");
        exit();
    } else {
        s($ccid, "❌ Ushbu ID bilan anime topilmadi!");
    }
    unlink("step/$ccid.step");
    exit();
}
if ($d == 'sendMessage' && $cfid == $ITACHI_UCHIHA_SONO_SHARINGAN) {
    $k = json_encode(['inline_keyboard' => [
        [['text' => '👤 Userga', 'callback_data' => 'user_message']],
        [['text' => '👥 Oddiy xabar', 'callback_data' => 'simple_message']],
        [['text' => '📩 Forward xabar', 'callback_data' => 'forward_message']],
        [['text' => '🔙 Ortga', 'callback_data' => 'back']]
    ]]);
    e($ccid, $cmid, "O'zingizga kerakli xabar turini tanlang !:", $k);
    exit();
}

if ($d == "user_message" && $cfid == $ITACHI_UCHIHA_SONO_SHARINGAN) {
    e($ccid,$cmid, "Foydalanuvchi ID sini kiriting:");
    file_put_contents("step/$ccid.step", 'waiting_user_id');
    exit();
}

if ($step == 'waiting_user_id' && $fid == $ITACHI_UCHIHA_SONO_SHARINGAN && $txt) {
    file_put_contents("step/$cid.step", "waiting_message_$txt");
    s($cid, "Foydalanuvchiga yuboriladigan xabarni kiriting:");
    exit();
}

if (preg_match('/waiting_message_(\d+)/', $step, $matches) && $fid == $ITACHI_UCHIHA_SONO_SHARINGAN && $txt) {
    $user_id = $matches[1];
    sendToUser($cid, $user_id, $txt);
    unlink("step/$cid.step");
    exit();
}

if ($d == "simple_message" && $cfid == $ITACHI_UCHIHA_SONO_SHARINGAN) {
    e($ccid,$cmid, "Hamma foydalanuvchilarga yuboriladigan xabarni kiriting:");
    file_put_contents("step/$ccid.step", 'waiting_broadcast');
    exit();
}

if ($step == 'waiting_broadcast' && $fid == $ITACHI_UCHIHA_SONO_SHARINGAN && $txt) {
    broadcastMessage($cid, $txt);
    unlink("step/$cid.step");
    exit();
}

if ($d == "forward_message" && $cfid == $ITACHI_UCHIHA_SONO_SHARINGAN) {
    e($ccid,$cmid, "Forward qilinadigan xabarni yuboring:");
    file_put_contents("step/$ccid.step", 'waiting_forward_message');
    exit();
}

if ($step == 'waiting_forward_message' && $fid == $ITACHI_UCHIHA_SONO_SHARINGAN) {
    $forward_mid = $mid;
    broadcastMessage($cid, null, true, $forward_mid);
    unlink("step/$cid.step");
    exit();
}

if ($d == 'back') {
    unlink("step/$ccid.step");
    ($cfid == $ITACHI_UCHIHA_SONO_SHARINGAN) ? showAdminPanel($ccid, $cmid) : showMainMenu($ccid, $cmid);
    exit();
}
?>