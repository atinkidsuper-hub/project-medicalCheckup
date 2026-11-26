<?php
// FILE: admin/app/page/admin/actions/cari.php
header('Content-Type: application/json');
require_once '../../../../../config/koneksi.php';

$allowed = ['nama', 'username'];
$col = in_array($_GET['col'] ?? '', $allowed) ? $_GET['col'] : 'nama';
$kw = '%' . mysqli_real_escape_string($koneksi, trim($_GET['kw'] ?? '')) . '%';

$res = mysqli_query($koneksi, "SELECT * FROM data_admin WHERE $col LIKE '$kw' ORDER BY nama ASC");
$rows = [];
while ($r = mysqli_fetch_assoc($res))
    $rows[] = $r;
echo json_encode(['status' => 'success', 'data' => $rows]);