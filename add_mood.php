<?php

session_start();

// giriş kontrolü
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// form gönderildiğinde
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mood = $_POST['mood'];
    $note = $_POST['note'];

    // veritabanı bağlantısı
    $mysqli = new mysqli("localhost", "root", "", "mood_tracker");
    if ($mysqli->connect_errno) {
        die("Veritabanı bağlantı hatası: " . $mysqli->connect_error);
    }

    // yeni ruh hali kaydını eklemek için
    $stmt = $mysqli->prepare("INSERT INTO mood_entries (user_id, mood, note, date) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iss", $_SESSION['user_id'], $mood, $note);
    $stmt->execute();
    $stmt->close();
    $mysqli->close();

    header("Location: mood_list.php"); // kaydettikten sonra liste sayfasına yönlendirmek için
    exit();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Ruh Hali Girişi</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">
    <h2>Günlük Ruh Hali Girişi</h2>
    <form method="post" action="">
        <div class="mb-3">
            <label for="mood" class="form-label">Ruh Haliniz</label>
            <select name="mood" id="mood" class="form-select" required>
                <option value="">Seçiniz</option>
                <option value="Mutlu 😊">Mutlu 😊</option>
                <option value="Üzgün 😢">Üzgün 😢</option>
                <option value="Stresli 😰">Stresli 😰</option>
                <option value="Heyecanlı 😃">Heyecanlı 😃</option>
                <option value="Yorgun 😴">Yorgun 😴</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="note" class="form-label">Not (isteğe bağlı)</label>
            <textarea name="note" id="note" class="form-control" rows="3"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Kaydet</button>
        <a href="dashboard.php" class="btn btn-secondary">Geri</a>
    </form>
</body>
</html>
