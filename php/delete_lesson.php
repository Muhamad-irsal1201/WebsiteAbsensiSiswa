<?php
session_start();
include '../koneksi/koneksi.php';

// Cek role, hanya headmaster yang boleh hapus
if ($_SESSION['role'] !== 'headmaster') {
    echo "<script>alert('Akses ditolak!');window.location='lesson.php';</script>";
    exit;
}

// Pastikan ada ID
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // hindari SQL Injection

    // Hapus data
    $query = "DELETE FROM lesson_schedule WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('The schedule has been successfully deleted.');window.location='lesson.php';</script>";
    } else {
        echo "<script>alert('Schedule failed to be deleted');window.location='lesson.php';</script>";
    }
} else {
    header("Location: lesson.php");
    exit;
}
?>
