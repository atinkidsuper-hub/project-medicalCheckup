<?php
// FILE: admin/app/page/mahasiswa/actions/hapus.php
require_once '../../../../config/koneksi.php';
header('Content-Type: application/json');
$id = mysqli_real_escape_string($koneksi, trim($_POST['id'] ?? ''));
if (!$id) {
    echo json_encode(['status' => 'error', 'message' => 'ID tidak ada.']);
    exit;
}
mysqli_query($koneksi, "DELETE FROM data_mahasiswa WHERE id_mahasiswa='$id'");
echo json_encode(['status' => 'success', 'message' => 'DATA BERHASIL DIHAPUS']);