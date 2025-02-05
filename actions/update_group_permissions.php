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
        'WISPr-Bandwidth-Max-Down' => (int)($_POST['wispr_down'])
    ];

    foreach ($permissions as $attribute => $value) {
        $stmt = $pdo->prepare("DELETE FROM radgroupreply WHERE groupname = :groupname AND attribute = :attribute");
        $stmt->execute(['groupname' => $groupname, 'attribute' => $attribute]);

        $stmt = $pdo->prepare("INSERT INTO radgroupreply (groupname, attribute, op, value) VALUES (:groupname, :attribute, ':=', :value)");
        $stmt->execute(['groupname' => $groupname, 'attribute' => $attribute, 'value' => $value]);
    }
}

header("Location: ../public/group_permissions.php?groupname=" . urlencode($groupname));
exit();
?>