<?php
try {
    // PDO ulanishini sozlash
    $pdo = new PDO("mysql:host=mysql-30ecd8d8-shomurodovodilbek4-a532.h.aivencloud.com;dbname=defaultdb", "avnadmin", "AVNS_AAYSpxIB9I7WMbFWrah");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("SET NAMES 'utf8mb4'");
} catch (PDOException $e) {
    die("❌ Ma'lumotlar bazasiga ulanishda xatolik: " . $e->getMessage());
}
?>
