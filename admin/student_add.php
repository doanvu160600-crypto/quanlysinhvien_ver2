
<?php
session_start();

if (!isset($_SESSION["role"]) || $_SESSION["role"] != "admin") {
    header("Location: ../auth/login.php");
    exit();
}

require_once "../config/ketnoi.php";

$error = "";
$success = "";

// Lấy danh sách lớp
$sql = "SELECT malop, tenlop FROM lop ORDER BY tenlop";
$stmt = $conn->prepare($sql);
$stmt->execute();
$danhSachLop = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Xử lý khi nhấn nút Thêm
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $mssv = trim($_POST["mssv"] ?? "");
    $hoten = trim($_POST["hoten"] ?? "");
    $ngaysinh = $_POST["ngaysinh"] ?? "";
    $gioitinh = $_POST["gioitinh"] ?? "";
    $malop = $_POST["malop"] ?? "";
    $email = trim($_POST["email"] ?? "");
    $sdt = trim($_POST["sdt"] ?? "");
    $diachi = trim($_POST["diachi"] ?? "");
    $password = $_POST["password"] ?? "";
    $trangthai = $_POST["trangthai"] ?? "";

    if (
        $mssv == "" || $hoten == "" || $ngaysinh == "" ||
        $gioitinh == "" || $malop == "" || $email == "" ||
        $password == "" || $trangthai == ""
    ) {
        $error = "Vui lòng nhập đầy đủ thông tin!";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email không hợp lệ!";

    } elseif (!in_array($gioitinh, ["Nam", "Nữ", "Khác"])) {
        $error = "Giới tính không hợp lệ!";

    } elseif (!in_array($trangthai, ["Đang học", "Tốt nghiệp", "Bảo lưu", "Đã nghỉ"])) {
        $error = "Trạng thái không hợp lệ!";

    } else {

        // Kiểm tra MSSV đã tồn tại
        $sql = "SELECT mssv FROM sinh_vien WHERE mssv = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$mssv]);

        if ($stmt->fetch()) {
            $error = "MSSV đã tồn tại!";

        } else {

            // Lấy mã khoa theo lớp
            $sql = "SELECT makhoa FROM lop WHERE malop = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$malop]);
            $lop = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$lop) {
                $error = "Lớp không tồn tại!";

            } else {

                // Mã hóa mật khẩu
                $matKhauMaHoa = password_hash($password, PASSWORD_DEFAULT);

                // Thêm sinh viên
                $sql = "INSERT INTO sinh_vien
                        (mssv, hoten, ngaysinh, gioitinh, malop,
                         makhoa, email, sdt, diachi, password, trangthai)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                $stmt = $conn->prepare($sql);

                $stmt->execute([
                    $mssv,
                    $hoten,
                    $ngaysinh,
                    $gioitinh,
                    $malop,
                    $lop["makhoa"],
                    $email,
                    $sdt,
                    $diachi,
                    $matKhauMaHoa,
                    $trangthai
                ]);

                $success = "Thêm sinh viên thành công!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm sinh viên</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-100 min-h-screen">

    <div class="max-w-4xl mx-auto p-6">

        <!-- Tiêu đề -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800">
                THÊM SINH VIÊN
            </h1>

            <p class="text-slate-500 mt-2">
                Nhập thông tin để thêm sinh viên mới vào hệ thống.
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

            <!-- Thông báo thành công -->
            <?php if ($success != "") { ?>
                <div class="bg-green-100 text-green-700 border border-green-200 rounded-lg p-4 mb-5">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php } ?>

            <?php if (count($danhSachLop) == 0) { ?>

                <div class="bg-yellow-100 text-yellow-700 p-4 rounded-lg">
                    Chưa có lớp học. Vui lòng thêm lớp trước.
                </div>

            <?php } else { ?>

                <form method="POST" class="space-y-5">

                    <!-- MSSV và họ tên -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        <div>
                            <label class="block font-medium text-slate-700 mb-2">
                                MSSV *
                            </label>

                            <input type="text" name="mssv" required
                                   value="<?php echo htmlspecialchars($_POST["mssv"] ?? ""); ?>"
                                   placeholder="Nhập mã số sinh viên"
                                   class="w-full border border-slate-300 rounded-lg px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block font-medium text-slate-700 mb-2">
                                Họ tên *
                            </label>

                            <input type="text" name="hoten" required
                                   value="<?php echo htmlspecialchars($_POST["hoten"] ?? ""); ?>"
                                   placeholder="Nhập họ và tên"
                                   class="w-full border border-slate-300 rounded-lg px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                    </div>

                    <!-- Ngày sinh và giới tính -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        <div>
                            <label class="block font-medium text-slate-700 mb-2">
                                Ngày sinh *
                            </label>

                            <input type="date" name="ngaysinh" required
                                   value="<?php echo htmlspecialchars($_POST["ngaysinh"] ?? ""); ?>"
                                   class="w-full border border-slate-300 rounded-lg px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block font-medium text-slate-700 mb-2">
                                Giới tính *
                            </label>

                            <select name="gioitinh" required
                                    class="w-full border border-slate-300 rounded-lg px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500">

                                <option value="Nam">Nam</option>
                                <option value="Nữ">Nữ</option>
                                <option value="Khác">Khác</option>

                            </select>
                        </div>

                    </div>

                    <!-- Lớp -->
                    <div>
                        <label class="block font-medium text-slate-700 mb-2">
                            Lớp *
                        </label>

                        <select name="malop" required
                                class="w-full border border-slate-300 rounded-lg px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500">

                            <?php foreach ($danhSachLop as $lop) { ?>

                                <option value="<?php echo htmlspecialchars($lop["malop"]); ?>"
                                    <?php echo (($_POST["malop"] ?? "") == $lop["malop"]) ? "selected" : ""; ?>>

                                    <?php echo htmlspecialchars($lop["tenlop"]); ?>

                                </option>

                            <?php } ?>

                        </select>
                    </div>

                    <!-- Email và số điện thoại -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        <div>
                            <label class="block font-medium text-slate-700 mb-2">
                                Email *
                            </label>

                            <input type="email" name="email" required
                                   value="<?php echo htmlspecialchars($_POST["email"] ?? ""); ?>"
                                   placeholder="example@gmail.com"
                                   class="w-full border border-slate-300 rounded-lg px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block font-medium text-slate-700 mb-2">
                                Số điện thoại
                            </label>

                            <input type="text" name="sdt"
                                   value="<?php echo htmlspecialchars($_POST["sdt"] ?? ""); ?>"
                                   placeholder="Nhập số điện thoại"
                                   class="w-full border border-slate-300 rounded-lg px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                    </div>

                    <!-- Địa chỉ -->
                    <div>
                        <label class="block font-medium text-slate-700 mb-2">
                            Địa chỉ
                        </label>

                        <input type="text" name="diachi"
                               value="<?php echo htmlspecialchars($_POST["diachi"] ?? ""); ?>"
                               placeholder="Nhập địa chỉ"
                               class="w-full border border-slate-300 rounded-lg px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Mật khẩu -->
                    <div>
                        <label class="block font-medium text-slate-700 mb-2">
                            Mật khẩu *
                        </label>

                        <input type="password" name="password" required
                               placeholder="Nhập mật khẩu"
                               class="w-full border border-slate-300 rounded-lg px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Trạng thái -->
                    <div>
                        <label class="block font-medium text-slate-700 mb-2">
                            Trạng thái *
                        </label>

                        <select name="trangthai" required
                                class="w-full border border-slate-300 rounded-lg px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500">

                            <option value="Đang học">Đang học</option>
                            <option value="Tốt nghiệp">Tốt nghiệp</option>
                            <option value="Bảo lưu">Bảo lưu</option>
                            <option value="Đã nghỉ">Đã nghỉ</option>

                        </select>
                    </div>

                    <!-- Nút -->
                    <div class="flex flex-col sm:flex-row gap-3 pt-3">

                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg transition">
                            Thêm sinh viên
                        </button>

                        <a href="student_list.php"
                           class="bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold px-6 py-3 rounded-lg text-center transition">
                            Hủy
                        </a>

                    </div>

                </form>

            <?php } ?>

        </div>

    </div>

</body>
</html>
