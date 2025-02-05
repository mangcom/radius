<?php
session_start();
require_once '../config/config.php';

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// ตรวจสอบว่ามีการส่งค่าชื่อผู้ใช้มา
if (!isset($_GET['username'])) {
    header("Location: users.php");
    exit();
}

$username = $_GET['username'];

// ดึงข้อมูลผู้ใช้
$stmt = $pdo->prepare("SELECT value FROM radcheck WHERE username = :username AND attribute = 'Cleartext-Password'");
$stmt->execute(['username' => $username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// ดึงกลุ่มทั้งหมด
$stmt = $pdo->query("SELECT DISTINCT groupname FROM radusergroup ORDER BY groupname ASC");
$groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ดึงกลุ่มของผู้ใช้
$stmt = $pdo->prepare("SELECT groupname FROM radusergroup WHERE username = :username");
$stmt->execute(['username' => $username]);
$userGroup = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <title>แก้ไขผู้ใช้ | FreeRADIUS Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand btn btn-warning" href="index.php">FreeRADIUS Admin Menu</a>
            <a class="btn btn-danger" href="logout.php">ออกจากระบบ</a>
        </div>
    </nav>
    <div class="container mt-4">
        <h2>แก้ไขผู้ใช้: <?php echo htmlspecialchars($username); ?></h2>

        <form action="../actions/update_user.php" method="POST">
            <input type="hidden" name="username" value="<?php echo htmlspecialchars($username); ?>">

            <div class="mb-3">
                <label>รหัสผ่านใหม่ (ปล่อยว่างหากไม่ต้องการเปลี่ยน):</label>
                <input type="password" class="form-control" name="password">
            </div>

            <div class="mb-3">
                <label>กลุ่มผู้ใช้:</label>
                <select name="groupname" class="form-select">
                    <option value="">-- เลือกกลุ่ม --</option>
                    <?php foreach ($groups as $group): ?>
                    <option value="<?php echo $group['groupname']; ?>"
                        <?php if ($userGroup && $userGroup['groupname'] == $group['groupname']) echo 'selected'; ?>>
                        <?php echo $group['groupname']; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-success">บันทึกการเปลี่ยนแปลง</button>
            <a href="users.php" class="btn btn-secondary">ยกเลิก</a>
        </form>
    </div>
</body>

</html>