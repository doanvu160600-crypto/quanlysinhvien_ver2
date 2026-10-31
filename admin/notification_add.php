
<?php
session_start();

// Kiểm tra quyền Admin
if (!isset($_SESSION["role"]) || $_SESSION["role"] != "admin") {
    header("Location: ../auth/login.php");
    exit();
}

require_once "../config/ketnoi.php";

$thongBao = "";
$tieude = "";
$noidung = "";

// Xử lý khi gửi form
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $tieude = trim($_POST["tieude"] ?? "");
    $noidung = trim($_POST["noidung"] ?? "");

    if ($tieude == "" || $noidung == "") {

        $thongBao = "Vui lòng nhập đầy đủ tiêu đề và nội dung!";

    } else {

        try {
            $sql = "INSERT INTO thong_bao (tieude, noidung, created_at)
                    VALUES (?, ?, NOW())";

            $stmt = $conn->prepare($sql);
            $stmt->execute([$tieude, $noidung]);

            header("Location: notification.php");
            exit();

        } catch (PDOException $e) {
            $thongBao = "Không thể thêm thông báo. Vui lòng thử lại!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm thông báo</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen">

    <div class="max-w-3xl mx-auto p-6">

        <!-- Tiêu đề -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800">
                Thêm thông báo
            </h1>

            <p class="text-gray-500 mt-2">
                Tạo thông báo mới cho sinh viên.
            </p>
        </div>

        <!-- Form thêm thông báo -->
        <div class="bg-white rounded-xl shadow-md p-6">

            <a href="notification.php"
               class="inline-block text-blue-600 hover:text-blue-800 mb-6">
                ← Quay lại danh sách thông báo
            </a>

            <?php if ($thongBao != ""): ?>
                <div class="bg-red-100 text-red-700 p-3 rounded-lg mb-5">
                    <?php echo htmlspecialchars($thongBao); ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-5">

                <!-- Tiêu đề -->
                <div>
                    <label class="block font-medium text-gray-700 mb-2">
                        Tiêu đề
                    </label>

                    <input
                        type="text"
                        name="tieude"
                        value="<?php echo htmlspecialchars($tieude); ?>"
                        required
                        class="w-full border border-gray-300 rounded-lg px-4 py-3
                               focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Nhập tiêu đề thông báo"
                    >
                </div>

                <!-- Nội dung -->
                <div>
                    <label class="block font-medium text-gray-700 mb-2">
                        Nội dung
                    </label>

                    <textarea
                        name="noidung"
                        rows="7"
                        required
                        class="w-full border border-gray-300 rounded-lg px-4 py-3
                               focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Nhập nội dung thông báo..."
                    ><?php echo htmlspecialchars($noidung); ?></textarea>
                </div>

                <!-- Nút chức năng -->
                <div class="flex flex-wrap gap-3 pt-2">

                    <button
                        type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white
                               font-medium px-6 py-3 rounded-lg transition"
                    >
                        Đăng thông báo
                    </button>

                    <a
                        href="notification.php"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700
                               font-medium px-6 py-3 rounded-lg transition"
                    >
                        Hủy
                    </a>

                </div>

            </form>

        </div>
    </div>

</body>
</html>
