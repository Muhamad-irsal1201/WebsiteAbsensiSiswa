<?php
// php/edit_lesson.php
// Edit jadwal pelajaran

$koneksi_file = __DIR__ . '/../koneksi/koneksi.php';
if (!file_exists($koneksi_file)) {
    die("File koneksi tidak ditemukan: $koneksi_file");
}
include_once $koneksi_file;

if (isset($koneksi) && $koneksi instanceof mysqli) {
    $db = $koneksi;
} elseif (isset($conn) && $conn instanceof mysqli) {
    $db = $conn;
} else {
    $db = mysqli_connect('localhost','root','','smp');
    if (!$db) die("Koneksi fallback gagal: " . mysqli_connect_error());
}

// inisialisasi pesan
$error = "";
$success = "";

// ambil data berdasarkan id
$id = $_GET['id'] ?? '';
if ($id === '') {
    die("ID jadwal tidak ditemukan.");
}

$sqlGet = "SELECT * FROM lesson_schedule WHERE id=?";
$stmt = mysqli_prepare($db, $sqlGet);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);

if (!$data) {
    die("Data jadwal tidak ditemukan.");
}

/**
 * Endpoint AJAX: saran slot kosong
 */
if (isset($_GET['action']) && $_GET['action'] === 'suggest' && isset($_GET['day'])) {
    $day = $_GET['day'];
    $class = $_GET['class'] ?? '';
    $teacher = $_GET['teacher'] ?? '';

    $stmt = mysqli_prepare($db, "SELECT time_start, time_end, teacher, class FROM lesson_schedule WHERE day = ? AND id<>?");
    mysqli_stmt_bind_param($stmt, "si", $day, $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    $occupied = [];
    while ($r = mysqli_fetch_assoc($res)) {
        $occupied[] = [
            'start' => $r['time_start'],
            'end'   => $r['time_end'],
            'teacher' => $r['teacher'],
            'class' => $r['class']
        ];
    }
    mysqli_stmt_close($stmt);

    $slot_duration = 45 * 60;
    $start_day = strtotime("07:00");
    $end_day   = strtotime("16:00");

    $suggestions = [];
    for ($t = $start_day; $t + $slot_duration <= $end_day; $t += $slot_duration) {
        $s = date("H:i", $t);
        $e = date("H:i", $t + $slot_duration);

        $bentrok = false;
        foreach ($occupied as $occ) {
            if ($teacher !== '' && $occ['teacher'] === $teacher) {
                if (!( $e <= $occ['start'] || $s >= $occ['end'] )) {
                    $bentrok = true; break;
                }
            }
            if ($class !== '' && $occ['class'] === $class) {
                if (!( $e <= $occ['start'] || $s >= $occ['end'] )) {
                    $bentrok = true; break;
                }
            }
        }
        if (!$bentrok) $suggestions[] = $s . ' - ' . $e;
    }

    header('Content-Type: application/json');
    echo json_encode($suggestions);
    exit;
}

/**
 * Proses submit update
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class   = trim($_POST['class'] ?? '');

    $subject = trim($_POST['subject'] ?? '');
    $subject_new = trim($_POST['subject_new'] ?? '');
    if ($subject_new !== '') $subject = $subject_new;

    $teacher = trim($_POST['teacher'] ?? '');
    $teacher_new = trim($_POST['teacher_new'] ?? '');
    if ($teacher_new !== '') $teacher = $teacher_new;

    $day = trim($_POST['day'] ?? '');
    $time_start = $_POST['time_start'] ?? '';
    $time_end   = $_POST['time_end'] ?? '';

    if ($class === '' || $subject === '' || $teacher === '' || $day === '' || $time_start === '' || $time_end === '') {
        $error = "Semua field wajib diisi.";
    } else {
        if (!preg_match('/^\d{2}:\d{2}$/', $time_start) || !preg_match('/^\d{2}:\d{2}$/', $time_end)) {
            $error = "Format waktu harus HH:MM (contoh 08:00).";
        } elseif (strtotime($time_start) >= strtotime($time_end)) {
            $error = "Start time harus lebih kecil dari end time.";
        } else {
            $sqlCheck = "SELECT * FROM lesson_schedule 
                         WHERE day = ? 
                           AND (teacher = ? OR class = ?)
                           AND NOT (time_end <= ? OR time_start >= ?)
                           AND id<>?";
            $stmt2 = mysqli_prepare($db, $sqlCheck);
            mysqli_stmt_bind_param($stmt2, "sssssi", $day, $teacher, $class, $time_start, $time_end, $id);
            mysqli_stmt_execute($stmt2);
            $resCheck = mysqli_stmt_get_result($stmt2);

            if (mysqli_num_rows($resCheck) > 0) {
                $error = "Tidak dapat menyimpan: jadwal bentrok dengan jadwal yang sudah ada (guru/kelas).";
            } else {
                $sqlUpdate = "UPDATE lesson_schedule 
                              SET class=?, subject=?, teacher=?, day=?, time_start=?, time_end=?
                              WHERE id=?";
                $stmt3 = mysqli_prepare($db, $sqlUpdate);
                if ($stmt3) {
                    mysqli_stmt_bind_param($stmt3, "ssssssi", $class, $subject, $teacher, $day, $time_start, $time_end, $id);
                    $ok = mysqli_stmt_execute($stmt3);
                    if ($ok) {
                        mysqli_stmt_close($stmt3);
                        header("Location: lesson.php?class=" . urlencode($class) . "&updated=1");
                        exit;
                    } else {
                        $error = "Gagal mengupdate data: " . mysqli_error($db);
                        mysqli_stmt_close($stmt3);
                    }
                } else {
                    $error = "Gagal menyiapkan query: " . mysqli_error($db);
                }
            }
            mysqli_stmt_close($stmt2);
        }
    }
}

// ambil daftar guru untuk dropdown
$teachers = [];
$q = mysqli_query($db, "SELECT id, name, subject FROM users WHERE role='teacher' ORDER BY name");
if ($q) {
    while ($r = mysqli_fetch_assoc($q)) {
        $teachers[] = $r;
    }
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Edit Lesson</title>
<style>
* { box-sizing: border-box; margin:0; padding:0; font-family: Arial, sans-serif; }
body { background:#f1f1f1; padding:16px; min-height:100vh; display:flex; align-items:center; justify-content:center; }
.card { width:100%; max-width:540px; background:#fff; padding:24px; border-radius:10px; box-shadow:0 6px 18px rgba(0,0,0,0.08); }
h2 { text-align:center; color:#2e7d32; margin-bottom:16px; }
.form-group { margin-bottom:12px; }
label { display:block; margin-bottom:6px; color:#333; font-size:14px; }
input[type="text"], input[type="time"], select { width:100%; padding:10px 12px; border-radius:6px; border:1px solid #ccc; font-size:14px; background:#fff; }
.btn { width:100%; padding:12px; background:#2e7d32; color:#fff; border:0; border-radius:6px; font-size:16px; cursor:pointer; }
.btn.secondary { background:#6c757d; margin-top:8px; }
.msg { padding:10px 12px; border-radius:6px; margin-bottom:12px; font-weight:500; }
.msg.error { background:#ffecec; color:#b00020; border:1px solid #f5c6c6; }
.msg.success { background:#eaffea; color:#0b6b13; border:1px solid #c7f0c7; }
.note { font-size:12px; color:#666; margin-top:4px; }
.suggestions { margin-top:8px; }
@media (max-width:600px){ .card{ padding:18px; max-width:420px; } .btn{ padding:10px; } }
</style>
</head>
<body>

<div class="card">
  <h2>Lesson Schedule Edit</h2>

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
        <option value="VII" <?php if($data['class']=='VII') echo 'selected'; ?>>VII</option>
        <option value="VIII" <?php if($data['class']=='VIII') echo 'selected'; ?>>VIII</option>
        <option value="IX" <?php if($data['class']=='IX') echo 'selected'; ?>>IX</option>
      </select>
    </div>

    <div class="form-group">
      <label for="subject">Subject Teacher (select or type new)</label>
      <select id="subject" name="subject">
        <option value="">-- Select a Subject Teacher --</option>
        <?php foreach($teachers as $t): ?>
          <option value="<?php echo htmlspecialchars($t['subject']); ?>" 
            <?php if($data['subject']==$t['subject']) echo 'selected'; ?>>
            <?php echo htmlspecialchars($t['subject']); ?>
          </option>
        <?php endforeach; ?>
      </select>
      <input type="text" name="subject_new" placeholder="Atau ketik mapel baru" />
      <div class="note">leave blank if selecting above</div>
    </div>

    <div class="form-group">
      <label for="teacher">Teacher Name (select or type new)</label>
      <select id="teacher" name="teacher">
        <option value="">-- Select a Teacher Name --</option>
        <?php foreach($teachers as $t): ?>
          <option value="<?php echo htmlspecialchars($t['name']); ?>" 
            <?php if($data['teacher']==$t['name']) echo 'selected'; ?>>
            <?php echo htmlspecialchars($t['name']); ?> (<?php echo htmlspecialchars($t['subject']); ?>)
          </option>
        <?php endforeach; ?>
      </select>
      <input type="text" name="teacher_new" placeholder="Atau ketik nama guru baru" />
      <div class="note">leave blank if selecting above</div>
    </div>

    <div class="form-group">
      <label for="day">Day</label>
      <select id="day" name="day" required>
        <option value="">-- Select Day --</option>
        <?php
          $days = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
          foreach ($days as $d) {
            $sel = ($data['day']==$d) ? 'selected':'';
            echo "<option value=\"$d\" $sel>$d</option>";
          }
        ?>
      </select>
    </div>

    <div class="form-group">
      <label for="time_start">Start Time</label>
      <input id="time_start" name="time_start" type="time" required 
             value="<?php echo htmlspecialchars($data['time_start']); ?>" />
    </div>

    <div class="form-group">
      <label for="time_end">End Time</label>
      <input id="time_end" name="time_end" type="time" required 
             value="<?php echo htmlspecialchars($data['time_end']); ?>" />
    </div>

    <div class="form-group suggestions">
      <label for="suggestions">Suggested empty schedule (click for use)</label>
      <select id="suggestions" class="form-control">
        <option value="">-- Suggested Select Schedule --</option>
      </select>
      <div class="note">Recommendations will appear after selecting Day & Teacher/Class.</div>
    </div>

    <button type="submit" class="btn">Save</button>
    <a href="lesson.php" class="btn secondary" style="text-decoration:none; display:block; text-align:center;">Cancel</a>
  </form>
</div>

<script>
const dayEl = document.getElementById('day');
const classEl = document.getElementById('class');
const teacherEl = document.getElementById('teacher');
const suggestionsEl = document.getElementById('suggestions');

function fetchSuggestions() {
  const day = dayEl.value;
  const cls = classEl.value;
  const teacher = teacherEl.value;
  if (!day) {
    suggestionsEl.innerHTML = '<option value="">-- Pilih Saran Jadwal --</option>';
    return;
  }
  const url = `edit_lesson.php?action=suggest&id=<?php echo $id; ?>&day=${encodeURIComponent(day)}&class=${encodeURIComponent(cls)}&teacher=${encodeURIComponent(teacher)}`;
  fetch(url)
    .then(r => r.json())
    .then(data => {
      suggestionsEl.innerHTML = '<option value="">-- Pilih Saran Jadwal --</option>';
      data.forEach(slot => {
        const opt = document.createElement('option');
        opt.value = slot;
        opt.textContent = slot;
        suggestionsEl.appendChild(opt);
      });
    })
    .catch(err => console.error('Error fetch suggestions', err));
}

suggestionsEl.addEventListener('change', function(){
  const val = this.value;
  if (!val) return;
  const parts = val.split('-').map(s => s.trim());
  if (parts.length === 2) {
    document.getElementById('time_start').value = parts[0];
    document.getElementById('time_end').value   = parts[1];
  }
});

dayEl.addEventListener('change', fetchSuggestions);
classEl.addEventListener('change', fetchSuggestions);
teacherEl.addEventListener('change', fetchSuggestions);

document.addEventListener('DOMContentLoaded', () => {
  if (dayEl.value) fetchSuggestions();
});
</script>
</body>
</html>

