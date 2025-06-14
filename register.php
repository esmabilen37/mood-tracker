<?php
include 'db.php';

// kayıt formu gönderildiğinde 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    
    // email adresi daha önceden kullanılmış mı diye kontrol etmek için
    $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $check_stmt->execute([$email]);
    $email_exists = $check_stmt->fetchColumn() > 0;
    
    // eğer email daha önceden kullanılmışsa hata mesajı gosterip giriş sayfasına yönlendirmek için
    if ($email_exists) {
        $error = '<div class="alert alert-danger">
            Bu email adresi zaten kullanımda! 
            <a href="login.php" class="btn btn-outline-primary btn-sm ms-2">Giriş Yap</a>
        </div>';
    } else {
        try {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
            $stmt->execute([$username, $email, $password_hash]);

            echo '<div class="alert alert-success">
                Kayıt başarılı! 
                <a href="login.php" class="btn btn-primary btn-sm ms-2">Giriş Yap</a>
            </div>';
        } catch (PDOException $e) {
            $error = '<div class="alert alert-danger">Bir hata oluştu, lütfen tekrar deneyin.</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kayıt Ol</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2>Kayıt Ol</h2>    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">                <div class="card-body">
                    <?php if (isset($error)) echo $error; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Kullanıcı Adı:</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email:</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Şifre:</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="submit" class="btn btn-primary">Kayıt Ol</button>
                            <a href="login.php" class="btn btn-outline-primary">Giriş Yap</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
