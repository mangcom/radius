<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <title>แดชบอร์ด | FreeRADIUS Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand  btn btn-warning" href="index.php">FreeRADIUS Admin Menu</a>
            <a class="btn btn-danger" href="logout.php">ออกจากระบบ</a>
        </div>
    </nav>
    <div class="container mt-4">
        <h2>ยินดีต้อนรับ, <?php echo $_SESSION['username']; ?>!</h2>
        <ul>
            <li><a href="users.php">จัดการผู้ใช้</a></li>
            <li><a href="groups.php">จัดการกลุ่ม</a></li>
        </ul>
    </div>
</body>

</html>