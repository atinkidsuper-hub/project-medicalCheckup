<?php
// FILE: admin/app/page/admin/actions/edit.php
header('Content-Type: application/json');
require_once '../../../../../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = intval($_GET['id'] ?? 0);
    $res = mysqli_query($koneksi, "SELECT * FROM data_admin WHERE id_admin=$id");
    $row = mysqli_fetch_assoc($res);
    if ($row) {
        echo json_encode(['status' => 'success', 'data' => $row]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id_admin'] ?? 0);
    $nama = trim($_POST['nama'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!$id || !$nama || !$username) {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
        exit;
    }

    // Cek username duplikat (selain diri sendiri)
    $u = mysqli_real_escape_string($koneksi, $username);
    $cek = mysqli_query($koneksi, "SELECT id_admin FROM data_admin WHERE username='$u' AND id_admin!=$id");
    if (mysqli_num_rows($cek) > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Username sudah digunakan']);
        exit;
    }

    $n = mysqli_real_escape_string($koneksi, $nama);
    $pwSql = $password ? ", password='" . mysqli_real_escape_string($koneksi, $password) . "'" : '';

    $sql = "UPDATE data_admin SET nama='$n', username='$u'$pwSql WHERE id_admin=$id";
    if (mysqli_query($koneksi, $sql)) {
        $row = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM data_admin WHERE id_admin=$id"));
        echo json_encode(['status' => 'success', 'message' => 'Data admin berhasil diperbarui', 'data' => $row]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui: ' . mysqli_error($koneksi)]);
    }
    exit;
}
echo json_encode(['status' => 'error', 'message' => 'Method tidak valid']);