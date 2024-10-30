<?php
session_start();
$message = "";

$servername = "localhost";
$usernameDB = "root"; // Tên đăng nhập mặc định của XAMPP
$passwordDB = ""; // Mật khẩu mặc định của XAMPP thường để trống
$dbname = "resinshop";

// Tạo kết nối
$conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Truy vấn người dùng từ cơ sở dữ liệu
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Kiểm tra người dùng và mật khẩu
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION["loggedin"] = true;
            $_SESSION["username"] = $username;
            header("Location: index.php");
            exit;
        } else {
            $message = "Mật khẩu không đúng!";
        }
    } else {
        $message = "Tên đăng nhập không đúng!";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Đăng Nhập - RESINSHOP</title>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: Arial, sans-serif;
    }

    body {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        background-color: #b0c4de;
        /* Màu xanh xám */
    }

    .login-container {
        background-color: #ffffff;
        width: 1050px;
        /* Tăng chiều rộng lên gấp 3 lần */
        padding: 120px;
        /* Tăng padding lên gấp 3 lần */
        border-radius: 10px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        text-align: center;
    }


    h2 {
        font-size: 66px;
        /* Tăng kích thước phông chữ lên gấp 3 lần (từ 22px) */
        color: #333;
        font-weight: bold;
        margin-bottom: 20px;
    }

    form {
        display: flex;
        flex-direction: column;
    }

    label {
        font-size: 14px;
        color: #555;
        margin: 10px 0 5px;
        text-align: left;
    }

    input[type="text"],
    input[type="password"] {
        padding: 10px;
        font-size: 14px;
        border: 1px solid #ddd;
        border-radius: 5px;
        background-color: #f9f9f9;
        margin-bottom: 15px;
        outline: none;
        transition: border-color 0.3s;
    }

    input[type="text"]:focus,
    input[type="password"]:focus {
        border-color: #3b82f6;
    }

    button[type="submit"] {
        padding: 12px;
        font-size: 16px;
        color: #ffffff;
        background-color: #3b82f6;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    button[type="submit"]:hover {
        background-color: #2563eb;
    }

    .error {
        color: #e11d48;
        font-size: 14px;
        margin-top: 10px;
    }

    p {
        font-size: 14px;
        color: #666;
        margin-top: 20px;
    }

    p a {
        color: #3b82f6;
        text-decoration: none;
        font-weight: bold;
        transition: color 0.3s;
    }

    p a:hover {
        color: #2563eb;
    }
    </style>
</head>

<body>
    <div class="login-container">
        <h2>CHÀO MỪNG BẠN ĐẾN VỚI RESINSHOP</h2>
        <form action="login.php" method="POST">
            <label for="username">Tên đăng nhập</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Mật khẩu</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Đăng nhập</button>
        </form>
        <?php if ($message): ?>
        <p class="error"><?php echo $message; ?></p>
        <?php endif; ?>

        <p>Chưa có tài khoản? <a href="register.php">Đăng ký</a></p>
    </div>
</body>

</html>