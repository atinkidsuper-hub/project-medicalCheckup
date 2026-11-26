<?php
// FILE: admin/app/page/mahasiswa/actions/cari.php
require_once '../../../../config/koneksi.php';
header('Content-Type: application/json');
$allowed = ['nama_mahasiswa', 'jenis_kelamin', 'tanggal_lahir'];
$col = in_array($_GET['col'] ?? '', $allowed) ? $_GET['col'] : 'nama_mahasiswa';
$kw = mysqli_real_escape_string($koneksi, $_GET['kw'] ?? '');
$result = mysqli_query($koneksi, "SELECT * FROM data_mahasiswa WHERE $col LIKE '%$kw%' ORDER BY nama_mahasiswa ASC");
$rows = [];
while ($r = mysqli_fetch_assoc($result))
    $rows[] = $r;
echo json_encode(['status' => 'success', 'data' => $rows]);