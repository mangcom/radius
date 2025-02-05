<?php
require_once '../config/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $groupname = $_POST['groupname'];

    if (!empty($username) && !empty($password) && !empty($groupname)) {
        // ตรวจสอบว่าผู้ใช้มีอยู่แล้วหรือไม่
        $stmt = $pdo->prepare("SELECT * FROM radcheck WHERE username = :username LIMIT 1");
        $stmt->execute(['username' => $username]);

        if ($stmt->rowCount() == 0) {
            // เข้ารหัสรหัสผ่านด้วย bcrypt
            // $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $hashed_password = $password;

            // เพิ่มผู้ใช้ลงใน radcheck (รหัสผ่านแบบเข้ารหัส)
            $stmt = $pdo->prepare("INSERT INTO radcheck (username, attribute, op, value) VALUES (:username, 'Cleartext-Password', ':=', :password)");
            $stmt->execute(['username' => $username, 'password' => $hashed_password]);

            // เพิ่มผู้ใช้ลงกลุ่ม radusergroup
            $stmt = $pdo->prepare("INSERT INTO radusergroup (username, groupname, priority) VALUES (:username, :groupname, 1)");
            $stmt->execute(['username' => $username, 'groupname' => $groupname]);
        }
    }
}

header("Location: ../public/users.php");
exit();
?>