<?php
require_once '../config/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $groupname = $_POST['groupname'];

    if (!empty($username) && !empty($password) && !empty($groupname)) {
        // เข้ารหัสรหัสผ่าน (MD5 ตามมาตรฐาน FreeRADIUS)
        $hashed_password = md5($password);

        // เพิ่มข้อมูลลงตาราง radcheck
        $stmt = $pdo->prepare("INSERT INTO radcheck (username, attribute, op, value) VALUES (:username, 'Cleartext-Password', ':=', :password)");
        $stmt->execute(['username' => $username, 'password' => $hashed_password]);

        // เพิ่มผู้ใช้ลงกลุ่ม
        $stmt = $pdo->prepare("INSERT INTO radusergroup (username, groupname, priority) VALUES (:username, :groupname, 1)");
        $stmt->execute(['username' => $username, 'groupname' => $groupname]);
    }
}

header("Location: ../public/users.php");
exit();
?>