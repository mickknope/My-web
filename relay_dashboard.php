<?php
session_start();
require_once __DIR__ . '/relay_db.php';

$stmt = $conn->prepare("SELECT id, sw1, sw2, ldr, ry1, ry2, ry3, ry4, ry5, created_at FROM relay_status ORDER BY id DESC LIMIT 20");
$stmt->execute();
$stmt->bind_result($id, $sw1, $sw2, $ldr, $ry1, $ry2, $ry3, $ry4, $ry5, $createdAt);

$rows = [];
while ($stmt->fetch()) {
    $rows[] = [
        'id' => $id,
        'sw1' => (int)$sw1,
        'sw2' => (int)$sw2,
        'ldr' => (int)$ldr,
        'ry1' => (int)$ry1,
        'ry2' => (int)$ry2,
        'ry3' => (int)$ry3,
        'ry4' => (int)$ry4,
        'ry5' => (int)$ry5,
        'created_at' => $createdAt,
    ];
}
$stmt->close();
$conn->close();

$latest = !empty($rows) ? $rows[0] : ['sw1' => 0, 'sw2' => 0, 'ldr' => 0, 'ry1' => 0, 'ry2' => 0, 'ry3' => 0, 'ry4' => 0, 'ry5' => 0];
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relay Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="page">
    <div class="dashboard-panel">
        <div class="dashboard-title">
            <h1 class="dashboard-title">สถานะรีเลย์</h1>
            <p class="dashboard-subtitle">แสดงสถานะของแต่ละรีเลย์แบบเรียลไทม์</p>
        </div>

        <div class="relay-grid">
            <?php for ($i = 1; $i <= 5; $i++): $key = 'ry' . $i; ?>
            <div class="relay-card">
                <div class="relay-number">รีเลย์ <?= $i ?></div>
                <div class="relay-badge <?= $latest[$key] ? 'on' : 'off' ?>"><?= $latest[$key] ? 'ON' : 'OFF' ?></div>
                <button class="relay-btn <?= $latest[$key] ? 'off' : 'on' ?>" data-relay="<?= $key ?>" data-value="<?= $latest[$key] ? 0 : 1 ?>">
                    <?= $latest[$key] ? 'ปิด' : 'เปิด' ?> รีเลย์ <?= $i ?>
                </button>
            </div>
            <?php endfor; ?>
        </div>
    </div>
</div>

<script>
const buttons = document.querySelectorAll('.relay-btn');
buttons.forEach(button => {
    button.addEventListener('click', async () => {
        const relay = button.dataset.relay;
        const value = parseInt(button.dataset.value, 10);
        button.disabled = true;
        button.textContent = 'กำลังอัปเดต...';

        try {
            const response = await fetch('api_set_relay.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ relay, value })
            });

            const result = await response.json();
            if (result.success) {
                location.reload();
            } else {
                alert(result.message || 'อัปเดต relay ล้มเหลว');
                button.disabled = false;
                button.textContent = button.dataset.value === '1' ? 'เปิด Relay' : 'ปิด Relay';
            }
        } catch (error) {
            alert('เชื่อมต่อเซิร์ฟเวอร์ไม่สำเร็จ');
            button.disabled = false;
            button.textContent = button.dataset.value === '1' ? 'เปิด Relay' : 'ปิด Relay';
        }
    });
});
</script>
</body>
</html>
