<?php
require_once '../config/koneksi.php';
header('Content-Type: application/json');

$result = mysqli_query($koneksi, "SELECT status, tahap, uid, nama, suhu, spo2, hr, td, pesan, wifi, waktu FROM monitoring_alat WHERE id=1");
$row = mysqli_fetch_assoc($result);

$waktu = $row['waktu'] ?? '';
$timeAgo = $waktu ? time() - strtotime($waktu) : 999;

echo json_encode([
    'status' => $row['status'] ?? 'offline',
    'tahap' => $row['tahap'] ?? '—',
    'uid' => $row['uid'] ?? '',
    'nama' => $row['nama'] ?? '',
    'suhu' => $row['suhu'] ?? '',
    'spo2' => $row['spo2'] ?? '',
    'hr' => $row['hr'] ?? '',
    'td' => $row['td'] ?? '',
    'pesan' => $row['pesan'] ?? '',
    'wifi' => $row['wifi'] ?? '—',
    'waktu' => $waktu,
    'time_ago' => $timeAgo
]);
