<?php

 //alışkanlık düzenleme sayfası (sadece kullanıcının kendi alışkanlıklarını düzenlemesine izin verilir)

session_start();
require_once 'db.php';

// giriş kontrolü
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// kullanıcı ve alışkanlık id kontrolü
$user_id = $_SESSION['user_id'];
$habit_id = $_GET['id'] ?? null;

if (!$habit_id) {
    header('Location: habits.php');
    exit;
}

// düzenlenecek alışkanlığın bilgilerini veritabanından getirmesi için
$stmt = $pdo->prepare("SELECT * FROM habits WHERE id = ?");
$stmt->execute([$habit_id]);
$habit = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$habit || ($habit['user_id'] !== null && $habit['user_id'] != $user_id)) {
    header('Location: habits.php');
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description'] ?? '');
    
    if (empty($title)) {
        $message = 'Alışkanlık adı boş olamaz!';
    } else {
        try {
            // kullanıcı kendi alışkanlığını düzenliyorsa güncellesin diye
            if ($habit['user_id'] == $user_id) {
                $stmt = $pdo->prepare("UPDATE habits SET title = ?, description = ? WHERE id = ? AND user_id = ?");
                $stmt->execute([$title, $description, $habit_id, $user_id]);
                $message = 'Alışkanlık başarıyla güncellendi!';
            } else {
                //üst kısımda öneri olarak verdiklerimden kullanıcı kendi alışkanlığını olusturmak isterse:
                $stmt = $pdo->prepare("INSERT INTO habits (user_id, title, description) VALUES (?, ?, ?)");
                $stmt->execute([$user_id, $title, $description]);
                
                $new_habit_id = $pdo->lastInsertId();
                $message = 'Alışkanlık başarıyla oluşturuldu!';
                
                // yeni oluşturulan alışkanlığın sayfasına yönlendirmek için
                header("Location: edit_habit.php?id=" . $new_habit_id);
                exit;
            }
        } catch (PDOException $e) {
            $message = 'Bir hata oluştu, lütfen tekrar deneyin.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alışkanlık Düzenle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title mb-0">
                            <?= $habit['user_id'] == $user_id ? 'Alışkanlık Düzenle' : 'Yeni Alışkanlık Oluştur' ?>
                        </h3>
                    </div>
                    <div class="card-body">
                        <?php if ($message): ?>
                            <div class="alert alert-<?= strpos($message, 'başarıyla') !== false ? 'success' : 'danger' ?>">
                                <?= htmlspecialchars($message) ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label for="title" class="form-label">Alışkanlık Adı</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="title" 
                                       name="title" 
                                       value="<?= htmlspecialchars($habit['title']) ?>" 
                                       required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Açıklama</label>
                                <textarea class="form-control" 
                                          id="description" 
                                          name="description" 
                                          rows="3"><?= htmlspecialchars($habit['description'] ?? '') ?></textarea>
                            </div>

                            <div class="d-flex justify-content-between">
                                <button type="submit" class="btn btn-primary">
                                    <?= $habit['user_id'] == $user_id ? 'Güncelle' : 'Oluştur' ?>
                                </button>
                                <a href="habits.php" class="btn btn-secondary">Geri Dön</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
