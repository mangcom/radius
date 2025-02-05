<?php
session_start();
require_once '../config/config.php';

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 1) ดึงข้อมูลกลุ่ม (Distinct) จาก radgroupcheck
$stmt = $pdo->query("SELECT DISTINCT groupname FROM radgroupcheck ORDER BY groupname ASC");
$groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

/**
 * ฟังก์ชันดึง attribute ทั้งหมดใน radgroupcheck
 * คืนค่าเป็น array ของ ["attribute","op","value"] สำหรับ group นั้น ๆ
 */
function getCheckAttributes($pdo, $groupname) {
    $sql = "SELECT attribute, op, value 
            FROM radgroupcheck 
            WHERE groupname = :gname
            ORDER BY attribute ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['gname' => $groupname]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * ฟังก์ชันดึง attribute ทั้งหมดใน radgroupreply
 */
function getReplyAttributes($pdo, $groupname) {
    $sql = "SELECT attribute, op, value
            FROM radgroupreply
            WHERE groupname = :gname
            ORDER BY attribute ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['gname' => $groupname]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <title>จัดการกลุ่มผู้ใช้ | FreeRADIUS Admin</title>

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
        <h2>จัดการกลุ่มผู้ใช้</h2>

        <!-- ฟอร์มเพิ่มกลุ่ม -->
        <form action="../actions/add_group.php" method="POST" class="mb-3">
            <div class="input-group">
                <input type="text" class="form-control" name="groupname" placeholder="ชื่อกลุ่มใหม่" required>
                <button type="submit" class="btn btn-primary">เพิ่มกลุ่ม</button>
            </div>
        </form>

        <!-- แสดงรายการกลุ่ม (DataTables) -->
        <table class="table table-bordered" id="groupsTable">
            <thead>
                <tr>
                    <th style="width: 15%;">ชื่อกลุ่ม</th>
                    <th style="width: 60%;">รายละเอียด Attribute</th>
                    <th style="width: 25%;">การจัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($groups) > 0): ?>
                    <?php foreach ($groups as $group): ?>
                        <?php 
                            $gname = $group['groupname'];
                            $checkAtts = getCheckAttributes($pdo, $gname);    // Array of [attribute, op, value]
                            $replyAtts = getReplyAttributes($pdo, $gname);    // Array of [attribute, op, value]
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($gname); ?></td>
                            <td>
                                <!-- สร้างส่วน Check Attributes -->
                                <strong>Check Attributes:</strong><br>
                                <?php if (!empty($checkAtts)): ?>
                                    <ul class="mb-2">
                                    <?php foreach ($checkAtts as $att): ?>
                                        <li>
                                            <?php 
                                                echo htmlspecialchars($att['attribute']) . 
                                                     ' ' . htmlspecialchars($att['op']) . 
                                                     ' ' . htmlspecialchars($att['value']);
                                            ?>
                                        </li>
                                    <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <p>- ไม่มี Check Attribute -</p>
                                <?php endif; ?>

                                <!-- สร้างส่วน Reply Attributes -->
                                <strong>Reply Attributes:</strong><br>
                                <?php if (!empty($replyAtts)): ?>
                                    <ul>
                                    <?php foreach ($replyAtts as $att): ?>
                                        <li>
                                            <?php 
                                                echo htmlspecialchars($att['attribute']) . 
                                                     ' ' . htmlspecialchars($att['op']) . 
                                                     ' ' . htmlspecialchars($att['value']);
                                            ?>
                                        </li>
                                    <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <p>- ไม่มี Reply Attribute -</p>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="edit_group.php?groupname=<?php echo urlencode($gname); ?>" class="btn btn-warning btn-sm">จัดการ</a>
                                <a href="../actions/delete_group.php?groupname=<?php echo urlencode($gname); ?>" 
                                   class="btn btn-danger btn-sm" 
                                   onclick="return confirm('ต้องการลบกลุ่มนี้หรือไม่?');">ลบ</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="3" class="text-center">ไม่มีข้อมูลกลุ่ม</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- JS: jQuery, Bootstrap, DataTables -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

    <script>
      $(document).ready(function() {
         $('#groupsTable').DataTable({
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
