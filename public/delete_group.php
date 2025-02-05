<?php
require_once '../config/config.php';

if (isset($_GET['groupname'])) {
    $groupname = $_GET['groupname'];

    // ลบกลุ่มออกจากฐานข้อมูล
    $stmt = $pdo->prepare("DELETE FROM radusergroup WHERE groupname = :groupname");
    $stmt->execute(['groupname' => $groupname]);
}

header("Location: ../public/groups.php");
exit();