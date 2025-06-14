<?php
//bu kısmı olustururken aidan cok fazla yardım aldım.
session_start();
require_once 'db.php';

// javascriptten gelen bilgileri almak için
$input = json_decode(file_get_contents('php://input'), true);
$habit_id = $input['habit_id'] ?? null;
$date = $input['date'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;

// bilgiler eksikse işlem yapmayalım diye
if (!$user_id || !$habit_id || !$date) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

try {
    // bu güne ait kayıt var mı diye bakıyoruz
    $sql = "SELECT status FROM habit_tracking 
            WHERE user_id = :user_id 
            AND habit_id = :habit_id 
            AND DATE(date) = :date";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':user_id' => $user_id,
        ':habit_id' => $habit_id,
        ':date' => $date
    ]);
    
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existing) {
        // kayıt varsa durumu değiştir x -1- ise :( -0- yap
        if ($existing['status'] == 1) {
            $sql = "UPDATE habit_tracking 
                    SET status = 0 
                    WHERE user_id = :user_id 
                    AND habit_id = :habit_id 
                    AND DATE(date) = :date";
            $new_status = 0;
        } 
        // :( -0- ise kaydı siliyoruz
        else if ($existing['status'] == 0) {
            $sql = "DELETE FROM habit_tracking 
                    WHERE user_id = :user_id 
                    AND habit_id = :habit_id 
                    AND DATE(date) = :date";
            $new_status = null;
        }
    } else {
        // kayıt yoksa yeni x-1- ekle
        $sql = "INSERT INTO habit_tracking (user_id, habit_id, date, status) 
                VALUES (:user_id, :habit_id, :date, 1)";
        $new_status = 1;
    }
      // değişiklikleri veritabanına kaydet
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':user_id' => $user_id,
        ':habit_id' => $habit_id,
        ':date' => $date
    ]);
    
    // sadece başarılı/başarısız ve yeni durumu gönder
    echo json_encode([
        'success' => true,
        'status' => $new_status
    ]);
    
} catch (PDOException $e) {
    // hata olursa bildirmek için
    echo json_encode([
        'success' => false,
        'message' => 'Database error'
    ]);
}
