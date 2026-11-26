<?php
// FILE: admin/app/page/riwayat/actions/tambah.php
require_once '../../../../config/koneksi.php';
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST')
    exit;
$mhs = mysqli_real_escape_string($koneksi, trim($_POST['id_mahasiswa'] ?? ''));
$waktu = mysqli_real_escape_string($koneksi, trim($_POST['waktu'] ?? date('Y-m-d H:i:s')));
$suhu = mysqli_real_escape_string($koneksi, trim($_POST['suhu_tubuh'] ?? ''));
$spo2 = mysqli_real_escape_string($koneksi, trim($_POST['spo2'] ?? ''));
$detak = mysqli_real_escape_string($koneksi, trim($_POST['detak_jantung'] ?? ''));
$td = mysqli_real_escape_string($koneksi, trim($_POST['tekanan_darah'] ?? ''));
if (!$mhs) {
    echo json_encode(['status' => 'error', 'message' => 'Pilih mahasiswa terlebih dahulu.']);
    exit;
}
// Format datetime
$waktu = str_replace('T', ' ', $waktu);
$id = uniqid('rw_');
mysqli_query($koneksi, "INSERT INTO data_riwayat VALUES('$id','$mhs','$waktu','" . ($suhu ?: NULL) . "','" . ($spo2 ?: NULL) . "','" . ($detak ?: NULL) . "','" . ($td ?: NULL) . "')");
// Ambil nama mahasiswa untuk response
$nm = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT nama_mahasiswa,jenis_kelamin,tanggal_lahir FROM data_mahasiswa WHERE id_mahasiswa='$mhs'"));
echo json_encode([
    'status' => 'success',
    'message' => 'DATA BERHASIL DITAMBAHKAN',
    'data' => [
        'id_riwayat' => $id,
        'id_mahasiswa' => $mhs,
        'waktu' => $waktu,
        'suhu_tubuh' => $suhu,
        'spo2' => $spo2,
        'detak_jantung' => $detak,
        'tekanan_darah' => $td,
        'nama_mahasiswa' => $nm['nama_mahasiswa'] ?? '',
        'jenis_kelamin' => $nm['jenis_kelamin'] ?? '',
        'tanggal_lahir' => $nm['tanggal_lahir'] ?? ''
    ]
]);