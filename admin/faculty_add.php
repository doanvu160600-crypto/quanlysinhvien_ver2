
<?php
session_start();

// Kiểm tra quyền Admin
if (!isset($_SESSION["role"]) || $_SESSION["role"] != "admin") {
    header("Location: ../auth/login.php");
    exit();
}

require_once "../config/ketnoi.php";

$thongBao = "";

// Lưu dữ liệu đã nhập
$makhoa = "";
$tenkhoa = "";
$mota = "";

// Xử lý khi gửi form
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $makhoa = trim($_POST["makhoa"]);
    $tenkhoa = trim($_POST["tenkhoa"]);
    $mota = trim($_POST["mota"]);

    if ($makhoa == "" || $tenkhoa == "") {
        $thongBao = "Vui lòng nhập mã khoa và tên khoa!";
    } else {

        // Kiểm tra mã khoa đã tồn tại chưa
        $sql = "SELECT COUNT(*) FROM khoa WHERE makhoa = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$makhoa]);

        if ($stmt->fetchColumn() > 0) {
            $thongBao = "Mã khoa đã tồn tại!";
        } else {

            // Thêm khoa vào database
            $sql = "INSERT INTO khoa (makhoa, tenkhoa, mota)
                    VALUES (?, ?, ?)";

            $stmt = $conn->prepare($sql);
            $stmt->execute([$makhoa, $tenkhoa, $mota]);

            header("Location: faculty_list.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm khoa</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen">

    <div class="max-w-3xl mx-auto p-6">

        <!-- Tiêu đề -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800">
                Thêm khoa mới
            </h1>

            <p class="text-gray-500 mt-1">
                Nhập thông tin để thêm khoa vào hệ thống
            </p>
        </div>

        <!-- Form thêm khoa -->
        <div class="bg-white rounded-xl shadow-md p-6">

            <form method="POST">

                <!-- Mã khoa -->
                <div class="mb-5">
                    <label class="block text-gray-700 font-medium mb-2">
                        Mã khoa
                    </label>

                    <input
                        type="text"
                        name="makhoa"
                        value="<?php echo htmlspecialchars($makhoa); ?>"
                        required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Nhập mã khoa">
                </div>

                <!-- Tên khoa -->
                <div class="mb-5">
                    <label class="block text-gray-700 font-medium mb-2">
                        Tên khoa
                    </label>

                    <input
                        type="text"
                        name="tenkhoa"
                        value="<?php echo htmlspecialchars($tenkhoa); ?>"
                        required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Nhập tên khoa">
                </div>

                <!-- Mô tả -->
                <div class="mb-5">
                    <label class="block text-gray-700 font-medium mb-2">
                        Mô tả
                    </label>

                    <textarea
                        name="mota"
                        rows="4"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Nhập mô tả về khoa"><?php echo htmlspecialchars($mota); ?></textarea>
                </div>

                <!-- Thông báo lỗi -->
                <?php if ($thongBao != "") { ?>

                    <div class="bg-red-100 text-red-700 border border-red-300 p-3 rounded-lg mb-5">
                        <?php echo htmlspecialchars($thongBao); ?>
                    </div>

                <?php } ?>

                <!-- Nút thao tác -->
                <div class="flex flex-col sm:flex-row gap-3">

                    <button
                        type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-medium">

                        Thêm khoa

                    </button>

                    <a
                        href="faculty_list.php"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-5 py-2.5 rounded-lg font-medium text-center">

                        Quay lại danh sách

                    </a>

                </div>

            </form>

        </div>

    </div>

</body>
</html>
