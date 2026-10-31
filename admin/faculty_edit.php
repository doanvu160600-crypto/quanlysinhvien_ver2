
<?php
session_start();

// Kiểm tra quyền Admin
if (!isset($_SESSION["role"]) || $_SESSION["role"] != "admin") {
    header("Location: ../auth/login.php");
    exit();
}

require_once "../config/ketnoi.php";

// Lấy mã khoa từ URL
$makhoa = $_GET["makhoa"] ?? "";

// Tìm khoa cần sửa
$sql = "SELECT * FROM khoa WHERE makhoa = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$makhoa]);

$khoa = $stmt->fetch(PDO::FETCH_ASSOC);

// Nếu không tìm thấy khoa
if (!$khoa) {
    die("Không tìm thấy khoa!");
}

$thongBao = "";

// Xử lý khi nhấn nút cập nhật
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $tenkhoa = trim($_POST["tenkhoa"]);
    $mota = trim($_POST["mota"]);

    // Giữ lại dữ liệu đã nhập
    $khoa["tenkhoa"] = $tenkhoa;
    $khoa["mota"] = $mota;

    if ($tenkhoa == "") {
        $thongBao = "Vui lòng nhập tên khoa!";
    } else {

        $sql = "UPDATE khoa
                SET tenkhoa = ?, mota = ?
                WHERE makhoa = ?";

        $stmt = $conn->prepare($sql);
        $stmt->execute([$tenkhoa, $mota, $makhoa]);

        header("Location: faculty_list.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa khoa</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen">

    <div class="max-w-3xl mx-auto p-6">

        <!-- Tiêu đề -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800">
                Sửa thông tin khoa
            </h1>

            <p class="text-gray-500 mt-1">
                Cập nhật thông tin khoa trong hệ thống
            </p>
        </div>

        <!-- Form sửa khoa -->
        <div class="bg-white rounded-xl shadow-md p-6">

            <form method="POST">

                <!-- Mã khoa -->
                <div class="mb-5">
                    <label class="block text-gray-700 font-medium mb-2">
                        Mã khoa
                    </label>

                    <input
                        type="text"
                        value="<?php echo htmlspecialchars($khoa["makhoa"]); ?>"
                        disabled
                        class="w-full border border-gray-300 bg-gray-100 text-gray-500 rounded-lg px-4 py-2.5">
                </div>

                <!-- Tên khoa -->
                <div class="mb-5">
                    <label class="block text-gray-700 font-medium mb-2">
                        Tên khoa
                    </label>

                    <input
                        type="text"
                        name="tenkhoa"
                        value="<?php echo htmlspecialchars($khoa["tenkhoa"]); ?>"
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
                        placeholder="Nhập mô tả về khoa"><?php echo htmlspecialchars($khoa["mota"] ?? ""); ?></textarea>
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

                        Cập nhật

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
