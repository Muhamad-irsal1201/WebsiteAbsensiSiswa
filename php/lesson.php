<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}

require_once "../koneksi/koneksi.php";

$name    = $_SESSION['name'];
$subject = $_SESSION['subject'];
$role    = $_SESSION['role'];

// Filter untuk headmaster
$filter_subject = $_GET['filter_subject'] ?? "";

// Query
if ($role === "headmaster") {
    if (!empty($filter_subject)) {
        $query = "SELECT * FROM lesson_schedule WHERE subject = ? ORDER BY class, day, time_start";
        $stmt  = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "s", $filter_subject);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    } else {
        $query  = "SELECT * FROM lesson_schedule ORDER BY class, day, time_start";
        $result = mysqli_query($koneksi, $query) or die("Query Error: " . mysqli_error($koneksi));
    }
} else {
    // Guru hanya bisa lihat jadwal berdasarkan subject mereka
    $query = "SELECT * FROM lesson_schedule WHERE subject = ? ORDER BY class, day, time_start";
    $stmt  = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "s", $subject);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lesson Schedule</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/lesson.css">
  <script src="../js/lesson.js" defer></script>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar shadow">
    <div class="nav-left">
      <button class="toggle-btn" id="toggle-btn">&#9776;</button>
    </div>
    <div class="nav-right">
      <span class="user-info"><?php echo $name; ?> (<?php echo $subject; ?> - <?php echo $role; ?>)</span>
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
        <a class="nav-link dropdown-toggle text-white" href="#" data-bs-toggle="dropdown">Attendance</a>
        <ul class="dropdown-menu dropdown-menu-dark">
          <li><a class="dropdown-item" href="Attedance.php">Input Attendance</a></li>
          <li><a class="dropdown-item" href="result.php">Result</a></li>
        </ul>
      </li>
      <li><a href="lesson.php" class="active">Lesson Schedule</a></li>
      <li><a href="#">Select Language</a></li>
    </ul>
</aside>

<!-- CONTENT -->
<main class="content shifted" id="content">
  <section class="schedule-container">
    <h2>Lesson Schedule</h2>

    <?php if ($role == "headmaster"): ?>
      <div class="mb-3 d-flex align-items-center">
        <a href="add_lesson.php" class="btn btn-success me-3">+ Add Schedule</a>
        <!-- Filter Mata Pelajaran -->
        <form method="get" class="d-flex">
          <select name="filter_subject" class="form-select me-2">
            <option value="">-- All Subject --</option>
            <?php
            $subQuery = "SELECT DISTINCT subject FROM lesson_schedule ORDER BY subject";
            $subResult = mysqli_query($koneksi, $subQuery);
            while ($subRow = mysqli_fetch_assoc($subResult)) {
              $selected = ($filter_subject == $subRow['subject']) ? "selected" : "";
              echo "<option value='{$subRow['subject']}' $selected>{$subRow['subject']}</option>";
            }
            ?>
          </select>
          <button type="submit" class="btn btn-primary">Filter</button>
        </form>
      </div>
    <?php endif; ?>

   <!-- Tabel Lesson Schedule -->
<table class="student-table">
  <thead>
    <tr>
      <th>Class</th>
      <th>Subject</th>
      <th>Teacher</th>
      <th>Day</th>
      <th>Start</th>
      <th>End</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
  <?php while($row = mysqli_fetch_assoc($result)): ?>
    <tr>
      <td><?php echo $row['class']; ?></td>
      <td><?php echo $row['subject']; ?></td>
      <td><?php echo $row['teacher']; ?></td>
      <td><?php echo $row['day']; ?></td>
      <td><?php echo $row['time_start']; ?></td>
      <td><?php echo $row['time_end']; ?></td>
      <td>
        <?php if ($role === 'headmaster'): ?>
          <a href="edit_lesson.php?id=<?php echo $row['id']; ?>" class="btn edit">Edit</a>
          <a href="delete_lesson.php?id=<?php echo $row['id']; ?>" class="btn delete" onclick="return confirm('Are you sure, want to delete this schedule?')">Delete</a>
        <?php else: ?>
          <span class="text-muted">View Only</span>
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
