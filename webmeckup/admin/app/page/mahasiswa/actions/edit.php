<?php
// FILE: admin/app/page/mahasiswa/actions/edit.php
require_once '../../../../config/koneksi.php';
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = mysqli_real_escape_string($koneksi, $_GET['id'] ?? '');
    $row = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM data_mahasiswa WHERE id_mahasiswa='$id'"));
    echo json_encode($row ? ['status' => 'success', 'message' => 'OK', 'data' => $row] : ['status' => 'error', 'message' => 'Data tidak ditemukan.']);
    exit;
}
$id = mysqli_real_escape_string($koneksi, trim($_POST['id_mahasiswa'] ?? ''));
$nama = mysqli_real_escape_string($koneksi, trim($_POST['nama_mahasiswa'] ?? ''));
$jk = mysqli_real_escape_string($koneksi, trim($_POST['jenis_kelamin'] ?? ''));
$tgl = mysqli_real_escape_string($koneksi, trim($_POST['tanggal_lahir'] ?? ''));
if (!$id || !$nama) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap.']);
    exit;
}
mysqli_query($koneksi, "UPDATE data_mahasiswa SET nama_mahasiswa='$nama',jenis_kelamin='$jk',tanggal_lahir='$tgl' WHERE id_mahasiswa='$id'");
echo json_encode([
    'status' => 'success',
    'message' => 'DATA BERHASIL DIPERBARUI',
    'data' => ['id_mahasiswa' => $id, 'nama_mahasiswa' => $nama, 'jenis_kelamin' => $jk, 'tanggal_lahir' => $tgl]
]);