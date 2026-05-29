<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="device-width" , initial-scale="1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php
    include 'navbar.php';
    ?>
    <div class="form-container">
        <h1> สมัครสมาชิก </h1>
        <form action="register_process.php" method="post">
            <label for="username"> ชื่อผู้ใช้: </label>
            <input type="text" id="username" name="username" required class="input-feild"><br><br>

            <label for="email"> อีเมล: </label>
            <input type="email" id="email" name="email" required class="input-feild"><br><br>

            <label for="password"> รหัสผ่าน: </label>
            <input type="password" id="password" name="password" required class="input-feild"><br><br>

            <input type="submit" value="สมัครสมาชิก" class="btn">
        </form>
    </div>
</body>

</html>