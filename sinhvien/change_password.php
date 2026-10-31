
<?php
session_start();

// Kiểm tra quyền sinh viên
if (!isset($_SESSION["role"]) || $_SESSION["role"] != "sinhvien") {
    header("Location: ../auth/login.php");
    exit();
}

require_once "../config/ketnoi.php";

$mssv = $_SESSION["user_id"];
$thongBao = "";
$thanhCong = false;

// Xử lý khi gửi form
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $matKhauCu = $_POST["matkhaucu"] ?? "";
    $matKhauMoi = $_POST["matkhaumoi"] ?? "";
    $xacNhan = $_POST["xacnhan"] ?? "";

    // Lấy mật khẩu hiện tại trong database
    $sql = "SELECT password FROM sinh_vien WHERE mssv = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$mssv]);

    $sv = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$sv || !password_verify($matKhauCu, $sv["password"])) {

        $thongBao = "Mật khẩu hiện tại không đúng!";

    } elseif (strlen($matKhauMoi) < 6) {

        $thongBao = "Mật khẩu mới phải có ít nhất 6 ký tự!";

    } elseif ($matKhauMoi != $xacNhan) {

        $thongBao = "Mật khẩu xác nhận không khớp!";

    } else {

        try {
            // Mã hóa mật khẩu mới
            $matKhauHash = password_hash($matKhauMoi, PASSWORD_DEFAULT);

            // Cập nhật mật khẩu
            $sql = "UPDATE sinh_vien SET password = ? WHERE mssv = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$matKhauHash, $mssv]);

            $thongBao = "Đổi mật khẩu thành công!";
            $thanhCong = true;

        } catch (PDOException $e) {
            $thongBao = "Không thể đổi mật khẩu. Vui lòng thử lại!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đổi mật khẩu</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen">

    <!-- Header -->
    <header class="bg-blue-700 text-white shadow-md">
        <div class="max-w-5xl mx-auto px-6 py-5">
            <h1 class="text-2xl md:text-3xl font-bold">
                ĐỔI MẬT KHẨU
            </h1>

            <p class="text-blue-100 mt-1">
                Cập nhật mật khẩu tài khoản sinh viên
            </p>
        </div>
    </header>

    <main class="max-w-3xl mx-auto p-6">

        <!-- Thanh điều hướng -->
        <div class="flex flex-wrap gap-4 mb-6">
            <a href="index.php"
               class="text-blue-600 hover:text-blue-800 font-medium">
                Trang chủ
            </a>

            <a href="profile.php"
               class="text-blue-600 hover:text-blue-800 font-medium">
                Thông tin cá nhân
            </a>

            <a href="notifications.php"
               class="text-blue-600 hover:text-blue-800 font-medium">
                Thông báo
            </a>

            <a href="../auth/logout.php"
               class="text-red-600 hover:text-red-800 font-medium">
                Đăng xuất
            </a>
        </div>

        <!-- Form đổi mật khẩu -->
        <div class="bg-white rounded-xl shadow-md p-6">

            <h2 class="text-xl font-bold text-gray-800 mb-5">
                Cập nhật mật khẩu
            </h2>

            <!-- Thông báo -->
            <?php if ($thongBao != ""): ?>
                <div class="<?php echo $thanhCong
                    ? 'bg-green-100 text-green-700'
                    : 'bg-red-100 text-red-700'; ?>
                    p-3 rounded-lg mb-5">
                    <?php echo htmlspecialchars($thongBao); ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-5">

                <!-- Mật khẩu cũ -->
                <div>
                    <label class="block font-medium text-gray-700 mb-2">
                        Mật khẩu hiện tại
                    </label>

                    <input
                        type="password"
                        name="matkhaucu"
                        required
                        class="w-full border border-gray-300 rounded-lg px-4 py-3
                               focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Nhập mật khẩu hiện tại"
                    >
                </div>

                <!-- Mật khẩu mới -->
                <div>
                    <label class="block font-medium text-gray-700 mb-2">
                        Mật khẩu mới
                    </label>

                    <input
                        type="password"
                        name="matkhaumoi"
                        required
                        minlength="6"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3
                               focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Nhập mật khẩu mới"
                    >

                    <p class="text-sm text-gray-500 mt-1">
                        Mật khẩu phải có ít nhất 6 ký tự.
                    </p>
                </div>

                <!-- Xác nhận mật khẩu -->
                <div>
                    <label class="block font-medium text-gray-700 mb-2">
                        Xác nhận mật khẩu mới
                    </label>

                    <input
                        type="password"
                        name="xacnhan"
                        required
                        minlength="6"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3
                               focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Nhập lại mật khẩu mới"
                    >
                </div>

                <!-- Nút chức năng -->
                <div class="flex flex-wrap gap-3 pt-2">

                    <button
                        type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white
                               font-medium px-6 py-3 rounded-lg transition"
                    >
                        Đổi mật khẩu
                    </button>

                    <a
                        href="index.php"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700
                               font-medium px-6 py-3 rounded-lg transition"
                    >
                        Quay lại
                    </a>

                </div>

            </form>

        </div>

    </main>

    <footer class="text-center text-gray-500 py-6">
        Hệ thống quản lý sinh viên
    </footer>

</body>
</html>
