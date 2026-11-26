<?php
// FILE: admin/app/page/actions/admin/tambah.php
require_once '../../../../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $user = mysqli_real_escape_string($koneksi, $_POST['username']);

    // Kita gunakan password_hash agar aman dan cocok dengan sistem login pintar kita
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

    mysqli_query($koneksi, "INSERT INTO data_admin (nama, username, password) VALUES ('$nama', '$user', '$pass')");

    header("Location: index.php?pesan=tambah");
    exit();
}
?>