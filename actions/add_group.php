<?php
require_once '../config/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $groupname = trim($_POST['groupname']);

    if (!empty($groupname)) {
        // ตรวจสอบว่ากลุ่มมีอยู่แล้วหรือไม่
        $stmt = $pdo->prepare("SELECT * FROM radgroupcheck WHERE groupname = :groupname LIMIT 1");
        $stmt->execute(['groupname' => $groupname]);

        if ($stmt->rowCount() == 0) {
            // เพิ่มค่าเริ่มต้นใน radgroupcheck (สำหรับตรวจสอบเงื่อนไข)
            $stmt = $pdo->prepare("INSERT INTO radgroupcheck (groupname, attribute, op, value) VALUES (:groupname, 'Simultaneous-Use', ':=', '2')");
            $stmt->execute(['groupname' => $groupname]);

            // เพิ่มค่าเริ่มต้นให้กับ radgroupreply (สำหรับกำหนดสิทธิ์)
            $default_permissions = [
                ['attribute' => 'Idle-Timeout', 'value' => '900'],
                ['attribute' => 'Session-Timeout', 'value' => '3600'],
                ['attribute' => 'Mikrotik-Rate-Limit', 'value' => '10m/10m'],
                ['attribute' => 'WISPr-Bandwidth-Max-Up', 'value' => '10485760'],
                ['attribute' => 'WISPr-Bandwidth-Max-Down', 'value' => '10485760']
            ];

            foreach ($default_permissions as $perm) {
                $stmt = $pdo->prepare("INSERT INTO radgroupreply (groupname, attribute, op, value) VALUES (:groupname, :attribute, ':=', :value)");
                $stmt->execute([
                    'groupname' => $groupname,
                    'attribute' => $perm['attribute'],
                    'value' => $perm['value']
                ]);
            }
        }
    }
}

header("Location: ../public/groups.php");
exit();
?>