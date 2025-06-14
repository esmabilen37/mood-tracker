<?php
session_start();

// Eğer kullanıcı zaten giriş yapmışsa dashboard'a yönlendir
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hoşgeldiniz</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        
        .welcome-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .welcome-header {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            border-radius: 20px 20px 0 0;
            padding: 2rem;
            text-align: center;
        }
        
        .welcome-body {
            padding: 2.5rem;
        }
        
        .btn-custom {
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 500;
            transition: all 0.3s ease;
            margin: 0.5rem;
        }
        
        .btn-login {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
            color: white;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }
        
        .btn-register {
            background: transparent;
            border: 2px solid #667eea;
            color: #667eea;
        }
        
        .btn-register:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .feature-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            color: white;
            font-size: 1.5rem;
        }
        
        .feature-card {
            text-align: center;
            padding: 1.5rem;
            border-radius: 15px;
            background: rgba(255, 255, 255, 0.7);
            margin-bottom: 1rem;
            transition: transform 0.3s ease;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
        }
        
        .hero-text {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }
        
        .hero-subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="welcome-card">
                    <div class="welcome-header">
                        <h1 class="hero-text">Hoşgeldiniz! 👋</h1>
                        <p class="hero-subtitle">Sistemimize katılmak için giriş yapın veya yeni hesap oluşturun</p>
                    </div>
                    
                    <div class="welcome-body">
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="feature-card">
                                    <div class="feature-icon">
                                        🔒
                                    </div>
                                    <h5>Güvenli</h5>
                                    <p class="small text-muted">Verileriniz güvenli şekilde korunur</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="feature-card">
                                    <div class="feature-icon">
                                        ⚡
                                    </div>
                                    <h5>Hızlı</h5>
                                    <p class="small text-muted">Kolay ve hızlı kullanım deneyimi</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="feature-card">
                                    <div class="feature-icon">
                                        🌟
                                    </div>
                                    <h5>Modern</h5>
                                    <p class="small text-muted">Kullanıcı dostu arayüz</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-center">
                            <h3 class="mb-4">Başlamak için bir seçenek seçin</h3>
                            <div class="d-flex flex-column flex-md-row justify-content-center align-items-center">
                                <a href="login.php" class="btn btn-login btn-custom btn-lg">
                                    📧 Giriş Yap
                                </a>
                                <a href="register.php" class="btn btn-register btn-custom btn-lg">
                                    🆕 Yeni Hesap Oluştur
                                </a>
                            </div>
                            
                            <div class="mt-4 pt-3 border-top">
                                <p class="text-muted">
                                    <small>
                                        Hesabınız var mı? <a href="login.php" class="text-decoration-none">Giriş yapın</a> • 
                                        Yeni misiniz? <a href="register.php" class="text-decoration-none">Kayıt olun</a>
                                    </small>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>