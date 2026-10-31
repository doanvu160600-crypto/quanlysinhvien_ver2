
<?php
session_start();

// Kiểm tra quyền sinh viên
if (!isset($_SESSION["role"]) || $_SESSION["role"] != "sinhvien") {
    header("Location: ../auth/login.php");
    exit();
}

require_once "../config/ketnoi.php";

// Lấy MSSV từ session
$mssv = $_SESSION["user_id"];

// Truy vấn thông tin sinh viên
$sql = "SELECT sv.*, l.tenlop, k.tenkhoa
        FROM sinh_vien sv
        LEFT JOIN lop l ON sv.malop = l.malop
        LEFT JOIN khoa k ON sv.makhoa = k.makhoa
        WHERE sv.mssv = ?";

$stmt = $conn->prepare($sql);
$stmt->execute([$mssv]);

$sv = $stmt->fetch(PDO::FETCH_ASSOC);

// Kiểm tra sinh viên có tồn tại không
if (!$sv) {
    die("Không tìm thấy thông tin sinh viên!");
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin cá nhân</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen">

    <!-- Header -->
    <header class="bg-blue-700 text-white shadow-md">
        <div class="max-w-5xl mx-auto px-6 py-5">
            <h1 class="text-2xl md:text-3xl font-bold">
                THÔNG TIN CÁ NHÂN
            </h1>

            <p class="text-blue-100 mt-1">
                Thông tin tài khoản sinh viên
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

            <a href="change_password.php"
               class="text-blue-600 hover:text-blue-800 font-medium">
                Đổi mật khẩu
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

        <!-- Bảng thông tin -->
        <div class="bg-white rounded-xl shadow-md p-6">

            <h2 class="text-xl font-bold text-gray-800 mb-5">
                Hồ sơ sinh viên
            </h2>

            <div class="overflow-x-auto">
                <table class="w-full border-collapse">

                    <tbody class="divide-y divide-gray-200">

                        <tr>
                            <th class="text-left bg-gray-50 p-4 w-1/3 text-gray-700">
                                Mã số sinh viên
                            </th>
                            <td class="p-4 text-gray-800">
                                <?php echo htmlspecialchars($sv["mssv"]); ?>
                            </td>
                        </tr>

                        <tr>
                            <th class="text-left bg-gray-50 p-4 text-gray-700">
                                Họ tên
                            </th>
                            <td class="p-4 text-gray-800 font-medium">
                                <?php echo htmlspecialchars($sv["hoten"]); ?>
                            </td>
                        </tr>

                        <tr>
                            <th class="text-left bg-gray-50 p-4 text-gray-700">
                                Ngày sinh
                            </th>
                            <td class="p-4 text-gray-800">
                                <?php echo htmlspecialchars($sv["ngaysinh"]); ?>
                            </td>
                        </tr>

                        <tr>
                            <th class="text-left bg-gray-50 p-4 text-gray-700">
                                Giới tính
                            </th>
                            <td class="p-4 text-gray-800">
                                <?php echo htmlspecialchars($sv["gioitinh"]); ?>
                            </td>
                        </tr>

                        <tr>
                            <th class="text-left bg-gray-50 p-4 text-gray-700">
                                Lớp
                            </th>
                            <td class="p-4 text-gray-800">
                                <?php echo htmlspecialchars($sv["tenlop"] ?? "Chưa có"); ?>
                            </td>
                        </tr>

                        <tr>
                            <th class="text-left bg-gray-50 p-4 text-gray-700">
                                Khoa
                            </th>
                            <td class="p-4 text-gray-800">
                                <?php echo htmlspecialchars($sv["tenkhoa"] ?? "Chưa có"); ?>
                            </td>
                        </tr>

                        <tr>
                            <th class="text-left bg-gray-50 p-4 text-gray-700">
                                Email
                            </th>
                            <td class="p-4 text-gray-800">
                                <?php echo htmlspecialchars($sv["email"]); ?>
                            </td>
                        </tr>

                        <tr>
                            <th class="text-left bg-gray-50 p-4 text-gray-700">
                                Số điện thoại
                            </th>
                            <td class="p-4 text-gray-800">
                                <?php echo htmlspecialchars($sv["sdt"]); ?>
                            </td>
                        </tr>

                        <tr>
                            <th class="text-left bg-gray-50 p-4 text-gray-700">
                                Địa chỉ
                            </th>
                            <td class="p-4 text-gray-800">
                                <?php echo htmlspecialchars($sv["diachi"]); ?>
                            </td>
                        </tr>

                        <tr>
                            <th class="text-left bg-gray-50 p-4 text-gray-700">
                                Trạng thái
                            </th>
                            <td class="p-4">
                                <span class="inline-block bg-green-100 text-green-700
                                             px-3 py-1 rounded-full text-sm">
                                    <?php echo htmlspecialchars($sv["trangthai"]); ?>
                                </span>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>

        </div>

    </main>

    <footer class="text-center text-gray-500 py-6">
        Hệ thống quản lý sinh viên
    </footer>

</body>
</html>
