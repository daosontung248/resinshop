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

    // Kiểm tra xem tên người dùng đã tồn tại chưa
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $message = "Tên đăng nhập đã tồn tại!";
    } else {
        // Mã hóa mật khẩu
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Thêm người dùng vào cơ sở dữ liệu
        $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $hashed_password);

        if ($stmt->execute()) {
            // Hiển thị thông báo đăng ký thành công và chuyển hướng
            echo "<script>
                    alert('Đăng ký thành công! Bạn sẽ được chuyển đến trang đăng nhập.');
                    window.location.href = 'login.php';
                  </script>";
            exit();
        } else {
            $message = "Đăng ký thất bại. Vui lòng thử lại.";
        }
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Đăng Ký - RESINSHOP</title>
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

    .register-container {
        background-color: #ffffff;
        width: 1050px;
        /* Chiều rộng hộp đăng ký */
        padding: 120px;
        /* Padding cho hộp đăng ký */
        border-radius: 10px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    h2 {
        font-size: 66px;
        /* Kích thước chữ lớn */
        color: #333;
        font-weight: bold;
        margin-bottom: 20px;
    }

    form {
        display: flex;
        flex-direction: column;
        align-items: center;
        /* Canh giữa nội dung */
    }

    label {
        font-size: 18px;
        color: #555;
        margin: 10px 0 5px;
        text-align: left;
        width: 100%;
        max-width: 300px;
    }

    input[type="text"],
    input[type="password"] {
        width: 100%;
        max-width: 300px;
        padding: 10px;
        font-size: 16px;
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
        width: 100%;
        max-width: 300px;
    }

    button[type="submit"]:hover {
        background-color: #2563eb;
    }

    .message {
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
    <div class="register-container">
        <h2>ĐĂNG KÝ TÀI KHOẢN MỚI</h2>
        <form action="register.php" method="POST">
            <label for="username">Tên đăng nhập</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Mật khẩu</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Đăng ký</button>
        </form>
        <?php if ($message): ?>
        <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>
        <p>Đã có tài khoản? <a href="login.php">Đăng nhập</a></p>
    </div>
</body>

</html>