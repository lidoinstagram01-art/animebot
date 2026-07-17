<?php
try {
    // Aiven majburiy SSL talab qilgani uchun sozlamalar
    $options = [
        PDO::MYSQL_ATTR_SSL_CA => true,
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 5 // Baza ulanmasa kod qotib qolmasligi uchun
    ];

    // Rasmda ko'rsatilgan aniq Aiven ma'lumotlaringiz
    $pdo = new PDO(
        "mysql:host=mysql-30ecd8d8-shomurodovodilbek4-a532.h.aivencloud.com;port=22610;dbname=defaultdb;charset=utf8mb4", 
        "avnadmin", 
        "AVNS_AAYSpxIB9I7WMbFWrah", 
        $options
    );

} catch (PDOException $e) {
    // Agar bot.php da xatolikni tutmoqchi bo'lsak, xatoni yuqoriga otamiz
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}
