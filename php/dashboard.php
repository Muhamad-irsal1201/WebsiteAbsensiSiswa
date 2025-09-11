<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}

require_once "../koneksi/koneksi.php";

// Data dari session
$name    = $_SESSION['name'];
$subject = $_SESSION['subject'];
$role    = $_SESSION['role']; // ambil role dari session
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/dash.css">
  <script src="../js/dashboard.js" defer></script>
</head>
<body>

  <!-- NAVBAR -->
  <nav class="navbar shadow">
    <div class="nav-left">
      <button class="toggle-btn" id="toggle-btn">&#9776;</button>
    </div>
    <div class="nav-right">
      <!-- tampilkan role -->
      <img src="../icon/user.png" alt=""><span class="user-info">
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
  <li>
    <a href="dashboard.php" class="active">
      <img src="../icon/dashboard.png" alt="Dashboard Icon" width="16" height="16">
      Dashboard
    </a>
  </li>
  <li>
    <a href="student_profile.php">
      <img src="../icon/student.png" alt="Student Icon" width="16" height="16">
      Student Profile
    </a>
  </li>
  <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle text-white" href="#" data-bs-toggle="dropdown" aria-expanded="false">
      <img src="../icon/attendance.png" alt="Attendance Icon" width="16" height="16">
      Attendance
    </a>
    <ul class="dropdown-menu dropdown-menu-dark">
      <li>
        <a class="dropdown-item" href="attedance.php">
          <img src="../icon/attendance2.png" alt="Input Icon" width="16" height="16">
          Input Attendance
        </a>
      </li>
      <li>
        <a class="dropdown-item" href="result.php">
          <img src="../icon/result.png" alt="Result Icon" width="16" height="16">
          Result
        </a>
      </li>
    </ul>
  </li>
  <li>
    <a href="lesson.php">
      <img src="../icon/lesson.png" alt="Lesson Icon" width="16" height="16">
      Lesson Schedule
    </a>
  </li>
</ul>

  </aside>

  <!-- Section Sejarah Sekolah -->
  <section class="school-section">
    <div class="school-row">
      <div class="school-col image-col">
        <img src="../img/gedung.jpeg" alt="Foto Sekolah">
      </div>
      <div class="school-col text-col">
        <h2>Sejarah Sekolah</h2>
        <p>
          Sekolah ini berdiri pada tahun 1985 dan memiliki visi untuk mencetak generasi yang cerdas, kreatif, dan berakhlak mulia. 
          Berbagai prestasi akademik dan non-akademik telah diraih oleh siswa-siswi kami hingga saat ini.
        </p>
        <p>
          Dengan fasilitas lengkap dan tenaga pengajar profesional, sekolah kami berkomitmen untuk memberikan pendidikan terbaik.
        </p>
      </div>
    </div>
  </section>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
