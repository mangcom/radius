<?php
session_start();
require_once '../config/config.php';

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// ดึงข้อมูลผู้ใช้ทั้งหมดจาก radcheck (เลือก username distinct)
$stmt = $pdo->query("SELECT DISTINCT username FROM radcheck ORDER BY username ASC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ดึงรายการกลุ่มจาก radgroupcheck
$stmt = $pdo->query("SELECT DISTINCT groupname FROM radgroupcheck ORDER BY groupname ASC");
$groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

function getUserGroup($pdo, $username) {
    $stmt = $pdo->prepare("SELECT groupname FROM radusergroup WHERE username = :username LIMIT 1");
    $stmt->execute(['username' => $username]);
    $group = $stmt->fetch(PDO::FETCH_ASSOC);
    return $group ? htmlspecialchars($group['groupname']) : '-';
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <title>จัดการผู้ใช้ | FreeRADIUS Admin</title>
    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <!-- DataTables CSS + Bootstrap5 Integration -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">

</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand btn btn-warning" href="index.php">FreeRADIUS Admin Menu</a>
            <a class="btn btn-danger" href="logout.php">ออกจากระบบ</a>
        </div>
    </nav>
    <div class="container mt-4">
        <h2>จัดการผู้ใช้</h2>

        <!-- ฟอร์มเพิ่มผู้ใช้ -->
        <form action="../actions/add_user.php" method="POST" class="mb-3">
            <div class="row">
                <div class="col-md-3">
                    <input type="text" class="form-control" name="username" placeholder="ชื่อผู้ใช้" required>
                </div>
                <div class="col-md-3">
                    <input type="password" class="form-control" name="password" placeholder="รหัสผ่าน" required>
                </div>
                <div class="col-md-3">
                    <select name="groupname" class="form-select" required>
                        <option value="">-- เลือกกลุ่ม --</option>
                        <?php foreach ($groups as $group): ?>
                            <option value="<?php echo $group['groupname']; ?>">
                                <?php echo $group['groupname']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">เพิ่มผู้ใช้</button>
                </div>
            </div>
        </form>

        <!-- แสดงรายการผู้ใช้ -->
        <table class="table table-bordered" id="usersTable">
            <thead>
                <tr>
                    <th>ชื่อผู้ใช้</th>
                    <th>กลุ่ม</th>
                    <th>การจัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo getUserGroup($pdo, $user['username']); ?></td>
                    <td>
                        <a href="edit_user.php?username=<?php echo urlencode($user['username']); ?>"
                           class="btn btn-warning btn-sm">แก้ไข</a>
                        <a href="../actions/delete_user.php?username=<?php echo urlencode($user['username']); ?>"
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('ต้องการลบผู้ใช้นี้หรือไม่?');">ลบ</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- JS: jQuery, Bootstrap, DataTables -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

    <!-- สคริปต์เริ่มต้น DataTables -->
    <script>
      $(document).ready(function() {
         $('#usersTable').DataTable({
             // กำหนด option เพิ่มเติมได้ เช่น การตั้งค่าภาษา หรือ page length
             "pageLength": 10,
             "language": {
                 "lengthMenu": "แสดง _MENU_ แถวต่อหน้า",
                 "search": "ค้นหา:",
                 "zeroRecords": "ไม่พบข้อมูล",
                 "info": "แสดงหน้า _PAGE_ จาก _PAGES_",
                 "infoEmpty": "ไม่มีข้อมูล",
                 "infoFiltered": "(กรองจากทั้งหมด _MAX_ แถว)",
                 "paginate": {
                     "first": "หน้าแรก",
                     "last": "หน้าสุดท้าย",
                     "next": "ถัดไป",
                     "previous": "ก่อนหน้า"
                 },
             }
         });
      });
    </script>
</body>
</html>
