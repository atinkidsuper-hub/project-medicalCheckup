<?php
require_once '../config/koneksi.php';
header('Content-Type: application/json');

$result = mysqli_query($koneksi, "SELECT uid, waktu FROM scan_kartu WHERE id=1");
$row = mysqli_fetch_assoc($result);

echo json_encode([
    'uid' => $row['uid'] ?? '',
    'waktu' => $row['waktu'] ?? ''
]);
