<?php
header('Content-Type: application/json');
require_once __DIR__ . '/relay_db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$apiKeyHeader = isset($_SERVER['HTTP_X_API_KEY']) ? $_SERVER['HTTP_X_API_KEY'] : '';
$expectedApiKey = 'CYVCitIJfpgxHWUkA8xV3q7fJmyufbIB5Te5Rabv';

if ($apiKeyHeader !== $expectedApiKey) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON']);
    exit;
}

$sw1 = isset($data['sw1']) ? (int)$data['sw1'] : 0;
$sw2 = isset($data['sw2']) ? (int)$data['sw2'] : 0;
$ldr = isset($data['ldr']) ? (int)$data['ldr'] : 0;

$stmt = $conn->prepare("SELECT ry1, ry2, ry3, ry4, ry5 FROM relay_status ORDER BY id DESC LIMIT 1");
$stmt->execute();
$stmt->bind_result($ry1, $ry2, $ry3, $ry4, $ry5);
$stmt->fetch();
$stmt->close();

if (!isset($ry1)) {
    $ry1 = 0; $ry2 = 0; $ry3 = 0; $ry4 = 0; $ry5 = 0;
}

$sql = "INSERT INTO relay_status (sw1, sw2, ldr, ry1, ry2, ry3, ry4, ry5) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param('iiiIIIII', $sw1, $sw2, $ldr, $ry1, $ry2, $ry3, $ry4, $ry5);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Saved']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $stmt->error]);
}

$stmt->close();
$conn->close();
