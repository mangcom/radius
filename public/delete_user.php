<?php
require_once '../config/config.php';

if (isset($_GET['username'])) {
    $username = $_GET['username'];

    // ลบจาก radcheck
    $stmt = $pdo->prepare("DELETE FROM radcheck WHERE username = :username");
    $stmt->execute(['username' => $username]);

    // ลบจาก radusergroup
    $stmt = $pdo->prepare("DELETE FROM radusergroup WHERE username = :username");
    $stmt->execute(['username' => $username]);
}

header("Location: ../public/users.php");
exit();
?>