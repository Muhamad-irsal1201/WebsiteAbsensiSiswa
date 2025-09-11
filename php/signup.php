<?php
// signup.php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "smp";

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$success = "";
$error = "";

if (isset($_POST['signup'])) {
    $name     = trim($_POST['name'] ?? '');
    $subject  = trim($_POST['subject'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $pwd_raw  = $_POST['password'] ?? '';
    $role     = $_POST['role'] ?? 'teacher'; // default teacher

    if ($name === "" || $subject === "" || $email === "" || $username === "" || $pwd_raw === "" || $role === "") {
        $error = "Semua field wajib diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid.";
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $error = "Username sudah digunakan, silakan pilih username lain.";
            mysqli_stmt_close($stmt);
        } else {
            mysqli_stmt_close($stmt);

            $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            if (mysqli_stmt_num_rows($stmt) > 0) {
                $error = "Email sudah terdaftar, silakan gunakan email lain.";
                mysqli_stmt_close($stmt);
            } else {
                mysqli_stmt_close($stmt);

                $password_hash = password_hash($pwd_raw, PASSWORD_DEFAULT);

                $stmt = mysqli_prepare($conn, "INSERT INTO users (name, subject, email, username, password, role) VALUES (?, ?, ?, ?, ?, ?)");
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "ssssss", $name, $subject, $email, $username, $password_hash, $role);
                    $exec = mysqli_stmt_execute($stmt);
                    if ($exec) {
                        mysqli_stmt_close($stmt);
                        echo "<script>
                                alert('Signup berhasil! Anda akan diarahkan ke halaman login.');
                                window.location.href = '../index.php';
                              </script>";
                        exit;
                    } else {
                        $error = "Gagal menyimpan data: " . htmlspecialchars(mysqli_stmt_error($stmt));
                        mysqli_stmt_close($stmt);
                    }
                } else {
                    $error = "Gagal menyiapkan query: " . htmlspecialchars(mysqli_error($conn));
                }
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
<title>Sign Up</title>
<style>
/* (CSS tetap persis punyamu, tidak diubah) */
* { box-sizing: border-box; margin:0; padding:0; font-family: Arial, sans-serif; }
body { background: #f1f1f1; padding: 16px; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
.signup-container { width: 100%; max-width: 520px; background: #fff; padding: 28px; border-radius: 10px; box-shadow: 0 6px 18px rgba(0,0,0,0.08); }
.signup-container h2 { text-align: center; color: #2e7d32; margin-bottom: 18px; }
.msg { margin-bottom: 14px; padding: 10px 12px; border-radius: 6px; font-weight: 500; }
.msg.error { background:#ffecec; color:#b00020; border:1px solid #f5c6c6; }
.msg.success { background:#eaffea; color:#0b6b13; border:1px solid #c7f0c7; }
.form-row { display: flex; gap: 12px; margin-bottom: 12px; flex-wrap: wrap; }
.form-group { flex:1; min-width: 0; }
label { display:block; margin-bottom:6px; font-size:14px; color:#333; }
input[type="text"], input[type="email"], input[type="password"], select {
  width:100%; padding:10px 12px; border-radius:6px; border:1px solid #ccc;
  font-size:14px; background:#fff;
}
.full { width:100%; }
button[type="submit"] { width:100%; padding:12px; background:#2e7d32; color:#fff; border:0; border-radius:6px; font-size:16px; cursor:pointer; }
button[type="submit"]:hover { background:#27632a; }
@media (max-width: 600px) {
  .signup-container { padding:18px; max-width: 420px; }
  .form-row { gap:8px; }
  button[type="submit"] { padding:10px; font-size:15px; }
}
</style>
</head>
<body>

<div class="signup-container">
  <h2>Sign Up</h2>

  <?php if ($error !== ""): ?>
    <div class="msg error"><?php echo htmlspecialchars($error); ?></div>
  <?php endif; ?>

  <form method="post" action="">
    <div class="form-row">
      <div class="form-group">
        <label for="name">Name</label>
        <input id="name" name="name" type="text" required value="<?php echo isset($_POST['name'])?htmlspecialchars($_POST['name']):''; ?>">
      </div>
      <div class="form-group">
        <label for="subject">Subject (Teacher)</label>
        <input id="subject" name="subject" type="text" required value="<?php echo isset($_POST['subject'])?htmlspecialchars($_POST['subject']):''; ?>">
      </div>
    </div>

    <div class="form-row">
      <div class="form-group full">
        <label for="email">Email</label>
        <input id="email" name="email" type="email" required value="<?php echo isset($_POST['email'])?htmlspecialchars($_POST['email']):''; ?>">
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label for="username">Username</label>
        <input id="username" name="username" type="text" required value="<?php echo isset($_POST['username'])?htmlspecialchars($_POST['username']):''; ?>">
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input id="password" name="password" type="password" required>
      </div>
    </div>

    <div class="form-row">
      <div class="form-group full">
        <label for="role">Role</label>
        <select id="role" name="role" required>
          <option value="teacher" <?php if(isset($_POST['role']) && $_POST['role']=='teacher') echo 'selected'; ?>>Teacher</option>
          <option value="headmaster" <?php if(isset($_POST['role']) && $_POST['role']=='headmaster') echo 'selected'; ?>>Headmaster</option>
        </select>
      </div>
    </div>

    <button type="submit" name="signup">Sign Up</button>
  </form>
</div>

</body>
</html>
