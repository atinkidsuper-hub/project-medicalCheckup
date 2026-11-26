<?php
// FILE: admin/app/page/riwayat/actions/cari.php
require_once '../../../../config/koneksi.php';
header('Content-Type: application/json');
$allowed = ['m.nama_mahasiswa', 'r.suhu_tubuh', 'r.tekanan_darah', 'r.spo2', 'r.detak_jantung'];
$col = in_array($_GET['col'] ?? '', $allowed) ? $_GET['col'] : 'm.nama_mahasiswa';
$kw = mysqli_real_escape_string($koneksi, $_GET['kw'] ?? '');
$result = mysqli_query($koneksi, "SELECT r.*,m.nama_mahasiswa,m.jenis_kelamin,m.tanggal_lahir FROM data_riwayat r LEFT JOIN data_mahasiswa m ON r.id_mahasiswa=m.id_mahasiswa WHERE $col LIKE '%$kw%' ORDER BY r.waktu DESC");
$rows = [];
while ($r = mysqli_fetch_assoc($result))
    $rows[] = $r;
echo json_encode(['status' => 'success', 'data' => $rows]);