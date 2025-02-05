<?php
$host = "localhost";  // เปลี่ยนเป็น IP ของเซิร์ฟเวอร์ฐานข้อมูลถ้าใช้ remote
$dbname = "radius_db";  // ชื่อฐานข้อมูล FreeRADIUS
$username = "radius_user";  // ชื่อผู้ใช้ที่กำหนดไว้ใน MariaDB
$password = "radius_pass123";  // รหัสผ่านของ MariaDB

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>