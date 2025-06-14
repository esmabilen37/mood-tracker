<?php

 // kullanıcı sadece kendi alışkanlıklarını görebilir ve düzenleyebilir
 
session_start();
// giriş kontrolü
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// veritabanı bağlantısı ve kullanıcı idsi
$mysqli = new mysqli("localhost", "root", "", "mood_tracker");
$user_id = $_SESSION['user_id'];

// post metoduyla gelen title değeri kontrol edilip veritabanına ekleniyor
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['title'])) {
    $title = trim($_POST['title']);
    if (!empty($title)) {
        $stmt = $mysqli->prepare("INSERT INTO habits (user_id, title) VALUES (?, ?)");
        $stmt->bind_param("is", $user_id, $title);
        $stmt->execute();
        $stmt->close();
    }
}

// alışkanlık silmek için
if (isset($_GET['delete'])) {
    $habit_id = intval($_GET['delete']);
    $stmt = $mysqli->prepare("DELETE FROM habits WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $habit_id, $user_id);
    $stmt->execute();
    $stmt->close();
}

// kullanıcının alışkanlıklarını getirmek için
$stmt = $mysqli->prepare("SELECT * FROM habits WHERE user_id = ? ORDER BY title ASC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Alışkanlıklar</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        .action-buttons {
            white-space: nowrap;
        }
        .suggestions {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .suggestion-item {
            cursor: pointer;
            padding: 5px 10px;
            margin: 2px;
            display: inline-block;
            background-color: #e9ecef;
            border-radius: 15px;
            font-size: 0.9em;
            transition: all 0.2s;
        }
        .suggestion-item:hover {
            background-color: #dee2e6;
        }
    </style>
</head>
<body class="container mt-5">
    <h2>Alışkanlıklar</h2>

    <div class="suggestions mb-4">
        <h5 class="mb-3">💡 Örnek Alışkanlık Önerileri:</h5>
        <div>
            <span class="suggestion-item" onclick="fillHabit(this)">📚 Kitap okuma</span>
            <span class="suggestion-item" onclick="fillHabit(this)">🏃‍♂️ Egzersiz yapma</span>
            <span class="suggestion-item" onclick="fillHabit(this)">💧 Su içme</span>
            <span class="suggestion-item" onclick="fillHabit(this)">🧘‍♀️ Meditasyon</span>
            <span class="suggestion-item" onclick="fillHabit(this)">🥗 Dengeli beslenme</span>
            <span class="suggestion-item" onclick="fillHabit(this)">✍️ Günlük yazma</span>
            <span class="suggestion-item" onclick="fillHabit(this)">🎨 Yeni hobi edinme</span>
            <span class="suggestion-item" onclick="fillHabit(this)">🌙 Düzenli uyku</span>
            <span class="suggestion-item" onclick="fillHabit(this)">🧹 Oda düzenleme</span>
            <span class="suggestion-item" onclick="fillHabit(this)">📱 Telefon kullanımını azaltma</span>
        </div>
    </div>

    <form method="post" class="mb-3">
        <div class="input-group">
            <input type="text" name="title" id="habitInput" class="form-control" placeholder="Yeni alışkanlık ekle" required>
            <button type="submit" class="btn btn-primary">Ekle</button>
        </div>
    </form>

    <div class="list-group">
        <?php while ($habit = $result->fetch_assoc()): ?>
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1"><?= htmlspecialchars($habit['title']) ?></h5>
                </div>
                <div class="action-buttons">
                    <a href="habit_calendar.php?habit_id=<?= $habit['id'] ?>" class="btn btn-sm btn-info me-2">Takvim</a>
                    <a href="edit_habit.php?id=<?= $habit['id'] ?>" class="btn btn-sm btn-primary me-2">Düzenle</a>
                    <a href="?delete=<?= $habit['id'] ?>" class="btn btn-sm btn-danger" 
                       onclick="return confirm('Bu alışkanlığı silmek istediğinizden emin misiniz?')" 
                       title="Sil">
                        <i class="bi bi-trash"></i>
                    </a>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <div class="mt-3">
        <a href="dashboard.php" class="btn btn-secondary">Ana Sayfaya Dön</a>
    </div>

    <script>
    function fillHabit(element) {
        // örneklerde yanda emoji var bunu kaldırıp metni inputa yerleştirmesi için
        const text = element.textContent.split(' ').slice(1).join(' ');
        document.getElementById('habitInput').value = text;
    }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$mysqli->close();
?>
