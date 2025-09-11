<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}

require_once "../koneksi/koneksi.php"; // ini bikin $koneksi aktif

$name    = $_SESSION['name'];
$subject = $_SESSION['subject'];
$role    = $_SESSION['role']; // role guru/headmaster

// Ambil filter kelas (default VII)
$selected_class = isset($_GET['class']) ? $_GET['class'] : 'VII';

// Mapping nama kelas ke tabel
$tableMap = [
    'VII'  => 'students_vii',
    'VIII' => 'students_viii',
    'IX'   => 'students_ix'
];

$table = $tableMap[$selected_class];

// Query data
$query  = "SELECT * FROM $table";
$result = mysqli_query($koneksi, $query) or die("Query Error: " . mysqli_error($koneksi));
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Profile - <?php echo $selected_class; ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/student.css">
  <script src="../js/student.js" defer></script>
</head>
<body>

  <!-- NAVBAR -->
  <nav class="navbar shadow">
    <div class="nav-left">
      <button class="toggle-btn" id="toggle-btn">&#9776;</button>
    </div>
    <div class="nav-right">
      <span class="user-info">
        <?php echo $name; ?> (<?php echo $subject; ?>) - <?php echo $role; ?>
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
      <li><a href="student_profile.php" class="active">Student Profile</a></li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle text-white" href="#" data-bs-toggle="dropdown" aria-expanded="false">
          Attendance
        </a>
        <ul class="dropdown-menu dropdown-menu-dark">
          <li><a class="dropdown-item" href="Attedance.php">Input Attendance</a></li>
          <li><a class="dropdown-item" href="result.php">Result</a></li>
        </ul>
      </li>
      <li><a href="lesson.php">Lesson Schedule</a></li>
      <li><a href="#">Select Language</a></li>
    </ul>
  </aside>

  <!-- CONTENT -->
  <main class="content shifted" id="content">
    <section class="profile-container">
      <h2>Student Profile - Class <?php echo $selected_class; ?></h2>

      <!-- Filter Kelas -->
      <form method="GET" class="filter-form">
        <label for="class">Select Class: </label>
        <select name="class" id="class" onchange="this.form.submit()">
          <option value="VII" <?php if($selected_class=='VII') echo 'selected'; ?>>VII</option>
          <option value="VIII" <?php if($selected_class=='VIII') echo 'selected'; ?>>VIII</option>
          <option value="IX" <?php if($selected_class=='IX') echo 'selected'; ?>>IX</option>
        </select>
      </form>

      <!-- Tabel Siswa -->
      <table class="student-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Class</th>
            <th>Name</th>
            <th>NIM</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
        <?php while($row = mysqli_fetch_assoc($result)): ?>
          <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['class']; ?></td>
            <td><?php echo $row['name']; ?></td>
            <td><?php echo $row['nim']; ?></td>
            <td>
              <?php if ($role === 'teacher'): ?>
                <a href="edit.php?class=<?php echo $selected_class; ?>&id=<?php echo $row['id']; ?>" class="btn edit">Edit</a>
                <a href="delete.php?class=<?php echo $selected_class; ?>&id=<?php echo $row['id']; ?>" class="btn delete" onclick="return confirm('Yakin hapus data ini?')">Delete</a>
              <?php else: ?>
                <span class="text-muted">View Only</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>

      <!-- Tombol Tambah -->
      <?php if ($role === 'teacher'): ?>
      <div class="actions">
        <a href="add_student.php?class=<?php echo $selected_class; ?>" class="btn add">+ Student Add</a>
      </div>
      <?php endif; ?>

    </section>
  </main>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
