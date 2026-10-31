
<?php
session_start();

// Kiểm tra quyền Admin
if (!isset($_SESSION["role"]) || $_SESSION["role"] != "admin") {
    header("Location: ../auth/login.php");
    exit();
}

require_once "../config/ketnoi.php";

// Lấy danh sách thông báo
$sql = "SELECT * FROM thong_bao ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->execute();

$danhSachThongBao = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý thông báo</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen">

    <div class="max-w-7xl mx-auto p-6">

        <!-- Tiêu đề -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">

            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    Quản lý thông báo
                </h1>

                <p class="text-gray-500 mt-1">
                    Danh sách thông báo trong hệ thống
                </p>
            </div>

            <a href="notification_add.php"
               class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-medium">
                + Thêm thông báo mới
            </a>

        </div>

        <!-- Thông báo kết quả -->
        <?php if (isset($_GET["msg"])) { ?>

            <?php if ($_GET["msg"] == "deleted") { ?>

                <div class="bg-green-100 text-green-700 border border-green-300 p-3 rounded-lg mb-4">
                    Xóa thông báo thành công!
                </div>

            <?php } elseif ($_GET["msg"] == "error") { ?>

                <div class="bg-red-100 text-red-700 border border-red-300 p-3 rounded-lg mb-4">
                    Không thể xóa thông báo!
                </div>

            <?php } ?>

        <?php } ?>

        <!-- Bảng danh sách -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">

            <div class="p-5 border-b flex justify-between items-center">

                <h2 class="text-xl font-semibold text-gray-700">
                    Danh sách thông báo
                </h2>

                <a href="index.php"
                   class="text-blue-600 hover:text-blue-800 font-medium">
                    ← Trang chủ Admin
                </a>

            </div>

            <div class="overflow-x-auto">

                <table class="w-full text-left border-collapse">

                    <thead class="bg-gray-100 text-gray-700">

                        <tr>
                            <th class="px-5 py-4 border-b">ID</th>
                            <th class="px-5 py-4 border-b">Tiêu đề</th>
                            <th class="px-5 py-4 border-b">Nội dung</th>
                            <th class="px-5 py-4 border-b">Ngày tạo</th>
                            <th class="px-5 py-4 border-b text-center">Thao tác</th>
                        </tr>

                    </thead>

                    <tbody class="divide-y divide-gray-200">

                        <?php if (count($danhSachThongBao) > 0) { ?>

                            <?php foreach ($danhSachThongBao as $tb) { ?>

                                <tr class="hover:bg-gray-50">

                                    <td class="px-5 py-4 font-medium text-gray-800">
                                        <?php echo htmlspecialchars($tb["id"]); ?>
                                    </td>

                                    <td class="px-5 py-4 font-medium text-gray-800">
                                        <?php echo htmlspecialchars($tb["tieude"]); ?>
                                    </td>

                                    <td class="px-5 py-4 text-gray-700 min-w-64">
                                        <?php echo nl2br(htmlspecialchars($tb["noidung"])); ?>
                                    </td>

                                    <td class="px-5 py-4 text-gray-600 whitespace-nowrap">
                                        <?php echo htmlspecialchars($tb["created_at"]); ?>
                                    </td>

                                    <td class="px-5 py-4 text-center whitespace-nowrap">

                                        <a href="notification_edit.php?id=<?php echo urlencode($tb["id"]); ?>"
                                           class="inline-block bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1.5 rounded-md text-sm">
                                            Sửa
                                        </a>

                                        <form action="notification_delete.php"
                                              method="POST"
                                              class="inline-block"
                                              onsubmit="return confirm('Bạn có chắc muốn xóa thông báo này không?');">

                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?php echo htmlspecialchars($tb["id"]); ?>">

                                            <button
                                                type="submit"
                                                class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-md text-sm">
                                                Xóa
                                            </button>

                                        </form>

                                    </td>

                                </tr>

                            <?php } ?>

                        <?php } else { ?>

                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                    Chưa có thông báo nào.
                                </td>
                            </tr>

                        <?php } ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</body>
</html>
