<?php
require_once '../config/config.php';

if (isset($_GET['username'])) {
    $username = $_GET['username'];

    // ลบข้อมูลผู้ใช้จาก radcheck (ข้อมูลรหัสผ่าน)
    $stmt = $pdo->prepare("DELETE FROM radcheck WHERE username = :username");
    $stmt->execute(['username' => $username]);

    // ลบข้อมูลผู้ใช้จาก radusergroup (กลุ่มที่ผู้ใช้สังกัด)
    $stmt = $pdo->prepare("DELETE FROM radusergroup WHERE username = :username");
    $stmt->execute(['username' => $username]);

    // ลบข้อมูลการเชื่อมต่อจาก radacct (ประวัติการใช้งาน)
    $stmt = $pdo->prepare("DELETE FROM radacct WHERE username = :username");
    $stmt->execute(['username' => $username]);

    // ลบข้อมูลการตรวจสอบย้อนหลังจาก radpostauth
    $stmt = $pdo->prepare("DELETE FROM radpostauth WHERE username = :username");
    $stmt->execute(['username' => $username]);
}

// กลับไปยังหน้าจัดการผู้ใช้
header("Location: ../public/users.php");
exit();
?>