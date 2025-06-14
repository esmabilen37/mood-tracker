<?php
 
// veritabanı bağlantı bilgileri
$host = "localhost";
$dbname = "mood_tracker";
$username = "root";
$password = "";

// PDO ile mysql bağlantısı kurma ve hata kontrolü sağlıyoruz
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}
?>
