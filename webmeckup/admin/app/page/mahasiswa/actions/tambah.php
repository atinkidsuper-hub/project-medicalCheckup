<?php
// FILE: admin/app/page/mahasiswa/actions/tambah.php
require_once '../../../../config/koneksi.php';
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST')
    exit;
$nama = mysqli_real_escape_string($koneksi, trim($_POST['nama_mahasiswa'] ?? ''));
$jk = mysqli_real_escape_string($koneksi, trim($_POST['jenis_kelamin'] ?? ''));
$tgl = mysqli_real_escape_string($koneksi, trim($_POST['tanggal_lahir'] ?? ''));
if (!$nama) {
    echo json_encode(['status' => 'error', 'message' => 'Nama wajib diisi.']);
    exit;
}
$id = uniqid('mhs_');
mysqli_query($koneksi, "INSERT INTO data_mahasiswa VALUES('$id','$nama','$jk','$tgl')");
echo json_encode([
    'status' => 'success',
    'message' => 'DATA BERHASIL DITAMBAHKAN',
    'data' => ['id_mahasiswa' => $id, 'nama_mahasiswa' => $nama, 'jenis_kelamin' => $jk, 'tanggal_lahir' => $tgl]
]);