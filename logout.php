<?php
session_start();
$_SESSION = []; // Xóa tất cả các biến phiên
session_destroy(); // Huỷ phiên
header("Location: login.php"); // Chuyển hướng về trang đăng nhập
exit;
?>