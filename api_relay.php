<?php
header('Content-Type: application/json');
require_once __DIR__ . '/relay_db.php';

$stmt = $conn->prepare("SELECT ry1, ry2, ry3, ry4, ry5 FROM relay_status ORDER BY id DESC LIMIT 1");
$stmt->execute();
$stmt->bind_result($ry1, $ry2, $ry3, $ry4, $ry5);
if ($stmt->fetch()) {
    echo json_encode(['ry1' => (int)$ry1, 'ry2' => (int)$ry2, 'ry3' => (int)$ry3, 'ry4' => (int)$ry4, 'ry5' => (int)$ry5]);
} else {
    echo json_encode(['ry1' => 0, 'ry2' => 0, 'ry3' => 0, 'ry4' => 0, 'ry5' => 0]);
}
$stmt->close();
$conn->close();
