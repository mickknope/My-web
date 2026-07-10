<?php
session_start();
require "config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$name = $_POST["name"];
$address = $_POST["address"];

$imageName = null;

if (!empty($_FILES["image"]["name"])) {
    $targetDir = "uploads/";

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $fileName = time() . "_" . basename($_FILES["image"]["name"]);
    $targetFile = $targetDir . $fileName;

    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $allowTypes = ["jpg", "jpeg", "png", "gif", "webp"];

    if (in_array($fileType, $allowTypes)) {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            $imageName = $fileName;
        }
    }
}

if ($imageName) {
    $sql = "UPDATE tbl_user SET name = ?, address = ?, image = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssi", $name, $address, $imageName, $user_id);
} else {
    $sql = "UPDATE tbl_user SET name = ?, address = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssi", $name, $address, $user_id);
}

mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

header("Location: profile.php?success=1");
exit;
?>