<?php
require_once '../config/koneksi.php';

$iot_key = $_REQUEST['iot_key'] ?? '';
$uid = $_REQUEST['uid'] ?? '';

if ($iot_key !== 'iot_sehat_2026') {
    die('error_key');
}

$uid = mysqli_real_escape_string($koneksi, trim($uid));
if ($uid === '') {
    die('error_uid');
}

$waktu = date('Y-m-d H:i:s');

mysqli_query($koneksi, "UPDATE scan_kartu SET uid='$uid', waktu='$waktu' WHERE id=1");

if (mysqli_affected_rows($koneksi) > 0) {
    echo 'success';
} else {
    mysqli_query($koneksi, "INSERT INTO scan_kartu (id, uid, waktu) VALUES (1, '$uid', '$waktu') ON DUPLICATE KEY UPDATE uid='$uid', waktu='$waktu'");
    echo 'success';
}
