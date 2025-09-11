<?php
session_start();
if(isset($_SESSION['username'])){
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Absensi</title>
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
    <section class="login-section">
        <div class="container">
            <div class="row">
                <!-- Kiri: Logo + Nama Sekolah -->
                <div class="column left">
                    <img src="img/bpsk.png" alt="Logo Sekolah" class="logo">
                    <h1 class="school-name">SMP BPSK</h1>
                </div>

                <!-- Kanan: Form Login -->
                <div class="column right">
                    <div class="login-box">
                        <h2>LOGIN</h2>
                        <form action="php/login.php" method="POST">
                            <input type="text" name="username" placeholder="Username" required>
                            <input type="password" name="password" placeholder="Password" required>
                            <button type="submit"><img src="icon/avatar.png" alt="">Login</button>
                        </form>
                        <p>Belum punya akun? <a href="php/signup.php">Sign Up</a></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="js/script.js"></script>
</body>
</html>
