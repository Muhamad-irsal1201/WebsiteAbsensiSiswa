<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}

require_once "../koneksi/koneksi.php";

$name       = $_SESSION['name'];
$subject    = $_SESSION['subject'];
$teacher_id = $_SESSION['id']; 
$role       = $_SESSION['role']; // ambil role

// Filter kelas
$selected_class = isset($_GET['class']) ? $_GET['class'] : 'VII';

// Filter bulan
$selected_month = isset($_GET['month']) ? $_GET['month'] : date("m");

// Mapping kelas ke tabel
$tableMap = [
    'VII'  => 'students_vii',
    'VIII' => 'students_viii',
    'IX'   => 'students_ix'
];

$table = $tableMap[$selected_class];

// Ambil data siswa
$query  = "SELECT * FROM $table";
$result = mysqli_query($koneksi, $query) or die("Query Error: " . mysqli_error($koneksi));

// Ambil attendance
if ($role === 'teacher') {
    // Guru hanya lihat data absensinya sendiri
    $attendanceQuery = "SELECT * FROM result 
                        WHERE teacher_id='$teacher_id' 
                          AND class='$selected_class' 
                          AND month='$selected_month'";
} else {
    // Kepala sekolah bisa lihat semua data
    $attendanceQuery = "SELECT * FROM result 
                        WHERE class='$selected_class' 
                          AND month='$selected_month'";
}

$attendanceResult = mysqli_query($koneksi, $attendanceQuery);
$attendance = [];
while ($row = mysqli_fetch_assoc($attendanceResult)) {
    $attendance[$row['student_id']][$row['week']] = $row['status'];
}

// Fungsi untuk hitung % attendance
function calcAttendance($weeks){
    $totalWeek = 4;
    $presentCount = 0;
    foreach ($weeks as $w) {
        if(strtolower($w) == 'hadir') $presentCount++;
        elseif(strtolower($w)=='izin' || strtolower($w)=='sakit') $presentCount += 0.5;
    }
    return round(($presentCount/$totalWeek)*100,2); 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Result Attendance - <?php echo $selected_class; ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../css/result.css">
<script src="../js/result.js" defer></script>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar shadow">
    <div class="nav-left">
      <button class="toggle-btn" id="toggle-btn">&#9776;</button>
    </div>
    <div class="nav-right">
      <span class="user-info"><?php echo $name; ?> (<?php echo $subject; ?>) - <?php echo ucfirst($role); ?></span>
      <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</nav>

<!-- SIDEBAR -->
<aside class="sidebar shadow" id="sidebar">
    <div class="sidebar-logo">
      <img src="../img/bpsk.png" alt="Logo Sekolah">
    </div>
    <ul class="menu">
      <li><a href="dashboard.php">Dashboard</a></li>
      <li><a href="student_profile.php">Student Profile</a></li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle text-white" href="#" data-bs-toggle="dropdown" aria-expanded="false">Attendance</a>
        <ul class="dropdown-menu dropdown-menu-dark">
          <li><a class="dropdown-item" href="Attedance.php">Input Attendance</a></li>
          <li><a class="dropdown-item" href="Result.php">Result</a></li>
        </ul>
      </li>
      <li><a href="lesson.php">Lesson Schedule</a></li>
      <li><a href="#">Select Language</a></li>
    </ul>
</aside>

<!-- CONTENT -->
<main class="content shifted" id="content">
  <section class="attendance-container">
    <h2>Result Attendance - Class <?php echo $selected_class; ?></h2>

    <!-- Filter -->
    <form method="GET" action="Result.php" class="filter-form">
      <label for="class">Select Class: </label>
      <select name="class" id="class" onchange="this.form.submit()">
        <option value="VII" <?php if($selected_class=='VII') echo 'selected'; ?>>VII</option>
        <option value="VIII" <?php if($selected_class=='VIII') echo 'selected'; ?>>VIII</option>
        <option value="IX" <?php if($selected_class=='IX') echo 'selected'; ?>>IX</option>
      </select>

      <label for="month">Select Month: </label>
      <select name="month" id="month" onchange="this.form.submit()">
        <?php 
          for ($m=1;$m<=12;$m++) {
            $val = str_pad($m,2,"0",STR_PAD_LEFT);
            $nameMonth = date("F", mktime(0,0,0,$m,1));
            $sel = ($selected_month==$val) ? "selected" : "";
            echo "<option value='$val' $sel>$nameMonth</option>";
          }
        ?>
      </select>
    </form>

    <!-- Export buttons (hanya guru) -->
    <?php if ($role === 'teacher'): ?>
    <div class="report-buttons" style="margin-bottom:15px;">
        <button type="button" class="btn btn-success btn-sm" onclick="exportExcel('<?php echo $selected_class; ?>')">Export Excel</button>
        <button type="button" class="btn btn-danger btn-sm" onclick="exportPDF('<?php echo $selected_class; ?>')">Export PDF</button>
        <button type="button" class="btn btn-primary btn-sm" onclick="printTable('<?php echo $selected_class; ?>')">Print</button>
    </div>
    <?php endif; ?>

    <!-- Table -->
    <table class="attendance-table">
      <thead>
        <tr>
          <th>Name</th>
          <th>Class</th>
          <th>Total Attendance (%)</th>
        </tr>
      </thead>
      <tbody>
        <?php while($row = mysqli_fetch_assoc($result)): 
            $weeks = $attendance[$row['id']] ?? [];
            $totalPercent = calcAttendance($weeks);
        ?>
        <tr>
          <td><?php echo $row['name']; ?></td>
          <td><?php echo $row['class']; ?></td>
          <td><?php echo $totalPercent; ?>%</td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </section>
</main>

<!-- XLSX untuk Excel -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<!-- jsPDF + autoTable untuk PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
