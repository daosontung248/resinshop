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

// Xử lý tìm kiếm
$search = "";
if (isset($_POST['search'])) {
    $search = $_POST['search'];
}

// Truy vấn sản phẩm
$sql = "SELECT * FROM products WHERE name LIKE ?";
$stmt = $conn->prepare($sql);
$search_param = "%" . $search . "%";
$stmt->bind_param("s", $search_param);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RESINSHOP - Trang Chủ</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f1f4f8;
        margin: 0;
        padding: 0;
        color: #333;
    }

    .header {
        background-color: #4285f4;
        color: #fff;
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .header h1 {
        font-size: 24px;
        margin: 0;
        display: flex;
        align-items: center;
    }

    .header form {
        display: flex;
        align-items: center;
    }

    .header input[type="text"] {
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
        margin-right: 10px;
    }

    .header button {
        padding: 8px 15px;
        border: none;
        background-color: #ff5722;
        color: white;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .header button:hover {
        background-color: #e64a19;
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .product-container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        padding: 20px;
        justify-content: center;
        max-width: 1200px;
        margin: auto;
    }

    .product-card {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        width: 220px;
        padding: 15px;
        text-align: center;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: box-shadow 0.3s;
    }

    .product-card:hover {
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
    }

    .product-card img {
        width: 100%;
        height: auto;
        border-radius: 5px;
        margin-bottom: 10px;
    }

    .product-info h2 {
        font-size: 18px;
        color: #333;
        margin: 10px 0;
    }

    .quantity-control {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        margin: 10px 0;
    }

    .quantity-control button {
        padding: 6px 10px;
        border: 1px solid #ddd;
        background-color: #f9f9f9;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .quantity-control button:hover {
        background-color: #e0e0e0;
    }

    .quantity-control input {
        width: 40px;
        text-align: center;
        border: 1px solid #ddd;
        padding: 5px;
    }

    .add-to-cart {
        background-color: #ff5722;
        color: #fff;
        padding: 10px 15px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .add-to-cart:hover {
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
        <h1>
            <a href="index.php" style="color: white; text-decoration: none;">
                <i class="fas fa-home"></i> RESINSHOP
            </a>
        </h1>
        <form action="index.php" method="POST">
            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>"
                placeholder="Tìm sản phẩm..." required>
            <button type="submit">Tìm</button>
        </form>
        <div class="user-info">
            <span><?php echo htmlspecialchars($_SESSION["username"]); ?></span>
            <a href="view_cart.php" style="color: white;">
                <i class="fas fa-shopping-cart"></i>
            </a>
            <a href="logout.php">
                <button class="logout-button">Đăng xuất</button>
            </a>
        </div>
    </div>

    <div class="product-container">
        <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
        <div class="product-card">
            <img src="images/<?php echo htmlspecialchars($row['image']); ?>"
                alt="<?php echo htmlspecialchars($row['name']); ?>">
            <div class="product-info">
                <h2><?php echo htmlspecialchars($row['name']); ?></h2>
                <p><?php echo htmlspecialchars($row['description']); ?></p>
                <p>Giá: <?php echo number_format($row['price'], 2); ?> VNĐ</p>
                <p>Số lượng còn: <?php echo $row['quantity']; ?></p>

                <form action="cart.php" method="POST">
                    <div class="quantity-control">
                        <button type="button" onclick="decrement(<?php echo $row['id']; ?>)">-</button>
                        <input type="number" id="quantity_<?php echo $row['id']; ?>" name="quantity" value="1" min="1"
                            max="<?php echo $row['quantity']; ?>" readonly>
                        <button type="button"
                            onclick="increment(<?php echo $row['id']; ?>, <?php echo $row['quantity']; ?>)">+</button>
                    </div>
                    <button type="submit" class="add-to-cart" name="add_to_cart" value="<?php echo $row['id']; ?>">Thêm
                        vào giỏ</button>
                </form>
            </div>
        </div>
        <?php endwhile; ?>
        <?php else: ?>
        <p>Không tìm thấy sản phẩm nào.</p>
        <?php endif; ?>
    </div>

    <div class="footer">
        <p>&copy; 2024 RESINSHOP. Tất cả các quyền được bảo lưu.</p>
    </div>

    <script>
    function increment(id, maxQty) {
        let quantityInput = document.getElementById("quantity_" + id);
        let currentQuantity = parseInt(quantityInput.value);
        if (currentQuantity < maxQty) {
            quantityInput.value = currentQuantity + 1;
        } else {
            alert("Quá số lượng hàng còn lại!");
        }
    }

    function decrement(id) {
        let quantityInput = document.getElementById("quantity_" + id);
        let currentQuantity = parseInt(quantityInput.value);
        if (currentQuantity > 1) {
            quantityInput.value = currentQuantity - 1;
        } else {
            alert("Số lượng không thể nhỏ hơn 1!");
        }
    }
    </script>

    <?php
    $stmt->close();
    $conn->close();
    ?>
</body>

</html>