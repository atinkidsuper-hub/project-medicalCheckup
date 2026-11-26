<?php
// FILE: admin/app/page/riwayat/actions/edit.php
require_once '../../../../config/koneksi.php';
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = mysqli_real_escape_string($koneksi, $_GET['id'] ?? '');
    $row = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT r.*,m.nama_mahasiswa FROM data_riwayat r LEFT JOIN data_mahasiswa m ON r.id_mahasiswa=m.id_mahasiswa WHERE r.id_riwayat='$id'"));
    echo json_encode($row ? ['status' => 'success', 'message' => 'OK', 'data' => $row] : ['status' => 'error', 'message' => 'Data tidak ditemukan.']);
    exit;
}
$id = mysqli_real_escape_string($koneksi, trim($_POST['id_riwayat'] ?? ''));
$mhs = mysqli_real_escape_string($koneksi, trim($_POST['id_mahasiswa'] ?? ''));
$waktu = mysqli_real_escape_string($koneksi, str_replace('T', ' ', trim($_POST['waktu'] ?? '')));
$suhu = mysqli_real_escape_string($koneksi, trim($_POST['suhu_tubuh'] ?? ''));
$spo2 = mysqli_real_escape_string($koneksi, trim($_POST['spo2'] ?? ''));
$detak = mysqli_real_escape_string($koneksi, trim($_POST['detak_jantung'] ?? ''));
$td = mysqli_real_escape_string($koneksi, trim($_POST['tekanan_darah'] ?? ''));
if (!$id || !$mhs) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap.']);
    exit;
}
mysqli_query($koneksi, "UPDATE data_riwayat SET id_mahasiswa='$mhs',waktu='$waktu',suhu_tubuh=" . ($suhu ?: "NULL") . ",spo2=" . ($spo2 ?: "NULL") . ",detak_jantung=" . ($detak ?: "NULL") . ",tekanan_darah=" . ($td ? "'$td'" : "NULL") . " WHERE id_riwayat='$id'");
$nm = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT nama_mahasiswa,jenis_kelamin,tanggal_lahir FROM data_mahasiswa WHERE id_mahasiswa='$mhs'"));
echo json_encode([
    'status' => 'success',
    'message' => 'DATA BERHASIL DIPERBARUI',
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