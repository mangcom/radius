<?php
require_once '../config/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $groupname = $_POST['groupname'];
    $permissions = [
        'Simultaneous-Use' => $_POST['simultaneous_use'],
        'Idle-Timeout' => $_POST['idle_timeout'],
        'Session-Timeout' => $_POST['session_timeout'],
        'Mikrotik-Rate-Limit' => $_POST['rate_limit'],
        'WISPr-Bandwidth-Max-Up' => (int)($_POST['wispr_up']),
        'WISPr-Bandwidth-Max-Down' => (int)($_POST['wispr_down']),
        'Acct-Interim-Interval' => (int)($_POST['interim-interval'])
    ];

    foreach ($permissions as $attribute => $value) {
        // $stmt = $pdo->prepare("DELETE FROM radgroupreply WHERE groupname = :groupname AND attribute = :attribute");
        // $stmt->execute(['groupname' => $groupname, 'attribute' => $attribute]);

        // $stmt = $pdo->prepare("INSERT INTO radgroupreply (groupname, attribute, op, value) VALUES (:groupname, :attribute, ':=', :value)");
        // $stmt->execute(['groupname' => $groupname, 'attribute' => $attribute, 'value' => $value]);
        // 1) Update
        $stmt = $pdo->prepare("
        UPDATE radgroupreply
        SET op = ':=', value = :value
        WHERE groupname = :groupname AND attribute = :attribute
        ");
        $stmt->execute([
        'value' => $value,
        'groupname' => $groupname,
        'attribute' => $attribute
        ]);

        // ดูจำนวนแถวที่ถูกอัปเดต
        $affected = $stmt->rowCount();

        if ($affected === 0) {
        // 2) ไม่มีแถว (แปลว่าของเดิมไม่ซ้ำ) -> Insert
        $stmt = $pdo->prepare("
            INSERT INTO radgroupreply (groupname, attribute, op, value)
            VALUES (:groupname, :attribute, ':=', :value)
        ");
        $stmt->execute([
            'groupname' => $groupname,
            'attribute' => $attribute,
            'value' => $value
        ]);
        }
    }
}

header("Location: ../public/group_permissions.php?groupname=" . urlencode($groupname));
exit();
?>