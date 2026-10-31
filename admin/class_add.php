
<?php
session_start();

// Kiểm tra quyền Admin
if (!isset($_SESSION["role"]) || $_SESSION["role"] != "admin") {
    header("Location: ../auth/login.php");
    exit();
}

require_once "../config/ketnoi.php";

// Lấy danh sách khoa
$sql = "SELECT * FROM khoa ORDER BY makhoa";
$stmt = $conn->prepare($sql);
$stmt->execute();

$danhSachKhoa = $stmt->fetchAll(PDO::FETCH_ASSOC);

$thongBao = "";

// Lưu dữ liệu đã nhập khi có lỗi
$malop = "";
$tenlop = "";
$makhoa = "";

// Xử lý khi gửi form
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $malop = trim($_POST["malop"]);
    $tenlop = trim($_POST["tenlop"]);
    $makhoa = trim($_POST["makhoa"]);

    if ($malop == "" || $tenlop == "" || $makhoa == "") {
        $thongBao = "Vui lòng nhập đầy đủ thông tin!";
    } else {

        // Kiểm tra mã lớp đã tồn tại chưa
        $sql = "SELECT COUNT(*) FROM lop WHERE malop = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$malop]);

        if ($stmt->fetchColumn() > 0) {
            $thongBao = "Mã lớp đã tồn tại!";
        } else {

            // Thêm lớp vào database
            $sql = "INSERT INTO lop (malop, tenlop, makhoa)
                    VALUES (?, ?, ?)";

            $stmt = $conn->prepare($sql);
            $stmt->execute([$malop, $tenlop, $makhoa]);

            header("Location: class_list.php");
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
    <title>Thêm lớp học</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen">

    <div class="max-w-3xl mx-auto p-6">

        <!-- Tiêu đề -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800">
                Thêm lớp học
            </h1>

            <p class="text-gray-500 mt-1">
                Nhập thông tin để thêm lớp học mới vào hệ thống
            </p>
        </div>

        <!-- Form thêm lớp -->
        <div class="bg-white rounded-xl shadow-md p-6">

            <form method="POST">

                <!-- Mã lớp -->
                <div class="mb-5">
                    <label class="block text-gray-700 font-medium mb-2">
                        Mã lớp
                    </label>

                    <input
                        type="text"
                        name="malop"
                        value="<?php echo htmlspecialchars($malop); ?>"
                        required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Nhập mã lớp">
                </div>

                <!-- Tên lớp -->
                <div class="mb-5">
                    <label class="block text-gray-700 font-medium mb-2">
                        Tên lớp
                    </label>

                    <input
                        type="text"
                        name="tenlop"
                        value="<?php echo htmlspecialchars($tenlop); ?>"
                        required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Nhập tên lớp">
                </div>

                <!-- Khoa -->
                <div class="mb-5">
                    <label class="block text-gray-700 font-medium mb-2">
                        Khoa
                    </label>

                    <select
                        name="makhoa"
                        required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">

                        <option value="">-- Chọn khoa --</option>

                        <?php foreach ($danhSachKhoa as $khoa) { ?>

                            <option
                                value="<?php echo htmlspecialchars($khoa["makhoa"]); ?>"
                                <?php echo ($makhoa == $khoa["makhoa"]) ? "selected" : ""; ?>>

                                <?php echo htmlspecialchars($khoa["tenkhoa"]); ?>

                            </option>

                        <?php } ?>

                    </select>
                </div>

                <!-- Thông báo lỗi -->
                <?php if ($thongBao != "") { ?>

                    <div class="bg-red-100 text-red-700 border border-red-300 p-3 rounded-lg mb-5">
                        <?php echo htmlspecialchars($thongBao); ?>
                    </div>

                <?php } ?>

                <!-- Nút thao tác -->
                <?php if (count($danhSachKhoa) > 0) { ?>

                    <div class="flex flex-col sm:flex-row gap-3">

                        <button
                            type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-medium">

                            Thêm lớp

                        </button>

                        <a
                            href="class_list.php"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-5 py-2.5 rounded-lg font-medium text-center">

                            Quay lại danh sách

                        </a>

                    </div>

                <?php } else { ?>

                    <div class="bg-yellow-100 text-yellow-700 border border-yellow-300 p-3 rounded-lg mb-4">
                        Chưa có khoa. Vui lòng thêm khoa trước!
                    </div>

                    <a
                        href="class_list.php"
                        class="inline-block bg-gray-200 hover:bg-gray-300 text-gray-700 px-5 py-2.5 rounded-lg font-medium">

                        Quay lại danh sách

                    </a>

                <?php } ?>

            </form>

        </div>

    </div>

</body>
</html>
