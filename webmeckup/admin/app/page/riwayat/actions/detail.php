<?php
// FILE: admin/app/page/riwayat/actions/detail.php
require_once '../../../../config/koneksi.php';
header('Content-Type: application/json');
$id = mysqli_real_escape_string($koneksi, $_GET['id'] ?? '');
$row = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT r.*,m.nama_mahasiswa,m.jenis_kelamin,m.tanggal_lahir FROM data_riwayat r LEFT JOIN data_mahasiswa m ON r.id_mahasiswa=m.id_mahasiswa WHERE r.id_riwayat='$id'"));
echo json_encode($row ? ['status' => 'success', 'message' => 'OK', 'data' => $row] : ['status' => 'error', 'message' => 'Data tidak ditemukan.']);