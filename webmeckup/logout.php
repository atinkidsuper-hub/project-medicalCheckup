<?php
// FILE: logout.php

session_start();

// Hapus seluruh data session
$_SESSION = [];
session_destroy();

// Kembali ke halaman login
header("Location: /webmeckup/index.php");
exit;
