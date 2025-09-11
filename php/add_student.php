<?php
// php/add_student.php
// Tampilan form + proses tambah siswa

// include koneksi (sesuaikan path jika berbeda)
$koneksi_file = __DIR__ . '/../koneksi/koneksi.php';
if (!file_exists($koneksi_file)) {
    die("File koneksi tidak ditemukan: $koneksi_file");
}
include_once $koneksi_file;

// beberapa file koneksi menggunakan $koneksi atau $conn -> normalisasi
if (isset($koneksi) && $koneksi instanceof mysqli) {
    $db = $koneksi;
} elseif (isset($conn) && $conn instanceof mysqli) {
    $db = $conn;
} else {
    // coba buat koneksi fallback (jika koneksi tidak didefinisikan di file koneksi)
    $db = mysqli_connect('localhost','root','','smp');
    if (!$db) die("Koneksi fallback gagal: " . mysqli_connect_error());
}

// inisialisasi pesan
$error = "";
$success = "";

// list tabel yang diizinkan (jika kamu menggunakan tabel per kelas)
// sesuaikan nama-nama tabel bila berbeda
$allowed_tables = [
    'VII'  => 'students_vii',
    'VIII' => 'students_viii',
    'IX'   => 'students_ix',
    // fallback single table (jika kamu punya tabel 'student' atau 'students')
    'student' => 'student'
];

// ketika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class = trim($_POST['class'] ?? '');
    $name  = trim($_POST['name'] ?? '');
    $nim   = trim($_POST['nim'] ?? '');

    // validasi sederhana
    if ($class === '' || $name === '' || $nim === '') {
        $error = "Semua field harus diisi.";
    } else {
        // pilih tabel target berdasarkan class; jika tidak cocok pakai fallback 'student'
        $table = $allowed_tables[$class] ?? ($allowed_tables['student'] ?? 'student');

        // pastikan tabel benar-benar ada di DB (security)
        $tables_ok = [];
        $res_tables = mysqli_query($db, "SHOW TABLES");
        if ($res_tables) {
            while ($r = mysqli_fetch_row($res_tables)) {
                $tables_ok[] = $r[0];
            }
        }
        if (!in_array($table, $tables_ok)) {
            // fallback: jika table target tidak ada, coba 'student' atau ambil tabel pertama students_vii bila ada
            if (in_array('student', $tables_ok)) {
                $table = 'student';
            } elseif (in_array('students_vii', $tables_ok)) {
                $table = 'students_vii';
            } else {
                $error = "Tabel tujuan tidak ditemukan di database. Silakan cek struktur DB.";
            }
        }

        // jika tidak ada error lanjutkan insert
        if ($error === '') {
            // prepared statement untuk insert (kolom: class, name, nim)
            $sql = "INSERT INTO `{$table}` (`class`, `name`, `nim`) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($db, $sql);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "sss", $class, $name, $nim);
                $ok = mysqli_stmt_execute($stmt);
                if ($ok) {
                    mysqli_stmt_close($stmt);
                    // sukses -> redirect kembali ke student_profile (sesuaikan nama file)
                    // jika student_profile.php ada di folder php bersama file ini, gunakan header ke student_profile.php
                    $success = "Siswa berhasil ditambahkan.";
                    // redirect, sertakan class agar halaman menampilkan kelas yang dipilih
                    header("Location: student_profile.php?class=" . urlencode($class) . "&added=1");
                    exit;
                } else {
                    $error = "Gagal menyimpan data: " . mysqli_error($db);
                    mysqli_stmt_close($stmt);
                }
            } else {
                $error = "Gagal menyiapkan query: " . mysqli_error($db);
            }
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Add Student</title>
<style>
/* Simple responsive form (mirip signup) */
* { box-sizing: border-box; margin:0; padding:0; font-family: Arial, sans-serif; }
body { background:#f1f1f1; padding:16px; min-height:100vh; display:flex; align-items:center; justify-content:center; }
.card {
  width:100%; max-width:520px; background:#fff; padding:24px; border-radius:10px; box-shadow:0 6px 18px rgba(0,0,0,0.08);
}
h2 { text-align:center; color:#2e7d32; margin-bottom:16px; }
.form-group { margin-bottom:12px; }
label { display:block; margin-bottom:6px; color:#333; font-size:14px; }
input[type="text"] { width:100%; padding:10px 12px; border-radius:6px; border:1px solid #ccc; font-size:14px; }
select { width:100%; padding:10px 12px; border-radius:6px; border:1px solid #ccc; font-size:14px; background:#fff; }
.btn { width:100%; padding:12px; background:#2e7d32; color:#fff; border:0; border-radius:6px; font-size:16px; cursor:pointer; }
.btn.secondary { background:#6c757d; margin-top:8px; }
.msg { padding:10px 12px; border-radius:6px; margin-bottom:12px; font-weight:500; }
.msg.error { background:#ffecec; color:#b00020; border:1px solid #f5c6c6; }
.msg.success { background:#eaffea; color:#0b6b13; border:1px solid #c7f0c7; }

/* responsive tweaks */
@media (max-width:600px){ .card{ padding:18px; max-width:420px; } .btn{ padding:10px; } }
</style>
</head>
<body>

<div class="card">
  <h2>Student Add</h2>

  <?php if ($error !== ""): ?>
    <div class="msg error"><?php echo htmlspecialchars($error); ?></div>
  <?php endif; ?>

  <?php if ($success !== ""): ?>
    <div class="msg success"><?php echo htmlspecialchars($success); ?></div>
  <?php endif; ?>

  <form method="post" action="">
    <div class="form-group">
      <label for="class">Class</label>
      <select id="class" name="class" required>
        <option value="">-- Select Class --</option>
        <option value="VII" <?php if(isset($_GET['class']) && $_GET['class']=='VII') echo 'selected'; ?>>VII</option>
        <option value="VIII" <?php if(isset($_GET['class']) && $_GET['class']=='VIII') echo 'selected'; ?>>VIII</option>
        <option value="IX" <?php if(isset($_GET['class']) && $_GET['class']=='IX') echo 'selected'; ?>>IX</option>
      </select>
    </div>

    <div class="form-group">
      <label for="name">Name</label>
      <input id="name" name="name" type="text" required value="<?php echo isset($_POST['name'])?htmlspecialchars($_POST['name']):''; ?>">
    </div>

    <div class="form-group">
      <label for="nim">NIM</label>
      <input id="nim" name="nim" type="text" required value="<?php echo isset($_POST['nim'])?htmlspecialchars($_POST['nim']):''; ?>">
    </div>

    <button type="submit" class="btn">Save</button>
    <a href="student_profile.php" class="btn secondary" style="text-decoration:none; display:block; text-align:center;">Cancel</a>
  </form>
</div>

</body>
</html>
