<?php
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: login.php");
    exit;
}

$servername = "localhost";
$usernameDB = "root";
$passwordDB = "";
$dbname = "resinshop";

$conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Xử lý xóa sản phẩm
if (isset($_POST['remove'])) {
    $product_id = $_POST['remove'];
    unset($_SESSION['cart'][$product_id]);
}

// Xử lý cập nhật số lượng sản phẩm
if (isset($_POST['update'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $_SESSION['cart'][$product_id] = $quantity;
}

// Xử lý thanh toán
if (isset($_POST['checkout'])) {
    $selected_products = $_POST['selected_products'] ?? [];

    if (empty($selected_products)) {
        echo "<script>alert('Bạn cần chọn ít nhất một sản phẩm để thanh toán.');</script>";
    } else {
        // Hiển thị thông báo cảm ơn cho mỗi sản phẩm được chọn
        echo "<script>alert('Cảm ơn bạn đã mua sản phẩm: " . implode(", ", $selected_products) . "');</script>";

        // Xóa sản phẩm đã thanh toán khỏi giỏ hàng
        foreach ($selected_products as $product_id) {
            unset($_SESSION['cart'][$product_id]);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ Hàng - RESINSHOP</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #ffcc80;
        margin: 0;
        padding: 0;
        color: #333;
    }

    .header {
        background-color: #4285f4;
        color: #fff;
        padding: 15px 20px;
        display: flex;
        align-items: center;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .home-icon {
        color: white;
        font-size: 30px;
        text-decoration: none;
        margin-right: 20px;
    }

    .product-container {
        padding: 20px;
        max-width: 1200px;
        margin: auto;
    }

    .product-card {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .product-card img {
        width: 100px;
        height: auto;
        border-radius: 5px;
    }

    .product-info {
        flex: 1;
        margin-left: 15px;
    }

    .quantity-control {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .quantity-control input {
        width: 40px;
        text-align: center;
    }

    .remove-button,
    .checkout-button {
        background-color: #ff5722;
        color: white;
        padding: 8px 12px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .remove-button:hover,
    .checkout-button:hover {
        background-color: #e64a19;
    }

    .footer {
        text-align: center;
        padding: 10px;
        background: #f1f4f8;
        color: #333;
        border-top: 1px solid #ddd;
        margin-top: 20px;
    }
    </style>
</head>

<body>
    <div class="header">
        <a href="index.php" class="home-icon">
            <i class="fas fa-home"></i>
        </a>
        <h1>Giỏ Hàng</h1>
        <div class="user-info">
            <span><?php echo htmlspecialchars($_SESSION["username"]); ?></span>
            <a href="logout.php">
                <button class="logout-button">Đăng xuất</button>
            </a>
        </div>
    </div>

    <div class="product-container">
        <?php if (empty($_SESSION['cart'])): ?>
        <h2>Giỏ hàng trống!</h2>
        <?php else: ?>
        <h2>Giỏ hàng của bạn</h2>
        <form method="POST">
            <?php 
                    foreach ($_SESSION['cart'] as $product_id => $quantity): 
                        $sql = "SELECT * FROM products WHERE id = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $product_id);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $product = $result->fetch_assoc();

                        if ($product) {
                            $total_price = $product['price'] * $quantity; // Tính tổng giá cho sản phẩm
                ?>
            <div class="product-card">
                <input type="checkbox" name="selected_products[]" value="<?php echo $product_id; ?>">
                <img src="images/<?php echo htmlspecialchars($product['image']); ?>"
                    alt="<?php echo htmlspecialchars($product['name']); ?>">
                <div class="product-info">
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p>Giá: <?php echo number_format($product['price'], 2); ?> VNĐ</p>
                    <p>Tổng: <?php echo number_format($total_price, 2); ?> VNĐ</p> <!-- Hiển thị tổng giá sản phẩm -->
                    <div class="quantity-control">
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                            <input type="number" name="quantity" value="<?php echo $quantity; ?>" min="1" required>
                            <button type="submit" name="update">Cập nhật</button>
                        </form>
                        <button type="submit" name="remove" value="<?php echo $product_id; ?>"
                            class="remove-button">Xóa</button>
                    </div>
                </div>
            </div>
            <?php 
                        }
                    endforeach; 
                ?>
            <button type="submit" name="checkout" class="checkout-button">Thanh Toán</button>
        </form>
        <?php endif; ?>
    </div>

    <div class="footer">
        <p>&copy; 2024 RESINSHOP. Tất cả các quyền được bảo lưu.</p>
    </div>

    <?php
    $stmt->close();
    $conn->close();
    ?>
</body>

</html>