<?php
require_once '../config/config.php';

if (isset($_GET['groupname'])) {
    $groupname = $_GET['groupname'];

    // ตรวจสอบว่ามีผู้ใช้ที่อยู่ในกลุ่มนี้หรือไม่
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM radusergroup WHERE groupname = :groupname");
    $stmt->execute(['groupname' => $groupname]);
    $userCount = $stmt->fetchColumn();

    if ($userCount > 0) {
        // หากยังมีผู้ใช้ในกลุ่ม ให้ Redirect กลับไปพร้อมแจ้งเตือน
        header("Location: ../public/groups.php?error=group_in_use");
        exit();
    }

    // ลบกลุ่มออกจาก radgroupcheck
    $stmt = $pdo->prepare("DELETE FROM radgroupcheck WHERE groupname = :groupname");
    $stmt->execute(['groupname' => $groupname]);

    // ลบกลุ่มออกจาก radgroupreply
    $stmt = $pdo->prepare("DELETE FROM radgroupreply WHERE groupname = :groupname");
    $stmt->execute(['groupname' => $groupname]);
}

// กลับไปยังหน้าจัดการกลุ่ม
header("Location: ../public/groups.php");
exit();
?>