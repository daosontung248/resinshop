<?php
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: login.php");
    exit;
}

$total_price = $_SESSION['total_price'] ?? 0; // Lấy tổng giá từ session
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh Toán - RESINSHOP</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="container">
        <h1>Cảm ơn bạn đã mua hàng!</h1>
        <p>Tổng giá bạn phải thanh toán: <strong><?php echo number_format($total_price, 2); ?> VNĐ</strong></p>
        <p>Chúng tôi sẽ liên hệ với bạn sớm nhất có thể để xác nhận đơn hàng.</p>
        <a href="index.php">Quay lại trang chủ</a>
    </div>
</body>

</html>