<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$mysqli = new mysqli("localhost", "root", "", "mood_tracker");
$user_id = $_SESSION['user_id'];

if (!isset($_GET['id'])) {
    die("Alışkanlık seçilmedi.");
}
$habit_id = intval($_GET['id']);

// alışkanlıgın adını çekiyoruz
$stmt = $mysqli->prepare("SELECT habit_name FROM habits WHERE id = ? AND (user_id = ? OR user_id IS NULL)");
$stmt->bind_param("ii", $habit_id, $user_id);
$stmt->execute();
$stmt->bind_result($habit_name);
if (!$stmt->fetch()) {
    die("Alışkanlık bulunamadı.");
}
$stmt->close();

// günleri işaretleyebilmemiz için 
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['date'])) {
    $date = $_POST['date'];

    $stmt = $mysqli->prepare("SELECT id FROM habit_tracking WHERE user_id = ? AND habit_id = ? AND track_date = ?");
    $stmt->bind_param("iis", $user_id, $habit_id, $date);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // varsa silsin
        $stmt_del = $mysqli->prepare("DELETE FROM habit_tracking WHERE user_id = ? AND habit_id = ? AND track_date = ?");
        $stmt_del->bind_param("iis", $user_id, $habit_id, $date);
        $stmt_del->execute();
        $stmt_del->close();
    } else {
        // yoksa eklesin
        $stmt_ins = $mysqli->prepare("INSERT INTO habit_tracking (user_id, habit_id, track_date) VALUES (?, ?, ?)");
        $stmt_ins->bind_param("iis", $user_id, $habit_id, $date);
        $stmt_ins->execute();
        $stmt_ins->close();
    }
    $stmt->close();
    header("Location: habit_tracker.php?id=$habit_id");
    exit();
}

// işaretli günleri çekmek için
$stmt = $mysqli->prepare("SELECT track_date FROM habit_tracking WHERE user_id = ? AND habit_id = ?");
$stmt->bind_param("ii", $user_id, $habit_id);
$stmt->execute();
$result = $stmt->get_result();

$tracked_dates = [];
while ($row = $result->fetch_assoc()) {
    $tracked_dates[] = $row['track_date'];
}
$stmt->close();
$mysqli->close();

// bugünün tarihi:
$today = date("Y-m-d");
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($habit_name) ?> Takibi</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .calendar-day {
            width: 80px;
            height: 80px;
            margin: 5px;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            border: 1px solid #ccc;
            border-radius: 10px;
            cursor: pointer;
            font-weight: bold;
        }
        .marked {
            background-color: #28a745;
            color: white;
        }
        .today {
            border: 2px solid #007bff;
        }
    </style>
</head>
<body class="container mt-5">
    <h2><?= htmlspecialchars($habit_name) ?> Takibi</h2>

    <form method="post" class="mb-3">
        <input type="date" name="date" class="form-control" required>
        <button type="submit" class="btn btn-primary mt-2">İşaretle / Kaldır</button>
    </form>

    <h4>Son 30 Gün</h4>
    <div class="d-flex flex-wrap">
        <?php
        for ($i = 29; $i >= 0; $i--) {
            $date = date("Y-m-d", strtotime("-$i days"));
            $isMarked = in_array($date, $tracked_dates);
            $class = "calendar-day";
            if ($isMarked) $class .= " marked";
            if ($date == $today) $class .= " today";
            echo "<form method='post' style='display:inline-block;'>
                    <input type='hidden' name='date' value='$date'>
                    <button type='submit' class='$class'>" . date("d/m", strtotime($date)) . "</button>
                  </form>";
        }
        ?>
    </div>

    <a href="habits.php" class="btn btn-secondary mt-3">Geri</a>
</body>
</html>
