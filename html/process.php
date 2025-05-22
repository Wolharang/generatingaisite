<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once __DIR__ . '/config/api_keys.php';

$accommodation_map = [
    'hotel' => '호텔',
    'resort' => '리조트',
    'guesthouse' => '게스트하우스',
    'airbnb' => '에어비앤비',
    'pension' => '펜션'
];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = "잘못된 접근 방식입니다.";
    header("Location: index.php");
    exit;
}

$required = ['destination', 'start_date', 'end_date', 'travelers', 'accommodation', 'budget'];
foreach ($required as $field) {
    if (empty($_POST[$field])) {
        $_SESSION['error'] = "모든 필수 항목을 입력해주세요.";
        header("Location: index.php");
        exit;
    }
}

$destination = htmlspecialchars($_POST['destination']);
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];
$travelers = (int)$_POST['travelers'];
$accommodation = htmlspecialchars($_POST['accommodation']);
$budget = number_format((int)$_POST['budget']) . '원';
$additional_info = htmlspecialchars($_POST['additional_info']);

if (strtotime($start_date) > strtotime($end_date)) {
    $_SESSION['error'] = "종료일은 시작일보다 빠를 수 없습니다.";
    header("Location: index.php");
    exit;
}

$start_date_obj = new DateTime($start_date);
$end_date_obj = new DateTime($end_date);
$interval = $start_date_obj->diff($end_date_obj);
$days = $interval->days + 1;
$days_text = $days . "일";
$travelers_text = $travelers . "명";

$prompt = <<<PROMPT
너는 여행 가이드야.
너의 목표는 내가 즐겁게 여행할수 있도록 여행일정을 짜주는 것이야.
아래 조건을 참고해서 여행일정을 구성해줘.

나는 $destination 으로 $start_date ~ $end_date ($days_text) 동안 여행을 떠날 거야.
인원은 $travelers_text 이고, 예산은 $budget 원이야. $additional_info 를 강조해서 이것이 최대한 포함되도록 여행 일정이 만들어졌으면 해.
나는 $destination 에서 가성비 좋은 숙소를 찾을 거야. 숙소는 {$accommodation_map[$accommodation]} 으로 했으면 해. 숙소도 추천해주고 숙박 링크를 줘. 숙소를 고려할 때는 Adoga, Tripcom 등의 사이트들에서 리뷰와 평점을 확인하고, 숙소의 위치와 편의 시설 등을 종합적으로 고려해야 해.

현지 명소, 숨겨진 장소, 현지 음식점을 포함한 상세한 여행 일정을 짜줘. 아래 요청 사항을 고려해서 여행 일정을 짜줬으면 해.
1. 일별 일정을 시간대별로 구체적으로 작성해. 여행 기간에 맞게 오전, 오후, 저녁 일정으로 구성해줘.
2. 식사는 점심과 저녁을 각 지역에 유명한 맛집으로 해줘. 식당 추천 시 평점 4점 이상인 맛집 위주로 편성하면 좋겠어. 나는 $destination 에서 꼭 먹어봐야 할 현지 음식을 알고 싶어. 이 음식을 파는 최고의 장소를 추천해줘.
3. 교통 수단과 소요 시간을 명시해. 
4. 예산 범위 내에서 현실적인 계획을 만들어 줘.
5. 숙소는 이 여행 일정에 가장 적합한 곳으로 골라주면 좋겠어. 
6. 많은 사람들이 가는 유명한 코스로 구성을 해줘. 각 코스에 대해 내가 이해할수 있도록 자세한 설명을 해줘. 이 때 각 코스의 특징을 잘 나타내줬으면 해.
7. 사용자들의 후기도 출력해 줘. 내가 $start_date ~ $end_date ($days_text) 에 $destination 으로 갈 것이니깐, 필수 준비물과 자주 까먹는 아이템들까지 모두 확인해서 체크리스트를 만들어줘. 현지에서 주의해야 할 예절과 안전 수칙도 알려줘. 또, 피해야 할 사기나 위험 요소도 포함해서 알려줘.
8. 나는 $destination 에서 사진 찍기 좋은 곳도 알고 싶어. 현지인이 추천하는 숨겨진 스팟과 인기 있는 장소를 다 포함해서 알려줘.

웹 검색 및 @Google 지도 정보, @YouTube를 활용하여 $destination 에서 경험할 수 있는 특별한 액티비티나 체험도 추천해줘. 다음 조건으로 검색해줘:
- 현지인 추천 또는 독특한 경험 관련 정보 검색 (현지어, 영어, 일본어, 한국어로도 검색)
- @Google 지도 기반으로 실제 운영 중인 것으로 확인되는 장소 (정확한 위치 정보 포함)
- 계절/날짜별 특별 이벤트 정보 검색 (현지 축제, 공연, 시즌 한정 액티비티 등)
- 최근 소셜미디어에서 주목받는 장소 정보 검색 (붐비는 정도는 변동 가능)

