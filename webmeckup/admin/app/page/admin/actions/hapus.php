<?php
// FILE: admin/app/page/admin/actions/hapus.php
header('Content-Type: application/json');
require_once '../../../../../config/koneksi.php';

$id = intval($_POST['id'] ?? 0);
if (!$id) {
    echo json_encode(['status' => 'error', 'message' => 'ID tidak valid']);
    exit;
}

// Jangan hapus diri sendiri
session_start();
if ($id == ($_SESSION['admin_id'] ?? 0)) {
    echo json_encode(['status' => 'error', 'message' => 'Tidak bisa menghapus akun yang sedang login']);
    exit;
}

if (mysqli_query($koneksi, "DELETE FROM data_admin WHERE id_admin=$id")) {
    echo json_encode(['status' => 'success', 'message' => 'Admin berhasil dihapus']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus: ' . mysqli_error($koneksi)]);
}