<?php

session_start();
// giriş kontrolü
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// veritabanı bağlantısı sağlıyoruz
$mysqli = new mysqli("localhost", "root", "", "mood_tracker");
if ($mysqli->connect_errno) {
    die("Veritabanı bağlantı hatası: " . $mysqli->connect_error);
}

// kullanıcı ruh halini girdiğinde tarihiyle birlikte kaydetmek için
$stmt = $mysqli->prepare("SELECT mood, note, date FROM mood_entries WHERE user_id = ? ORDER BY date DESC");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Ruh Hali Geçmişi</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">
    <h2>Ruh Hali Geçmişi</h2>
    <a href="dashboard.php" class="btn btn-secondary mb-3">Geri</a>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Tarih</th>
                <th>Ruh Hali</th>
                <th>Not</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                   <td><?= htmlspecialchars($row['date']) ?></td>
                    <td><?= htmlspecialchars($row['mood']) ?></td>
                    <td><?= nl2br(htmlspecialchars($row['note'])) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>

<?php
$stmt->close();
$mysqli->close();
?>
