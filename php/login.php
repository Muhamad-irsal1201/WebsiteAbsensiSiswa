<?php
session_start();
include '../koneksi/koneksi.php';

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password']; // jangan escape password, nanti diverifikasi dengan hash

    // Ambil user berdasarkan username
    $query = "SELECT * FROM users WHERE username='$username' LIMIT 1";
    $result = mysqli_query($koneksi, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        // Verifikasi password dengan hash
        if (password_verify($password, $row['password'])) {
            // Login sukses
            $_SESSION['id']       = $row['id'];       // id user
            $_SESSION['name']     = $row['name'];     // nama user
            $_SESSION['subject']  = $row['subject'];  // mata pelajaran
            $_SESSION['username'] = $row['username']; // username
            $_SESSION['role']     = $row['role'];     // role (teacher/headmaster)

            header("Location: ../php/dashboard.php");
            exit;
        } else {
            echo "<script>alert('Password salah');window.location='../index.php';</script>";
        }
    } else {
        echo "<script>alert('Username tidak ditemukan');window.location='../index.php';</script>";
    }
} else {
    header("Location: ../index.php");
    exit;
}
?>
