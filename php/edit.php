<?php
// php/edit_student.php

// include koneksi
$koneksi_file = __DIR__ . '/../koneksi/koneksi.php';
if (!file_exists($koneksi_file)) {
    die("File koneksi tidak ditemukan: $koneksi_file");
}
include_once $koneksi_file;

// normalisasi koneksi
if (isset($koneksi) && $koneksi instanceof mysqli) {
    $db = $koneksi;
} elseif (isset($conn) && $conn instanceof mysqli) {
    $db = $conn;
} else {
    $db = mysqli_connect('localhost','root','','smp');
    if (!$db) die("Koneksi gagal: " . mysqli_connect_error());
}

// mapping tabel
$allowed_tables = [
    'VII'  => 'students_vii',
    'VIII' => 'students_viii',
    'IX'   => 'students_ix',
    'student' => 'student' // fallback
];

// jika tombol update ditekan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id    = intval($_POST['id']);
    $name  = trim($_POST['name']);
    $nim   = trim($_POST['nim']);

    // class ikut dikirim dari form (hidden)
    $class = $_POST['class'] ?? '';
    $table = $allowed_tables[$class] ?? 'student';

    if ($id > 0 && $name !== '' && $nim !== '') {
        $sql = "UPDATE `$table` SET `name`=?, `nim`=? WHERE `id`=?";
        $stmt = mysqli_prepare($db, $sql);
        mysqli_stmt_bind_param($stmt, "ssi", $name, $nim, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // redirect balik ke student_profile sesuai kelas
        header("Location: student_profile.php?class=" . urlencode($class) . "&updated=1");
        exit;
    } else {
        die("Data tidak lengkap.");
    }
}

// kalau masuk lewat GET → ambil data lama
$id    = intval($_GET['id'] ?? 0);
$class = $_GET['class'] ?? '';
$table = $allowed_tables[$class] ?? 'student';

$result = mysqli_query($db, "SELECT * FROM `$table` WHERE id=$id LIMIT 1");
$row = mysqli_fetch_assoc($result);
if (!$row) {
    die("Data siswa tidak ditemukan.");
}
?>

<!-- Tampilan Form Edit -->
<!DOCTYPE html>
<html>
<head>
    <title>Student Edit</title>
    <link rel="stylesheet" href="../css/Edit.css">
</head>
<body>
    <div class="form-container">
        <h2>Edit Student</h2>
        <form method="POST">
            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
            <input type="hidden" name="class" value="<?php echo $class; ?>">

            <label>ID</label>
            <input type="text" value="<?php echo $row['id']; ?>" readonly>

            <label>Class</label>
            <input type="text" value="<?php echo $row['class']; ?>" readonly>

            <label>Name</label>
            <input type="text" name="name" value="<?php echo $row['name']; ?>" required>

            <label>NIM</label>
            <input type="text" name="nim" value="<?php echo $row['nim']; ?>" required>

            <button type="submit" name="update">Update</button>
            <a href="student_profile.php?class=<?php echo urlencode($class); ?>" class="btn cancel">Cancel</a>
        </form>
    </div>
</body>
</html>
