<?php
require_once '../config/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $groupname = $_POST['groupname'];

    // อัปเดตรหัสผ่านถ้ามีการเปลี่ยนแปลง
    if (!empty($password)) {
        // $hashed_password = md5($password); // ใช้ MD5 ตามมาตรฐาน FreeRADIUS
        $hashed_password = $password; // ใช้ ClearText-Password ตามมาตรฐาน FreeRADIUS
        $stmt = $pdo->prepare("UPDATE radcheck SET value = :password WHERE username = :username AND attribute = 'Cleartext-Password'");
        $stmt->execute(['password' => $hashed_password, 'username' => $username]);
    }

    // อัปเดตกลุ่มผู้ใช้
    if (!empty($groupname)) {
        // ลบกลุ่มเก่า
        $stmt = $pdo->prepare("DELETE FROM radusergroup WHERE username = :username");
        $stmt->execute(['username' => $username]);

        // เพิ่มกลุ่มใหม่
        $stmt = $pdo->prepare("INSERT INTO radusergroup (username, groupname, priority) VALUES (:username, :groupname, 1)");
        $stmt->execute(['username' => $username, 'groupname' => $groupname]);
    }
}

header("Location: ../public/users.php");
exit();
?>