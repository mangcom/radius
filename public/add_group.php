<?php
require_once '../config/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $groupname = trim($_POST['groupname']);

    if (!empty($groupname)) {
        // ตรวจสอบว่ามีชื่อกลุ่มซ้ำหรือไม่
        $stmt = $pdo->prepare("SELECT * FROM radusergroup WHERE groupname = :groupname LIMIT 1");
        $stmt->execute(['groupname' => $groupname]);
        
        if ($stmt->rowCount() == 0) {
            // เพิ่มกลุ่มใหม่
            $stmt = $pdo->prepare("INSERT INTO radusergroup (groupname, priority) VALUES (:groupname, 1)");
            $stmt->execute(['groupname' => $groupname]);
        }
    }
}

header("Location: ../public/groups.php");
exit();