여행 일정을 만들 때, 내가 $destination 으로 $start_date ~ $end_date ($days_text) 동안 여행을 떠날 것이라는 점, 
인원은 $travelers_text 이고, 예산은 $budget 원이며, $additional_info 를 강조해서 이것이 최대한 포함되도록 여행 일정이 만들어졌으면 한다는 점,
내가 $destination 에서 가성비 좋은 숙소를 찾을 것이므로 숙소는 {$accommodation_map[$accommodation]} 으로 했으면 하니 Adoga, Tripcom 등의 사이트들에서 리뷰와 평점을 확인하고, 숙소의 위치와 편의 시설 등을 종합적으로 고려해서 숙소를 찾아서 숙박 링크를 제공해 줘야 한다는 점 등을 다시 한번 강조할게.

일정은 1시간 이하 단위로 시간대별로, 기상 시간부터 수면 시간까지 전부 다 하나하나 작성해 줘. 예를 들어, "08시 00분 A 호텔에서 조식 식사, 09시 00분 B 지하철역에서 C 전철 탑승 후 D 역까지 이동, 09시 30분 D 역에서 하차, 09시 40분 E 관광지 방문" 등과 같이 시간대별로 상세히 작성하라는 의미야.

answer more human-like, specifically focusing on avoiding typical GPT patterns, grammar, and tone that might reveal the text was AI-generated. NEVER use like '**'

답변 시 절대 ** 같은 강조표시를 절대로 쓰지 말고 답변해. GPT 표절에 걸리지 않게 표현을 다듬어줘.

PROMPT;

try {
    $initial_plan = callGeminiApi($prompt, $GEMINI_API_KEY);
    
    $_SESSION['destination'] = $destination;
    $_SESSION['start_date'] = $start_date;
    $_SESSION['end_date'] = $end_date;
    $_SESSION['final_plan'] = $initial_plan;
    $_SESSION['result_ready'] = true;
    
    header("Location: loading.php");
    exit;
    
} catch (Exception $e) {
    $_SESSION['error'] = "API 오류: " . $e->getMessage();
    header("Location: index.php");
    exit;
}

function callGeminiApi($prompt, $api_key) {
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=$api_key";
    $parsedUrl = parse_url($url);
    
    $host = $parsedUrl['host'];
    $path = $parsedUrl['path'] . '?' . $parsedUrl['query'];
    $port = 443;

    $fp = fsockopen("ssl://$host", $port, $errno, $errstr, 30);
    if (!$fp) throw new Exception("연결 실패: $errstr ($errno)");

    $requestData = [
        'contents' => [['parts' => [['text' => $prompt]]]],
        'generationConfig' => [
            'temperature' => 0.9,
            'topP' => 1.0,
            'maxOutputTokens' => 2048
        ]
    ];

    $postData = json_encode($requestData);
    $headers = [
        "Host: $host",
        "Content-Type: application/json",
        "Content-Length: " . strlen($postData),
        "Connection: close"
    ];

    $request = "POST $path HTTP/1.1\r\n";
    $request .= implode("\r\n", $headers) . "\r\n\r\n";
    $request .= $postData;

    fwrite($fp, $request);

    $response = '';
    while (!feof($fp)) {
        $response .= fgets($fp, 1024);
    }
    fclose($fp);

    // 헤더/바디 분리
    list($headers, $body) = explode("\r\n\r\n", $response, 2);

    // HTTP 상태 코드 검증
    preg_match('/HTTP\/1\.1 (\d+)/', $headers, $matches);
    $statusCode = $matches[1] ?? 500;
    if ($statusCode != 200) throw new Exception("HTTP $statusCode 오류");

    // 청크 인코딩 처리
    if (strpos($headers, 'Transfer-Encoding: chunked') !== false) {
        $body = decodeChunked($body);
    }

    $responseData = json_decode($body, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("JSON 파싱 오류: " . json_last_error_msg());
    }

    if (!isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
        throw new Exception("유효하지 않은 응답 형식");
    }

    return $responseData['candidates'][0]['content']['parts'][0]['text'];
}

function decodeChunked($data) {
    $decoded = '';
    $position = 0;
    $length = strlen($data);

    while ($position < $length) {
        $hexEnd = strpos($data, "\r\n", $position);
        if ($hexEnd === false) break;

        $chunkSize = hexdec(substr($data, $position, $hexEnd - $position));
        $position = $hexEnd + 2;

        if ($chunkSize == 0) break;

        $decoded .= substr($data, $position, $chunkSize);
        $position += $chunkSize + 2;
    }

    return $decoded;
}
?>