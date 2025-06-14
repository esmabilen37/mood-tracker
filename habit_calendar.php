<?php

session_start();
require_once 'db.php';

// kullanıcı ve alışkanlık id kontrolü
$user_id = $_SESSION['user_id'] ?? null;
$habit_id = $_GET['habit_id'] ?? null;

if (!$user_id || !$habit_id) {
    echo "Geçersiz kullanıcı veya alışkanlık.";
    exit;
}


// takvim için yıl ve ay bilgisini almak için
$year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');
$month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');

// bu ay içinde kaç gün işaretlenmiş saymak için
$sql_count = "SELECT COUNT(*) as total_days FROM habit_tracking 
              WHERE user_id = :user_id AND habit_id = :habit_id 
              AND MONTH(date) = :month AND YEAR(date) = :year 
              AND status = 1";
$stmt_count = $pdo->prepare($sql_count);
$stmt_count->execute([
    ':user_id' => $user_id,
    ':habit_id' => $habit_id,
    ':month' => $month,
    ':year' => $year
]);
$completed_days = $stmt_count->fetch(PDO::FETCH_ASSOC)['total_days'];

// seçilen ay için veritabanından işaretlenmiş günleri çekiyoruz
$sql = "SELECT DATE(date) as date, status FROM habit_tracking 
        WHERE user_id = :user_id AND habit_id = :habit_id 
        AND MONTH(date) = :month AND YEAR(date) = :year";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':user_id' => $user_id,
    ':habit_id' => $habit_id,
    ':month' => $month,
    ':year' => $year
]);

$tracked_days = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $day = date('j', strtotime($row['date']));
    $tracked_days[$day] = $row['status'] ?? 1;
}

$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
$firstDayOfMonth = date('w', strtotime("$year-$month-01"));
$monthName = date('F', strtotime("$year-$month-01"));
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">

<div class="container mt-4">
    <div class="month-navigation mb-4">
        <button class="btn btn-outline-primary" onclick="changeMonth(-1)">&#8249; Önceki Ay</button>
        <h2><?= $monthName ?> <?= $year ?></h2>
        <button class="btn btn-outline-primary" onclick="changeMonth(1)">Sonraki Ay &#8250;</button>
    </div>

    <table class="table table-bordered text-center">
        <thead>
            <tr>
                <th>Pzt</th><th>Sal</th><th>Çar</th><th>Per</th><th>Cum</th><th>Cmt</th><th>Paz</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $day = 1;
        $start = ($firstDayOfMonth + 6) % 7;
        for ($week = 0; $week < 6; $week++) {
            echo "<tr>";
            for ($i = 0; $i < 7; $i++) {
                if ($week === 0 && $i < $start || $day > $daysInMonth) {
                    echo "<td></td>";
                } else {
                    $today = date('Y-m-d') === sprintf('%04d-%02d-%02d', $year, $month, $day);
                    $status = isset($tracked_days[$day]) ? $tracked_days[$day] : null;
                    $statusClass = '';
                    $statusSymbol = '';
                    
                    if ($status === 1) {
                        $statusClass = 'habit-complete';
                        $statusSymbol = '✕';
                    } elseif ($status === 0) {
                        $statusClass = 'habit-incomplete';
                        $statusSymbol = ':(';
                    }
                    
                    $todayClass = $today ? 'bg-light' : '';
                    echo "<td class='calendar-day $statusClass $todayClass' 
                          onclick='toggleHabit($year, $month, $day, $habit_id)' 
                          data-date='$year-$month-$day'>
                          $day<br>$statusSymbol</td>";
                    $day++;
                }
            }
            echo "</tr>";
            if ($day > $daysInMonth) break;
        }
        ?>
        </tbody>
    </table>    <div class="card mt-3 shadow-sm border-success">
        <div class="card-body text-center">
            <h5 class="card-title text-success">Harika gidiyorsun!</h5>
            <div class="streak-count mb-3">
                En uzun zincirin: <span id="maxStreak">0</span> gün
            </div>
            <a href="habits.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Alışkanlıklara Dön
            </a>
        </div>
    </div>
</div>

<style>
.month-navigation {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.calendar-day {
    cursor: pointer;
    position: relative;
    height: 50px;
    transition: all 0.3s;
}

.calendar-day:hover {
    background-color: #f8f9fa;
}

.habit-complete {
    color: #198754;
    font-weight: bold;
}

.habit-incomplete {
    color: #dc3545;
}
</style>

<script>
function calculateStreaks() {
    // takvimden işaretli günleri bul
    const days = Array.from(document.querySelectorAll('.calendar-day'))
        .map(cell => {
            if (cell.classList.contains('habit-complete')) return 'X';
            if (cell.classList.contains('habit-incomplete')) return ':(';
            return '-';
        })
        .filter(status => status !== '-')
        .join('');

    // en uzun zinciri hesapla
    const streaks = days.split(':(').map(streak => {
        return (streak.match(/X+/) || [''])[0].length;
    });
    const maxStreak = Math.max(...streaks, 0);

    // zincir sayısını güncelle
    document.getElementById('maxStreak').textContent = maxStreak;
}

function toggleHabit(year, month, day, habitId) {
    const date = `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
    
    fetch('toggle_habit.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            habit_id: habitId,
            date: date
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const cell = document.querySelector(`td[data-date='${year}-${month}-${day}']`);            
            if (data.status === 1) {
                cell.innerHTML = `${day}<br>✕`;
                cell.classList.add('habit-complete');
                cell.classList.remove('habit-incomplete');
            } else if (data.status === 0) {
                cell.innerHTML = `${day}<br>:(`;
                cell.classList.add('habit-incomplete');
                cell.classList.remove('habit-complete');
            } else {                
                cell.innerHTML = day;
                cell.classList.remove('habit-complete', 'habit-incomplete');
            }
            // tüm bilgileri güncelle
            calculateStreaks();
        }
    });
}

function changeMonth(offset) {
    const urlParams = new URLSearchParams(window.location.search);
    let year = parseInt(urlParams.get('year')) || <?= date('Y') ?>;
    let month = parseInt(urlParams.get('month')) || <?= date('m') ?>;
    
    month += offset;
    
    if (month > 12) {
        month = 1;
        year++;
    } else if (month < 1) {
        month = 12;
        year--;
    }
    
    window.location.href = `habit_calendar.php?habit_id=${<?= $habit_id ?>}&year=${year}&month=${month}`;
}

// sayfa yüklendiği zaman ilk hesaplamayı yapmak için
document.addEventListener('DOMContentLoaded', calculateStreaks);
</script>
