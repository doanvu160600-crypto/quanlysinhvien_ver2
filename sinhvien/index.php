
<?php
session_start();

// Kiểm tra quyền sinh viên
if (!isset($_SESSION["role"]) || $_SESSION["role"] != "sinhvien") {
    header("Location: ../auth/login.php");
    exit();
}

$hoten = $_SESSION["hoten"] ?? "Sinh viên";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ sinh viên</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen">

    <!-- Header -->
    <header class="bg-blue-700 text-white shadow-md">
        <div class="max-w-6xl mx-auto px-6 py-5">
            <h1 class="text-2xl md:text-3xl font-bold">
                HỆ THỐNG QUẢN LÝ SINH VIÊN
            </h1>

            <p class="text-blue-100 mt-1">
                Khu vực dành cho sinh viên
            </p>
        </div>
    </header>

    <main class="max-w-6xl mx-auto p-6">

        <!-- Lời chào -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-800">
                Xin chào,
                <span class="text-blue-600">
                    <?php echo htmlspecialchars($hoten); ?>
                </span>!
            </h2>

            <p class="text-gray-500 mt-2">
                Chào mừng bạn đến với khu vực sinh viên.
            </p>
        </div>

        <!-- Danh sách chức năng -->
        <div class="mb-5">
            <h3 class="text-xl font-bold text-gray-800">
                Chức năng sinh viên
            </h3>

            <p class="text-gray-500 mt-1">
                Chọn chức năng bạn muốn sử dụng.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

            <!-- Thông tin cá nhân -->
            <a href="profile.php"
               class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg
                      hover:-translate-y-1 transition">
                <div class="text-blue-600 text-3xl mb-3">👤</div>

                <h4 class="text-lg font-bold text-gray-800">
                    Thông tin cá nhân
                </h4>

                <p class="text-gray-500 mt-2">
                    Xem thông tin cá nhân của bạn.
                </p>
            </a>

            <!-- Đổi mật khẩu -->
            <a href="change_password.php"
               class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg
                      hover:-translate-y-1 transition">
                <div class="text-green-600 text-3xl mb-3">🔒</div>

                <h4 class="text-lg font-bold text-gray-800">
                    Đổi mật khẩu
                </h4>

                <p class="text-gray-500 mt-2">
                    Thay đổi mật khẩu tài khoản.
                </p>
            </a>

            <!-- Thông báo -->
            <a href="notifications.php"
               class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg
                      hover:-translate-y-1 transition">
                <div class="text-orange-500 text-3xl mb-3">🔔</div>

                <h4 class="text-lg font-bold text-gray-800">
                    Xem thông báo
                </h4>

                <p class="text-gray-500 mt-2">
                    Theo dõi các thông báo từ nhà trường.
                </p>
            </a>

            <!-- Đăng xuất -->
            <a href="../auth/logout.php"
               class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg
                      hover:-translate-y-1 transition">
                <div class="text-red-600 text-3xl mb-3">🚪</div>

                <h4 class="text-lg font-bold text-gray-800">
                    Đăng xuất
                </h4>

                <p class="text-gray-500 mt-2">
                    Thoát khỏi tài khoản sinh viên.
                </p>
            </a>

        </div>

    </main>

    <!-- Footer -->
    <footer class="text-center text-gray-500 py-6">
        Hệ thống quản lý sinh viên
    </footer>

</body>
</html>
