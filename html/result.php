<?php
session_start();

if (!isset($_SESSION['final_plan'])) {
    header("Location: index.php");
    exit;
}

$destination = $_SESSION['destination'];
$start_date = $_SESSION['start_date'];
$end_date = $_SESSION['end_date'];
$final_plan = $_SESSION['final_plan'];

// 세션에서 사용 완료된 변수들 정리
unset($_SESSION['final_plan']);
unset($_SESSION['result_ready']);
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>여행 계획 결과</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container result-container">
        <h1>✈️ <?=htmlspecialchars($destination)?> 여행 계획</h1>
        <div class="meta-info">
            <p>📅 기간: <?=htmlspecialchars("$start_date ~ $end_date")?></p>
        </div>

        <div class="plan-results">
            <div class="plan-box">
                <h3>📌 최종 여행 일정</h3>
                <div class="plan-content">
                    <?=nl2br(htmlspecialchars($final_plan))?>
                </div>
            </div>
        </div>

        <div class="actions">
            <a href="index.php" class="btn">🏠 새 계획 작성</a>
            <button onclick="window.print()" class="btn">🖨️ 인쇄하기</button>
        </div>
    </div>
</body>
</html>
