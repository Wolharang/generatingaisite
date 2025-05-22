<?php
session_start();

// 결과 준비 여부 확인
if (!isset($_SESSION['result_ready'])) {
    header("Location: index.php");
    exit;
}

// 5초 후 자동 리디렉션
header("Refresh:5; url=result.php");
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>여행 계획 생성 중</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .loading-container {
            text-align: center;
            padding: 50px;
        }
        .loading-spinner {
            border: 5px solid #f3f3f3;
            border-top: 5px solid #3498db;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            margin: 20px auto;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="container loading-container">
        <h1>여행 계획을 생성 중입니다</h1>
        <div class="loading-spinner"></div>
        <p>잠시만 기다려 주세요... (5초 후 자동 이동)</p>
    </div>
</body>
</html>
