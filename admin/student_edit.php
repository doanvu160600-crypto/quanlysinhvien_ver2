
<?php
session_start();

if (!isset($_SESSION["role"]) || $_SESSION["role"] != "admin") {
    header("Location: ../auth/login.php");
    exit();
}

require_once "../config/ketnoi.php";

$error = "";

// Lấy MSSV từ URL
$mssv = $_GET["mssv"] ?? "";

// Tìm sinh viên cần sửa
$sql = "SELECT * FROM sinh_vien WHERE mssv = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$mssv]);
$sv = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$sv) {
    die("Không tìm thấy sinh viên!");
}

// Lấy danh sách lớp
$sql = "SELECT malop, tenlop FROM lop ORDER BY tenlop";
$stmt = $conn->prepare($sql);
$stmt->execute();
$danhSachLop = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Xử lý khi nhấn nút Cập nhật
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $hoten = trim($_POST["hoten"] ?? "");
    $ngaysinh = $_POST["ngaysinh"] ?? "";
    $gioitinh = $_POST["gioitinh"] ?? "";
    $malop = $_POST["malop"] ?? "";
    $email = trim($_POST["email"] ?? "");
    $sdt = trim($_POST["sdt"] ?? "");
    $diachi = trim($_POST["diachi"] ?? "");
    $trangthai = $_POST["trangthai"] ?? "";

    if (
        $hoten == "" || $ngaysinh == "" ||
        $gioitinh == "" || $malop == "" ||
        $email == "" || $trangthai == ""
    ) {
        $error = "Vui lòng nhập đầy đủ thông tin!";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email không hợp lệ!";

    } elseif (!in_array($gioitinh, ["Nam", "Nữ", "Khác"])) {
        $error = "Giới tính không hợp lệ!";

    } elseif (!in_array($trangthai, ["Đang học", "Tốt nghiệp", "Bảo lưu", "Đã nghỉ"])) {
        $error = "Trạng thái không hợp lệ!";

    } else {

        // Lấy mã khoa theo lớp
        $sql = "SELECT makhoa FROM lop WHERE malop = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$malop]);
        $lop = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$lop) {
            $error = "Lớp không tồn tại!";

        } else {

            // Cập nhật thông tin sinh viên
            $sql = "UPDATE sinh_vien SET
                    hoten = ?,
                    ngaysinh = ?,
                    gioitinh = ?,
                    malop = ?,
                    makhoa = ?,
                    email = ?,
                    sdt = ?,
                    diachi = ?,
                    trangthai = ?
                    WHERE mssv = ?";

            $stmt = $conn->prepare($sql);

            $stmt->execute([
                $hoten,
                $ngaysinh,
                $gioitinh,
                $malop,
                $lop["makhoa"],
                $email,
                $sdt,
                $diachi,
                $trangthai,
                $mssv
            ]);

            header("Location: student_list.php");
            exit();
        }
    }

    // Giữ lại dữ liệu vừa nhập nếu có lỗi
    $sv = array_merge($sv, $_POST);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa sinh viên</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-100 min-h-screen">

    <div class="max-w-4xl mx-auto p-6">

        <!-- Tiêu đề -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800">
                SỬA THÔNG TIN SINH VIÊN
            </h1>

            <p class="text-slate-500 mt-2">
                Cập nhật thông tin sinh viên trong hệ thống.
            </p>
        </div>

        <!-- Quay lại -->
        <div class="mb-5">
            <a href="student_list.php"
               class="text-blue-600 hover:text-blue-800 font-medium">
                ← Quay lại danh sách
            </a>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-xl shadow-md p-6">

            <!-- Thông báo lỗi -->
            <?php if ($error != "") { ?>
                <div class="bg-red-100 text-red-700 border border-red-200 rounded-lg p-4 mb-5">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php } ?>

            <form method="POST" class="space-y-5">

                <!-- MSSV -->
                <div>
                    <label class="block font-medium text-slate-700 mb-2">
                        MSSV
                    </label>

                    <input type="text"
                           value="<?php echo htmlspecialchars($sv["mssv"]); ?>"
                           disabled
                           class="w-full border border-slate-300 bg-slate-100 rounded-lg px-4 py-3 text-slate-500">
                </div>

                <!-- Họ tên và ngày sinh -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    <div>
                        <label class="block font-medium text-slate-700 mb-2">
                            Họ tên *
                        </label>

                        <input type="text" name="hoten"
                               value="<?php echo htmlspecialchars($sv["hoten"]); ?>"
                               required
                               class="w-full border border-slate-300 rounded-lg px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block font-medium text-slate-700 mb-2">
                            Ngày sinh *
                        </label>

                        <input type="date" name="ngaysinh"
                               value="<?php echo htmlspecialchars($sv["ngaysinh"]); ?>"
                               required
                               class="w-full border border-slate-300 rounded-lg px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                </div>

                <!-- Giới tính và lớp -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    <div>
                        <label class="block font-medium text-slate-700 mb-2">
                            Giới tính *
                        </label>

                        <select name="gioitinh" required
                                class="w-full border border-slate-300 rounded-lg px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500">

                            <?php foreach (["Nam", "Nữ", "Khác"] as $gt) { ?>

                                <option value="<?php echo $gt; ?>"
                                    <?php echo ($sv["gioitinh"] == $gt) ? "selected" : ""; ?>>

                                    <?php echo $gt; ?>

                                </option>

                            <?php } ?>

                        </select>
                    </div>

                    <div>
                        <label class="block font-medium text-slate-700 mb-2">
                            Lớp *
                        </label>

                        <select name="malop" required
                                class="w-full border border-slate-300 rounded-lg px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500">

                            <?php foreach ($danhSachLop as $lop) { ?>

                                <option value="<?php echo htmlspecialchars($lop["malop"]); ?>"
                                    <?php echo ($sv["malop"] == $lop["malop"]) ? "selected" : ""; ?>>

                                    <?php echo htmlspecialchars($lop["tenlop"]); ?>

                                </option>

                            <?php } ?>

                        </select>
                    </div>

                </div>

                <!-- Email và số điện thoại -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    <div>
                        <label class="block font-medium text-slate-700 mb-2">
                            Email *
                        </label>

                        <input type="email" name="email"
                               value="<?php echo htmlspecialchars($sv["email"]); ?>"
                               required
                               class="w-full border border-slate-300 rounded-lg px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block font-medium text-slate-700 mb-2">
                            Số điện thoại
                        </label>

                        <input type="text" name="sdt"
                               value="<?php echo htmlspecialchars($sv["sdt"] ?? ""); ?>"
                               class="w-full border border-slate-300 rounded-lg px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                </div>

                <!-- Địa chỉ -->
                <div>
                    <label class="block font-medium text-slate-700 mb-2">
                        Địa chỉ
                    </label>

                    <input type="text" name="diachi"
                           value="<?php echo htmlspecialchars($sv["diachi"] ?? ""); ?>"
                           class="w-full border border-slate-300 rounded-lg px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Trạng thái -->
                <div>
                    <label class="block font-medium text-slate-700 mb-2">
                        Trạng thái *
                    </label>

                    <select name="trangthai" required
                            class="w-full border border-slate-300 rounded-lg px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500">

                        <?php foreach (["Đang học", "Tốt nghiệp", "Bảo lưu", "Đã nghỉ"] as $tt) { ?>

                            <option value="<?php echo $tt; ?>"
                                <?php echo ($sv["trangthai"] == $tt) ? "selected" : ""; ?>>

                                <?php echo $tt; ?>

                            </option>

                        <?php } ?>

                    </select>
                </div>

                <!-- Nút -->
                <div class="flex flex-col sm:flex-row gap-3 pt-3">

                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg transition">
                        Cập nhật
                    </button>

                    <a href="student_list.php"
                       class="bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold px-6 py-3 rounded-lg text-center transition">
                        Hủy
                    </a>

                </div>

            </form>

        </div>

    </div>

</body>
</html>
