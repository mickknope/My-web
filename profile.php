<?php
session_start();
require "config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

$sql = "SELECT * FROM tbl_user WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);
?>

<link rel="stylesheet" href="style.css">

<?php include 'navbar.php'; ?>

<div class="container">
    <h2>Profile</h2>

    <?php if (isset($_GET["success"])): ?>
        <p style="color:green;">บันทึกข้อมูลสำเร็จ</p>
    <?php endif; ?>

    <div class="profile-box">

        <?php if (!empty($user["image"])): ?>
            <img src="uploads/<?= htmlspecialchars($user["image"]) ?>" width="150" style="border-radius:10px;">
        <?php else: ?>
            <img src="uploads/default.png" width="150" style="border-radius:10px;">
        <?php endif; ?>

        <p>รหัสผู้ใช้: <?= htmlspecialchars($user["id"]) ?></p>
        <p>ชื่อ: <?= htmlspecialchars($user["name"]) ?></p>
        <p>อีเมล: <?= htmlspecialchars($user["email"]) ?></p>
        <p>ที่อยู่: <?= htmlspecialchars($user["address"] ?? "-") ?></p>

        <button onclick="document.getElementById('editForm').style.display='block'" class="btn">
            แก้ไขข้อมูล
        </button>
    </div>

    <hr>

    <form id="editForm" action="profile_update.php" method="POST" enctype="multipart/form-data" style="display:none;">

        <label>ชื่อ</label>
        <input type="text" name="name" value="<?= htmlspecialchars($user["name"]) ?>" required class="input-field">

        <label>ที่อยู่</label>
        <textarea name="address" rows="4"><?= htmlspecialchars($user["address"] ?? "") ?></textarea>

        <label>รูปโปรไฟล์</label>
        <input type="file" name="image" accept="image/*">

        <br><br>

        <button type="submit" class="btn">บันทึก</button>
        <button type="button" onclick="document.getElementById('editForm').style.display='none'" class="btn">
            ยกเลิก
        </button>
    </form>
</div>