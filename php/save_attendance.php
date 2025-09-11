<?php
session_start();
require_once "../koneksi/koneksi.php";

// Cek apakah guru login
if (!isset($_SESSION['id'])) {
    echo json_encode(['success'=>false, 'error'=>'Guru belum login']);
    exit;
}

$teacher_id = $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'] ?? '';
    $week       = intval($_POST['week'] ?? 0);
    $status     = $_POST['status'] ?? '';
    $class      = $_POST['class'] ?? '';
    $month      = $_POST['month'] ?? '';

    if (!$student_id || !$week || !$status || !$class || !$month) {
        echo json_encode(['success'=>false, 'error'=>'Data tidak lengkap']);
        exit;
    }

    // Cek apakah sudah ada record untuk guru, siswa, minggu & bulan ini
    $check = mysqli_query($koneksi, "SELECT * FROM result 
        WHERE student_id='$student_id' 
          AND week='$week' 
          AND month='$month' 
          AND teacher_id='$teacher_id'");

    if(mysqli_num_rows($check) > 0){
        // Update jika sudah ada
        $query = "UPDATE result 
                  SET status='$status', updated_at=NOW() 
                  WHERE student_id='$student_id' 
                    AND week='$week' 
                    AND month='$month' 
                    AND teacher_id='$teacher_id'";
    } else {
        // Insert jika belum ada
        $query = "INSERT INTO result (student_id, class, month, week, status, teacher_id, created_at, updated_at)
                  VALUES ('$student_id','$class','$month','$week','$status','$teacher_id', NOW(), NOW())";
    }

    if(mysqli_query($koneksi, $query)){
        echo json_encode(['success'=>true]);
    } else {
        echo json_encode(['success'=>false, 'error'=>mysqli_error($koneksi)]);
    }
} else {
    echo json_encode(['success'=>false, 'error'=>'Metode request salah']);
}
