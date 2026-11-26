<?php
require_once '../config/koneksi.php';

$iot_key = $_REQUEST['iot_key'] ?? '';
$status = $_REQUEST['status'] ?? '';
$tahap = $_REQUEST['tahap'] ?? '';
$uid = $_REQUEST['uid'] ?? '';
$nama = $_REQUEST['nama'] ?? '';
$suhu = $_REQUEST['suhu'] ?? '';
$spo2 = $_REQUEST['spo2'] ?? '';
$hr = $_REQUEST['hr'] ?? '';
$td = $_REQUEST['td'] ?? '';
$pesan = $_REQUEST['pesan'] ?? '';
$wifi = $_REQUEST['wifi'] ?? '';

if ($iot_key !== 'iot_sehat_2026') {
    die('error_key');
}

$status = mysqli_real_escape_string($koneksi, trim($status));
$tahap = mysqli_real_escape_string($koneksi, trim($tahap));
$uid = mysqli_real_escape_string($koneksi, trim($uid));
$nama = mysqli_real_escape_string($koneksi, trim($nama));
$suhu = mysqli_real_escape_string($koneksi, trim($suhu));
$spo2 = mysqli_real_escape_string($koneksi, trim($spo2));
$hr = mysqli_real_escape_string($koneksi, trim($hr));
$td = mysqli_real_escape_string($koneksi, trim($td));
$pesan = mysqli_real_escape_string($koneksi, trim($pesan));
$wifi = mysqli_real_escape_string($koneksi, trim($wifi));
$waktu = date('Y-m-d H:i:s');

mysqli_query($koneksi, "UPDATE monitoring_alat SET 
    status='$status', tahap='$tahap', uid='$uid', nama='$nama',
    suhu='$suhu', spo2='$spo2', hr='$hr', td='$td',
    pesan='$pesan', wifi='$wifi', waktu='$waktu' WHERE id=1");

if (mysqli_affected_rows($koneksi) > 0) {
    echo 'success';
} else {
    mysqli_query($koneksi, "INSERT INTO monitoring_alat (id, status, tahap, uid, nama, suhu, spo2, hr, td, pesan, wifi, waktu) 
        VALUES (1, '$status', '$tahap', '$uid', '$nama', '$suhu', '$spo2', '$hr', '$td', '$pesan', '$wifi', '$waktu')
        ON DUPLICATE KEY UPDATE status='$status', tahap='$tahap', uid='$uid', nama='$nama',
        suhu='$suhu', spo2='$spo2', hr='$hr', td='$td', pesan='$pesan', wifi='$wifi', waktu='$waktu'");
    echo 'success';
}
