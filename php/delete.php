<?php
// php/delete_student.php

// include koneksi
$koneksi_file = __DIR__ . '/../koneksi/koneksi.php';
if (!file_exists($koneksi_file)) {
    die("File koneksi tidak ditemukan: $koneksi_file");
}
include_once $koneksi_file;

// normalisasi koneksi
if (isset($koneksi) && $koneksi instanceof mysqli) {
    $db = $koneksi;
} elseif (isset($conn) && $conn instanceof mysqli) {
    $db = $conn;
} else {
    $db = mysqli_connect('localhost','root','','smp');
    if (!$db) die("Koneksi gagal: " . mysqli_connect_error());
}

// mapping tabel
$allowed_tables = [
    'VII'  => 'students_vii',
    'VIII' => 'students_viii',
    'IX'   => 'students_ix',
    'student' => 'student' // fallback
];

// ambil parameter
$id    = intval($_GET['id'] ?? 0);
$class = $_GET['class'] ?? '';
$table = $allowed_tables[$class] ?? 'student';

if ($id > 0 && $class !== '') {
    $sql = "DELETE FROM `$table` WHERE id=?";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // redirect kembali ke student_profile dengan info sukses
    header("Location: student_profile.php?class=" . urlencode($class) . "&deleted=1");
    exit;
} else {
    die("Data tidak lengkap untuk menghapus.");
}
