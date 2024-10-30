<?php
session_start();

// Kiểm tra xem sản phẩm có trong giỏ hàng hay không
if (isset($_POST['product_id']) && isset($_SESSION['cart'][$_POST['product_id']])) {
    $product_id = $_POST['product_id'];
    unset($_SESSION['cart'][$product_id]); // Xóa sản phẩm khỏi giỏ hàng
}

// Quay lại trang giỏ hàng
header("Location: view_cart.php");
exit;
?>