
<?php
session_start();

// Kiểm tra quyền sinh viên
if (!isset($_SESSION["role"]) || $_SESSION["role"] != "sinhvien") {
    header("Location: ../auth/login.php");
    exit();
}

require_once "../config/ketnoi.php";

// Lấy danh sách thông báo
$sql = "SELECT * FROM thong_bao ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->execute();

$danhSach = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông báo sinh viên</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen">

    <!-- Header -->
    <header class="bg-blue-700 text-white shadow-md">
        <div class="max-w-5xl mx-auto px-6 py-5">
            <h1 class="text-2xl md:text-3xl font-bold">
                THÔNG BÁO SINH VIÊN
            </h1>

            <p class="text-blue-100 mt-1">
                Cập nhật thông tin và thông báo từ nhà trường
            </p>
        </div>
    </header>

    <main class="max-w-5xl mx-auto p-6">

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

            <a href="change_password.php"
               class="text-blue-600 hover:text-blue-800 font-medium">
                Đổi mật khẩu
            </a>

            <a href="../auth/logout.php"
               class="text-red-600 hover:text-red-800 font-medium">
                Đăng xuất
            </a>
        </div>

        <!-- Tiêu đề danh sách -->
        <div class="mb-6">
            <h2 class="text-xl font-bold text-gray-800">
                Danh sách thông báo
            </h2>

            <p class="text-gray-500 mt-1">
                Các thông báo được sắp xếp theo thời gian mới nhất.
            </p>
        </div>

        <!-- Nội dung thông báo -->
        <?php if (count($danhSach) > 0): ?>

            <div class="space-y-5">

                <?php foreach ($danhSach as $tb): ?>

                    <div class="bg-white rounded-xl shadow-md p-6
                                border-l-4 border-blue-600">

                        <h3 class="text-xl font-bold text-gray-800 mb-3">
                            <?php echo htmlspecialchars($tb["tieude"]); ?>
                        </h3>

                        <div class="text-gray-700 leading-relaxed whitespace-pre-line">
                            <?php echo htmlspecialchars($tb["noidung"]); ?>
                        </div>

                        <div class="border-t border-gray-200 mt-5 pt-4">
                            <p class="text-sm text-gray-500">
                                Ngày đăng:
                                <span class="font-medium">
                                    <?php echo htmlspecialchars($tb["created_at"]); ?>
                                </span>
                            </p>
                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php else: ?>

            <!-- Chưa có thông báo -->
            <div class="bg-white rounded-xl shadow-md p-10 text-center">

                <div class="text-5xl mb-4">🔔</div>

                <h3 class="text-lg font-bold text-gray-700">
                    Hiện chưa có thông báo nào
                </h3>

                <p class="text-gray-500 mt-2">
                    Các thông báo mới sẽ được hiển thị tại đây.
                </p>

            </div>

        <?php endif; ?>

    </main>

    <footer class="text-center text-gray-500 py-6">
        Hệ thống quản lý sinh viên
    </footer>

</body>
</html>
