<?php
session_start();
require_once '../config/config.php';

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// ดึงข้อมูลกลุ่มทั้งหมด
$stmt = $pdo->query("SELECT DISTINCT groupname FROM radusergroup ORDER BY groupname ASC");
$groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ดึงค่าปัจจุบันของสิทธิ์กลุ่ม
$permissions = [];
if (isset($_GET['groupname'])) {
    $groupname = $_GET['groupname'];
    $stmt = $pdo->prepare("SELECT * FROM radgroupreply WHERE groupname = :groupname");
    $stmt->execute(['groupname' => $groupname]);
    $permissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <title>จัดการสิทธิ์กลุ่ม | FreeRADIUS Admin</title>
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
        <h2>จัดการสิทธิ์ของกลุ่ม</h2>

        <form method="GET" action="group_permissions.php">
            <label for="groupname">เลือกกลุ่ม:</label>
            <select name="groupname" class="form-select" onchange="this.form.submit()">
                <option value="">-- เลือกกลุ่ม --</option>
                <?php foreach ($groups as $group): ?>
                <option value="<?php echo $group['groupname']; ?>"
                    <?php if (isset($_GET['groupname']) && $_GET['groupname'] == $group['groupname']) echo 'selected'; ?>>
                    <?php echo $group['groupname']; ?>
                </option>
                <?php endforeach; ?>
            </select>
        </form>

        <?php if (isset($groupname)): ?>
        <form action="../actions/update_group_permissions.php" method="POST" class="mt-4">
            <input type="hidden" name="groupname" value="<?php echo htmlspecialchars($groupname); ?>">

            <div class="mb-3">
                <label>Simultaneous-Use (จำนวนอุปกรณ์ที่ล็อกอินพร้อมกัน):</label>
                <input type="number" class="form-control" name="simultaneous_use"
                    value="<?php echo getPermission($permissions, 'Simultaneous-Use'); ?>" required>
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
                <label>Mikrotik-Rate-Limit (Upload/Download kbps เช่น 512k/1M):</label>
                <input type="text" class="form-control" name="rate_limit"
                    value="<?php echo getPermission($permissions, 'Mikrotik-Rate-Limit'); ?>" required>
            </div>

            <div class="mb-3">
                <label>WISPr-Bandwidth-Max-Up (Mbps):</label>
                <input type="number" class="form-control" name="wispr_up"
                    value="<?php echo getPermission($permissions, 'WISPr-Bandwidth-Max-Up'); ?>" required>
            </div>

            <div class="mb-3">
                <label>WISPr-Bandwidth-Max-Down (Mbps):</label>
                <input type="number" class="form-control" name="wispr_down"
                    value="<?php echo getPermission($permissions, 'WISPr-Bandwidth-Max-Down'); ?>" required>
            </div>

            <button type="submit" class="btn btn-success">บันทึก</button>
        </form>
        <?php endif; ?>
    </div>
</body>

</html>

<?php
function getPermission($permissions, $attribute) {
    foreach ($permissions as $perm) {
        if ($perm['attribute'] == $attribute) {
            return htmlspecialchars($perm['value']);
        }
    }
    return "";
}
?>