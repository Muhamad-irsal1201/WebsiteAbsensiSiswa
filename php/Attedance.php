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
$role       = $_SESSION['role']; // cek role

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

// Ambil attendance guru ini
$attendanceQuery = "SELECT * FROM result 
                    WHERE teacher_id='$teacher_id' 
                      AND class='$selected_class' 
                      AND month='$selected_month'";
$attendanceResult = mysqli_query($koneksi, $attendanceQuery);
$attendance = [];
while($row = mysqli_fetch_assoc($attendanceResult)){
    $attendance[$row['student_id']][$row['week']] = $row['status'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Attendance - <?php echo $selected_class; ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../css/Attedance.css">
<script src="../js/Attedance.js" defer></script>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar shadow">
    <div class="nav-left">
      <button class="toggle-btn" id="toggle-btn">&#9776;</button>
    </div>
    <div class="nav-right">
      <!-- tampilkan role -->
      <span class="user-info">
        <?php echo $name; ?> (<?php echo $subject; ?>) - 
        <strong><?php echo ucfirst($role); ?></strong>
      </span>
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
          <li><a class="dropdown-item" href="result.php">Result</a></li>
        </ul>
      </li>
      <li><a href="lesson.php"">Lesson Schedule</a></li>
      <li><a href="#">Select Language</a></li>
    </ul>
</aside>

<!-- CONTENT -->
<main class="content shifted" id="content">
  <section class="attendance-container">
    <h2>Attendance - Class <?php echo $selected_class; ?></h2>

    <!-- Filter -->
    <form method="GET" action="Attedance.php" class="filter-form">
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

    <!-- Table -->
    <table class="attendance-table">
      <thead>
        <tr>
          <th>Name</th>
          <th>Class</th>
          <th>Week 1</th>
          <th>Week 2</th>
          <th>Week 3</th>
          <th>Week 4</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php while($row = mysqli_fetch_assoc($result)): ?>
        <tr data-student="<?php echo $row['id']; ?>">
          <td><?php echo $row['name']; ?></td>
          <td><?php echo $row['class']; ?></td>
          <td class="week-1"><?php echo $attendance[$row['id']][1] ?? ''; ?></td>
          <td class="week-2"><?php echo $attendance[$row['id']][2] ?? ''; ?></td>
          <td class="week-3"><?php echo $attendance[$row['id']][3] ?? ''; ?></td>
          <td class="week-4"><?php echo $attendance[$row['id']][4] ?? ''; ?></td>
          <td>
            <?php if($role === 'teacher'): ?>
              <!-- Guru bisa input/edit attendance -->
              <button type="button" class="btn btn-success btn-sm hadir" data-week="1">Hadir</button>
              <button type="button" class="btn btn-warning btn-sm izin" data-week="1">Izin</button>
              <button type="button" class="btn btn-info btn-sm sakit" data-week="1">Sakit</button>
              <button type="button" class="btn btn-danger btn-sm absen" data-week="1">Tidak Hadir</button>
            <?php else: ?>
              <!-- Kepala sekolah hanya view -->
              <span class="text-muted">View only</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </section>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
