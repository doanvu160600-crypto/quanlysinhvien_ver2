
<?php
session_start();

// Kiểm tra quyền Admin
if (!isset($_SESSION["role"]) || $_SESSION["role"] != "admin") {
    header("Location: ../auth/login.php");
    exit();
}

require_once "../config/ketnoi.php";

// Chỉ cho phép xóa bằng phương thức POST
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: faculty_list.php");
    exit();
}

$makhoa = trim($_POST["makhoa"] ?? "");

if ($makhoa == "") {
    header("Location: faculty_list.php");
    exit();
}

try {

    // Xóa khoa
    $sql = "DELETE FROM khoa WHERE makhoa = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$makhoa]);

    if ($stmt->rowCount() > 0) {
        header("Location: faculty_list.php?msg=deleted");
    } else {
        header("Location: faculty_list.php?msg=error");
    }

    exit();

} catch (PDOException $e) {

    // Không thể xóa nếu khoa đang có lớp liên kết
    header("Location: faculty_list.php?msg=error");
    exit();
}
