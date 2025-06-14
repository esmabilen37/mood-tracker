<?php
// alışkanlıklar ve ruh hali ve çıkış linkleri var 
 
session_start();

// giriş kontrolü
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

// kullanıcı adını oturumdan alıyoruz
$username = $_SESSION["username"];
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kontrol Paneli</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

    <h2>Merhaba, <?= htmlspecialchars($username) ?> 👋</h2>
    <p>Bugün nasılsın? Hadi ruh halini ve alışkanlıklarını takip edelim.</p>

    <div class="mt-4">
        <a href="add_mood.php" class="btn btn-success me-2">Ruh Hali Girişi</a>
        <a href="habits.php" class="btn btn-info me-2">Alışkanlık Ekle</a>
        <a href="logout.php" class="btn btn-danger">Çıkış Yap</a>
    </div>

</body>
</html>
