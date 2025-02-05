<?php
session_start();
require_once '../config/config.php';

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// ตรวจสอบว่ามีการส่งค่าชื่อกลุ่มมา
if (!isset($_GET['groupname'])) {
    header("Location: groups.php");
    exit();
}

function getPermission($permissions, $attribute) {
    foreach ($permissions as $perm) {
        if ($perm['attribute'] == $attribute) {
            return htmlspecialchars($perm['value']);
        }
    }
    return "";
}
function getGroupCheck($pdo, $groupname, $attribute) {
    $stmt = $pdo->prepare("SELECT value FROM radgroupcheck WHERE groupname = :groupname AND attribute = :attribute LIMIT 1");
    $stmt->execute(['groupname' => $groupname, 'attribute' => $attribute]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? htmlspecialchars($row['value']) : "";
}

$groupname = $_GET['groupname'];

// ดึงข้อมูลกลุ่ม
$stmt = $pdo->prepare("SELECT DISTINCT groupname FROM radusergroup WHERE groupname = :groupname");
$stmt->execute(['groupname' => $groupname]);
$group = $stmt->fetch(PDO::FETCH_ASSOC);

// ดึงสิทธิ์ที่กำหนดไว้ของกลุ่ม
$stmt = $pdo->prepare("SELECT * FROM radgroupreply WHERE groupname = :groupname");
$stmt->execute(['groupname' => $groupname]);
$permissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <title>แก้ไขกลุ่ม | FreeRADIUS Admin</title>
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
        <h2>แก้ไขกลุ่ม: <?php echo htmlspecialchars($groupname); ?></h2>

        <form action="../actions/update_group.php" method="POST">
            <input type="hidden" name="old_groupname" value="<?php echo htmlspecialchars($groupname); ?>">

            <div class="mb-3">
                <label>ชื่อกลุ่มใหม่:</label>
                <input type="text" class="form-control" name="new_groupname"
                    value="<?php echo htmlspecialchars($groupname); ?>" required>
            </div>
            <div class="mb-3">
                <label>Simultaneous-Use (จำนวนอุปกรณ์ที่ล็อกอินพร้อมกัน):</label>
                <input type="number" class="form-control" name="simultaneous_use"
                    value="<?php echo getGroupCheck($pdo, $groupname, 'Simultaneous-Use'); ?>" required>
            </div>
            <div class="mb-3">
                <label>Idle-Timeout (วินาที):</label>
                <input type="number" class="form-control" name="idle_timeout"
                    value="<?php echo getPermission($permissions, 'Idle-Timeout'); ?>" required>
            </div>

            <div class="mb-3">
                <label>Session-Timeout (วินาที):</label>
                <input type="number" class="form-control" name="session_timeout"
                    value="<?php echo getPermission($permissions, 'Session-Timeout'); ?>" required>
            </div>

            <div class="mb-3">
                <label>Mikrotik-Rate-Limit (Upload/Download kbps เช่น 512k/1m) ตัวพิมพ์เล็ก:</label>
                <input type="text" class="form-control" name="rate_limit"
                    value="<?php echo getPermission($permissions, 'Mikrotik-Rate-Limit'); ?>" required>
            </div>

            <div class="mb-3">
                <label>WISPr-Bandwidth-Max-Up (bps: 1Kbps = 1,000 , 1Mbps = 1,000,000):</label>
                <input type="number" class="form-control" name="wispr_up"
                    value="<?php echo (int)(getPermission($permissions, 'WISPr-Bandwidth-Max-Up')); ?>" required>
            </div>

            <div class="mb-3">
                <label>WISPr-Bandwidth-Max-Down (bps: 1Kbps = 1,000 , 1Mbps = 1,000,000):</label>
                <input type="number" class="form-control" name="wispr_down"
                    value="<?php echo (int)(getPermission($permissions, 'WISPr-Bandwidth-Max-Down')); ?>" required>
            </div>

            <button type="submit" class="btn btn-success">บันทึก</button>
            <a href="groups.php" class="btn btn-secondary">ยกเลิก</a>
        </form>
    </div>
</body>

</html>