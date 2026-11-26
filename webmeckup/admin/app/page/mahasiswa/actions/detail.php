<?php
// FILE: admin/app/page/mahasiswa/actions/detail.php
require_once '../../../../config/koneksi.php';
header('Content-Type: application/json');
$id = mysqli_real_escape_string($koneksi, $_GET['id'] ?? '');
$row = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM data_mahasiswa WHERE id_mahasiswa='$id'"));
if (!$row) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan.']);
    exit;
}
// tambahan: total riwayat & pemeriksaan terakhir
$rq = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total, MAX(waktu) as terakhir FROM data_riwayat WHERE id_mahasiswa='$id'"));
$row['total_riwayat'] = $rq['total'];
$row['terakhir'] = $rq['terakhir'];
echo json_encode(['status' => 'success', 'message' => 'OK', 'data' => $row]);