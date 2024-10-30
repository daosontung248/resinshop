<?php
session_start();

// Kiểm tra nếu giỏ hàng chưa được khởi tạo
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Kiểm tra nếu có sản phẩm được thêm vào giỏ hàng
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['add_to_cart'];
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

    // Kiểm tra nếu sản phẩm đã có trong giỏ hàng
    if (array_key_exists($product_id, $_SESSION['cart'])) {
        $_SESSION['cart'][$product_id] += $quantity; // Tăng số lượng
    } else {
        $_SESSION['cart'][$product_id] = $quantity; // Thêm sản phẩm mới
    }

    // Chuyển hướng về trang chủ hoặc trang giỏ hàng
    header("Location: index.php"); // Hoặc chuyển đến trang giỏ hàng
    exit;
}
?>