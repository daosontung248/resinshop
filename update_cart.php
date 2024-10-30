<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['product_id'])) {
        $product_id = $_POST['product_id'];

        // Xử lý nút tăng số lượng
        if (isset($_POST['increase'])) {
            $_SESSION['cart'][$product_id]++;
        }

        // Xử lý nút giảm số lượng
        if (isset($_POST['decrease']) && $_SESSION['cart'][$product_id] > 1) {
            $_SESSION['cart'][$product_id]--;
        }

        // Xử lý nút xóa sản phẩm
        if (isset($_POST['remove'])) {
            unset($_SESSION['cart'][$product_id]);
        }
    }
}

header("Location: view_cart.php");
exit;