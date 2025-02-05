<?php
require_once '../config/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $old_groupname = $_POST['old_groupname'];
    $new_groupname = $_POST['new_groupname'];
    $simultaneous_use = $_POST['simultaneous_use'];
    $permissions = [
        'Idle-Timeout' => $_POST['idle_timeout'],
        'Session-Timeout' => $_POST['session_timeout'],
        'Mikrotik-Rate-Limit' => strtolower(trim($_POST['rate_limit'])),
        'WISPr-Bandwidth-Max-Up' => $_POST['wispr_up'],
        'WISPr-Bandwidth-Max-Down' => $_POST['wispr_down'],
        'Acct-Interim-Interval' => $_POST['interim-interval']
    ];

    try {
        $pdo->beginTransaction();

        // **อัปเดตชื่อกลุ่มใน `radgroupcheck`**
        $stmt = $pdo->prepare("UPDATE radgroupcheck SET groupname = :new_groupname WHERE groupname = :old_groupname");
        $stmt->execute(['new_groupname' => $new_groupname, 'old_groupname' => $old_groupname]);

        // **อัปเดตชื่อกลุ่มใน `radgroupreply`**
        $stmt = $pdo->prepare("UPDATE radgroupreply SET groupname = :new_groupname WHERE groupname = :old_groupname");
        $stmt->execute(['new_groupname' => $new_groupname, 'old_groupname' => $old_groupname]);

        // **อัปเดต `Simultaneous-Use` ใน `radgroupcheck`**
        $stmt = $pdo->prepare("UPDATE radgroupcheck SET value = :value WHERE groupname = :groupname AND attribute = 'Simultaneous-Use'");
        $stmt->execute(['groupname' => $new_groupname, 'value' => $simultaneous_use]);

        // **อัปเดตค่าการตั้งค่าใน `radgroupreply`**
        foreach ($permissions as $attribute => $value) {
            $stmt = $pdo->prepare("UPDATE radgroupreply SET value = :value WHERE groupname = :groupname AND attribute = :attribute");
            $stmt->execute(['groupname' => $new_groupname, 'attribute' => $attribute, 'value' => $value]);
        }

        // **อัปเดตชื่อกลุ่มใน `radusergroup` (หากมีผู้ใช้อยู่ในกลุ่ม)**
        $stmt = $pdo->prepare("UPDATE radusergroup SET groupname = :new_groupname WHERE groupname = :old_groupname");
        $stmt->execute(['new_groupname' => $new_groupname, 'old_groupname' => $old_groupname]);

        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        die("เกิดข้อผิดพลาด: " . $e->getMessage());
    }
}

header("Location: ../public/groups.php");
exit();
?>