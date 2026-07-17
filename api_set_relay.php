<?php
header('Content-Type: application/json');
require_once __DIR__ . '/relay_db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON']);
    exit;
}

$relay = isset($data['relay']) ? $data['relay'] : '';
$value = isset($data['value']) ? (int)$data['value'] : null;

$allowedRelays = ['ry1', 'ry2', 'ry3', 'ry4', 'ry5'];
if (!in_array($relay, $allowedRelays, true) || !in_array($value, [0, 1], true)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid relay or value']);
    exit;
}

$sql = "UPDATE relay_status SET $relay = ? ORDER BY id DESC LIMIT 1";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database prepare failed']);
    exit;
}

$stmt->bind_param('i', $value);
if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $stmt->error]);
    $stmt->close();
    $conn->close();
    exit;
}

if ($stmt->affected_rows === 0) {
    $stmt->close();
    $insertSql = "INSERT INTO relay_status (sw1, sw2, ldr, ry1, ry2, ry3, ry4, ry5) VALUES (0, 0, 0, 0, 0, 0, 0, 0)";
    if (!$conn->query($insertSql)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database insert failed']);
        $conn->close();
        exit;
    }
    
    $sql = "UPDATE relay_status SET $relay = ? ORDER BY id DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $value);
    $stmt->execute();
}

echo json_encode(['success' => true, 'message' => 'Relay updated']);
$stmt->close();
$conn->close();
