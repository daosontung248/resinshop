<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION["user_id"])) {
    $user_id = $_SESSION['user_id'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $email = $_POST['email'];

    $servername = "localhost";
    $usernameDB = "root";
    $passwordDB = "";
    $dbname = "resinshop";
    $conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);

    if ($conn->connect_error) {
        die("Kết nối thất bại: " . $conn->connect_error);
    }

    $sql = "UPDATE users SET phone = ?, address = ?, email = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $phone, $address, $email, $user_id);

    if ($stmt->execute()) {
        header("Location: checkout.php");
        exit;
    } else {
        echo "Lỗi khi lưu thông tin: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>