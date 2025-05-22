<?php session_start(); ?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>맞춤형 여행 계획 생성기</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>🚀 맞춤형 여행 계획 생성기</h1>
        
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert error">
                <?=htmlspecialchars($_SESSION['error'])?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form action="process.php" method="post">
            <div class="form-group">
                <label for="destination">🌍 여행 목적지</label>
                <input type="text" id="destination" name="destination" 
                       placeholder="예: 제주도, 도쿄, 파리 등" required>
            </div>

            <div class="form-group">
                <label>📅 여행 기간</label>
                <div class="date-range">
                    <input type="date" id="start_date" name="start_date" required>
                    <span>부터</span>
                    <input type="date" id="end_date" name="end_date" required>
                </div>
            </div>

            <div class="form-group">
                <label for="travelers">👥 여행 인원</label>
                <select id="travelers" name="travelers" required>
                    <option value="">선택하세요</option>
                    <?php for($i=1; $i<=6; $i++): ?>
                        <option value="<?=$i?>"><?=$i?>명</option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="accommodation">🏨 숙소 유형</label>
                <select id="accommodation" name="accommodation" required>
                    <option value="">선택하세요</option>
                    <option value="hotel">호텔</option>
                    <option value="resort">리조트</option>
                    <option value="guesthouse">게스트하우스</option>
                    <option value="airbnb">에어비앤비</option>
                    <option value="pension">펜션</option>
                </select>
            </div>

            <div class="form-group">
                <label for="budget">💰 예산 (1인당 원화)</label>
                <input type="number" id="budget" name="budget" 
                       min="100000" max="1000000" step="50000" 
                       placeholder="300000" required>
            </div>

            <div class="form-group">
                <label for="additional_info">📝 추가 요청사항</label>
                <textarea id="additional_info" name="additional_info" rows="4"
                          placeholder="특별히 방문하고 싶은 장소, 음식 선호도 등"></textarea>
            </div>

            <button type="submit" class="btn-submit">✨ 여행 계획 생성</button>
        </form>
    </div>
</body>
</html>
