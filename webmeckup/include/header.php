<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}
$pageTitle = $pageTitle ?? 'Dashboard';
$script = $_SERVER['SCRIPT_NAME'] ?? '';
if (strpos($script, '/dashboard/') !== false) {
    $page = 'dashboard';
} elseif (strpos($script, '/laporan/riwayat/') !== false) {
    $page = 'laporan_riwayat';
} elseif (strpos($script, '/riwayat/') !== false) {
    $page = 'riwayat';
} elseif (strpos($script, '/monitoring/') !== false) {
    $page = 'monitoring';
} elseif (strpos($script, '/mahasiswa/') !== false) {
    $page = 'mahasiswa';
} elseif (strpos($script, '/admin/') !== false) {
    $page = 'admin';
} else {
    $page = '';
}
$nama = $_SESSION['admin_nama'] ?? 'Admin';
$inicial = strtoupper(substr($nama, 0, 2));
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= $pageTitle ?> — Smart Healthy Checkup IoT</title>
    <link rel="icon"
        href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath fill='%233B5BDB' d='M8 14s-4-3-6-5.5A3.5 3.5 0 0 1 8 4.5 3.5 3.5 0 0 1 14 8.5C12 11 8 14 8 14z'/%3E%3C/svg%3E">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --blue: #3B5BDB;
            --green: #2F9E44;
            --red: #E03131;
            --yellow: #E67700;
            --cyan: #1971C2;
            --indigo: #4263EB;
            --purple: #7048E8;
            --light: #F4F6FB;
            --border: #E9ECEF;
            --gray: #868E96;
            --mid: #495057;
            --dark: #1A1B2E;
            --sidebar-w: 260px;
            --font: 'Plus Jakarta Sans', sans-serif;
            --mono: 'JetBrains Mono', monospace;
            --r: 12px;
            --shadow: 0 4px 20px rgba(0, 0, 0, .06);
        }

        html,
        body {
            height: 100%;
            overflow: hidden;
        }

        body {
            font-family: var(--font);
            background: var(--light);
            color: var(--dark);
            display: flex;
        }

        /* ─── OVERLAY ─── */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(10, 15, 30, .5);
            backdrop-filter: blur(2px);
            z-index: 98;
            opacity: 0;
            transition: opacity .3s ease;
        }

        .sidebar-overlay.on {
            display: block;
            opacity: 1;
        }

        /* ─── SIDEBAR ─── */
        .sidebar {
            width: var(--sidebar-w);
            min-width: var(--sidebar-w);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            background: linear-gradient(180deg, #1A1B4B 0%, #2B3A8E 100%);
            display: flex;
            flex-direction: column;
            z-index: 99;
            transition: transform .35s cubic-bezier(.4, 0, .2, 1);
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar-brand {
            padding: 20px 18px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, .08);
            flex-shrink: 0;
        }

        .brand-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: linear-gradient(135deg, #3B5BDB, #1971C2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.2rem;
            flex-shrink: 0;
            animation: brandPulse 3s ease-in-out infinite;
        }

        @keyframes brandPulse {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(59, 91, 219, .4);
            }

            50% {
                box-shadow: 0 0 0 8px rgba(59, 91, 219, 0);
            }
        }

        .brand-text h3 {
            color: #fff;
            font-size: .92rem;
            font-weight: 800;
            line-height: 1.2;
        }

        .brand-text p {
            color: rgba(255, 255, 255, .45);
            font-size: .7rem;
        }

        .sidebar-menu {
            padding: 12px 10px;
            flex: 1;
        }

        .menu-label {
            color: rgba(255, 255, 255, .3);
            font-size: .66rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 0 10px;
            margin: 16px 0 6px;
        }

        .menu-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 10px;
            color: rgba(255, 255, 255, .6);
            font-size: .85rem;
            font-weight: 600;
            text-decoration: none;
            transition: all .2s;
            margin-bottom: 2px;
            position: relative;
            overflow: hidden;
        }

        .menu-item i {
            font-size: 1.05rem;
            width: 20px;
            text-align: center;
            flex-shrink: 0;
        }

        .menu-item:hover {
            background: rgba(255, 255, 255, .08);
            color: #fff;
            transform: translateX(3px);
        }

        .menu-item.active {
            background: linear-gradient(135deg, #3B5BDB, #1971C2);
            color: #fff;
            box-shadow: 0 4px 16px rgba(59, 91, 219, .35);
        }

        .menu-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 20px;
            border-radius: 0 3px 3px 0;
            background: #fff;
        }

        /* ─── (Disembunyikan sementara) sidebar-user / info admin bawah ───
    .sidebar-user {
    padding: 14px 16px;
    border-top: 1px solid rgba(255, 255, 255, .08);
    display: flex;
    align-items: center;
    gap: 10px;
    flex-shrink: 0;
    }

    .user-av {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: linear-gradient(135deg, #3B5BDB, #1971C2);
    color: #fff;
    font-weight: 700;
    font-size: .8rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    }

    .user-info p {
    color: rgba(255, 255, 255, .9);
    font-size: .8rem;
    font-weight: 700;
    }

    .user-info span {
    color: rgba(255, 255, 255, .4);
    font-size: .7rem;
    }
    ─── end sidebar-user ─── */

        /* ─── MAIN ─── */
        .main-wrap {
            margin-left: var(--sidebar-w);
            flex: 1;
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transition: margin-left .35s cubic-bezier(.4, 0, .2, 1);
        }

        .topbar {
            height: 60px;
            background: #fff;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            padding: 0 20px;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .03);
            gap: 12px;
        }

        .hamburger {
            display: flex;
            width: 36px;
            height: 36px;
            align-items: center;
            justify-content: center;
            background: var(--light);
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 1.2rem;
            color: var(--dark);
            transition: background .2s;
            flex-shrink: 0;
        }

        .hamburger:hover {
            background: #E9ECEF;
        }

        .topbar .breadcrumb {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: .84rem;
            color: var(--gray);
            flex: 1;
        }

        .topbar .breadcrumb a {
            color: var(--blue);
            text-decoration: none;
            font-weight: 600;
            transition: color .2s;
        }

        .topbar .breadcrumb a:hover {
            color: #1a237e;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        .topbar-user {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 4px 12px 4px 4px;
            border-radius: 50px;
            background: var(--light);
            font-size: .82rem;
            font-weight: 600;
        }

        .topbar-user .mini-av {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3B5BDB, #1971C2);
            color: #fff;
            font-size: .65rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logout-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            border-radius: var(--r);
            border: none;
            cursor: pointer;
            background: #FFF5F5;
            color: #C92A2A;
            font-family: var(--font);
            font-size: .82rem;
            font-weight: 600;
            transition: all .2s;
            text-decoration: none;
        }

        .logout-btn:hover {
            background: #FFE3E3;
            transform: translateY(-1px);
        }

        .page-content {
            flex: 1;
            overflow-y: auto;
            padding: 24px;
            background: var(--light);
        }

        /* ─── COMMON ─── */
        .page-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 20px;
        }

        .page-top h2 {
            margin: 0;
            font-size: 1.4rem;
            font-weight: 800;
        }

        .page-top p {
            margin: 0;
            color: var(--gray);
            font-size: .85rem;
        }

        .page-top-act {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            align-items: center;
        }

        .chip {
            background: var(--blue);
            color: #fff;
            padding: 6px 14px;
            border-radius: 50px;
            font-weight: 700;
            font-size: .82rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 8px 16px;
            border-radius: var(--r);
            font-family: var(--font);
            font-weight: 600;
            font-size: .84rem;
            border: none;
            cursor: pointer;
            transition: all .2s;
            text-decoration: none;
            white-space: nowrap;
            line-height: 1.2;
        }

        .btn:hover {
            filter: brightness(1.08);
            transform: translateY(-1px);
        }

        .btn:active {
            transform: none;
        }

        .btn-blue {
            background: var(--blue);
            color: #fff;
        }

        .btn-green {
            background: var(--green);
            color: #fff;
        }

        .btn-red {
            background: var(--red);
            color: #fff;
        }

        .btn-yellow {
            background: var(--yellow);
            color: #fff;
        }

        .btn-cyan {
            background: var(--cyan);
            color: #fff;
        }

        .btn-purple {
            background: var(--purple);
            color: #fff;
        }

        .btn-outline {
            background: #fff;
            color: var(--blue);
            border: 2px solid var(--blue);
        }

        .btn-sm {
            padding: 5px 11px;
            font-size: .79rem;
        }

        .card {
            background: #fff;
            border-radius: var(--r);
            box-shadow: var(--shadow);
            transition: transform .2s, box-shadow .2s;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, .08);
        }

        .card-body {
            padding: 18px;
        }

        .avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3B5BDB, #1971C2);
            color: #fff;
            font-weight: 700;
            font-size: .75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .name-cell {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: nowrap;
        }

        .name-cell span {
            font-weight: 600;
            white-space: nowrap;
        }

        .uid {
            font-family: var(--mono);
            font-size: .78rem;
            background: #F1F3F5;
            padding: 2px 10px;
            border-radius: 50px;
            display: inline-block;
            white-space: nowrap;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 10px;
            border-radius: 50px;
            font-size: .76rem;
            font-weight: 700;
        }

        .badge-l {
            background: #DBEAFE;
            color: #1E40AF;
        }

        .badge-p {
            background: #FCE7F3;
            color: #9D174D;
        }

        .badge-ok {
            background: #D3F9D8;
            color: #1B5E20;
        }

        .badge-warn {
            background: #FFF3CD;
            color: #7B4F00;
        }

        .badge-danger {
            background: #FFE0E0;
            color: #7F1D1D;
        }

        .act {
            display: flex;
            gap: 4px;
            flex-wrap: nowrap;
        }

        .empty {
            text-align: center;
            padding: 40px 20px;
            color: var(--gray);
        }

        .empty i {
            font-size: 2.5rem;
            display: block;
            margin-bottom: 8px;
            opacity: .35;
        }

        /* ─── TOAST ─── */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 99999;
            display: flex;
            flex-direction: column;
            gap: 8px;
            pointer-events: none;
        }

        .toast {
            pointer-events: auto;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 14px 18px;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, .12);
            max-width: 400px;
            font-size: .85rem;
            font-weight: 600;
            animation: toastIn .35s cubic-bezier(.34, 1.56, .64, 1) forwards;
        }

        .toast-success {
            background: #EBFBEE;
            color: #1B5E20;
            border-left: 4px solid #2F9E44;
        }

        .toast-error {
            background: #FFF0F0;
            color: #7F1D1D;
            border-left: 4px solid #E03131;
        }

        .toast .toast-icon {
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .toast .toast-close {
            margin-left: auto;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.1rem;
            opacity: .4;
            padding: 0 2px;
            line-height: 1;
            transition: opacity .2s;
            color: inherit;
        }

        .toast .toast-close:hover {
            opacity: .8;
        }

        @keyframes toastIn {
            from {
                opacity: 0;
                transform: translateX(100%);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes toastOut {
            from {
                opacity: 1;
                transform: translateX(0);
            }

            to {
                opacity: 0;
                transform: translateX(100%);
            }
        }

        /* ─── LOADING ─── */
        .btn.loading {
            pointer-events: none;
            opacity: .75;
        }

        @keyframes spin {
            100% {
                transform: rotate(360deg);
            }
        }

        .btn.loading i {
            animation: spin .8s linear infinite;
        }

        /* ─── ZEBRA ─── */
        table.dataTable tbody tr:nth-child(even):not(.dtrg-group) {
            background: #F8F9FB;
        }

        table.dataTable tbody tr:hover {
            background: #EEF2FF;
        }

        /* ─── DATA TABLES ─── */
        .card .dataTables_wrapper {
            padding: 16px 18px 4px;
        }

        .dataTables_wrapper .dataTables_filter {
            float: right;
            margin-bottom: 10px;
        }

        .dataTables_wrapper .dataTables_filter label {
            font-size: .84rem;
            color: var(--mid);
            font-weight: 600;
        }

        .dataTables_wrapper .dataTables_filter input {
            border: 2px solid var(--border);
            border-radius: 8px;
            padding: 6px 12px;
            font-family: var(--font);
            font-size: .85rem;
            outline: none;
            margin-left: 6px;
            min-width: 180px;
        }

        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: var(--blue);
        }

        .dataTables_wrapper .dataTables_length {
            float: left;
            margin-bottom: 10px;
        }

        .dataTables_wrapper .dataTables_length label {
            font-size: .84rem;
            color: var(--mid);
            font-weight: 600;
        }

        .dataTables_wrapper .dataTables_length select {
            border: 2px solid var(--border);
            border-radius: 8px;
            padding: 4px 8px;
            font-family: var(--font);
            outline: none;
            margin: 0 4px;
        }

        .dataTables_wrapper table.dataTable {
            clear: both;
            margin: 0 !important;
        }

        .dataTables_wrapper .dataTables_info {
            float: left;
            clear: left;
            font-size: .82rem;
            color: var(--gray);
            padding: 12px 0;
        }

        .dataTables_wrapper .dataTables_paginate {
            float: right;
            padding: 10px 0;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 5px 13px;
            border-radius: 8px;
            border: 1px solid var(--border);
            margin: 0 2px;
            cursor: pointer;
            display: inline-block;
            transition: all .15s;
            font-size: .82rem;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #EEF2FF;
            border-color: #CBD5E1;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--blue);
            color: #fff !important;
            border-color: var(--blue);
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
            opacity: .35;
            cursor: default;
            pointer-events: none;
        }

        table.dataTable thead tr {
            background: linear-gradient(135deg, #3B5BDB, #1971C2);
            color: #fff;
        }

        table.dataTable thead th {
            padding: 11px 14px;
            font-size: .73rem;
            text-transform: uppercase;
            letter-spacing: .5px;
            font-weight: 700;
            color: #fff;
            border-bottom: none !important;
        }

        table.dataTable thead th.sorting:after,
        table.dataTable thead th.sorting_asc:after,
        table.dataTable thead th.sorting_desc:after {
            color: rgba(255, 255, 255, .6) !important;
            opacity: .7;
        }

        table.dataTable tbody td {
            padding: 9px 14px;
            font-size: .85rem;
            vertical-align: middle;
        }

        table.dataTable tbody tr {
            border-bottom: 1px solid #EDF2F7;
            transition: background .1s;
        }

        table.dataTable tbody tr:hover {
            background: #F4F6FB;
        }

        table.dataTable.no-footer {
            border-bottom: 1px solid #EDF2F7 !important;
        }

        table.dataTable {
            width: 100% !important;
            margin: 0 !important;
        }

        table.dataTable.dtr-inline.collapsed>tbody>tr>td.dtr-control:before {
            background: var(--blue) !important;
            border-color: var(--blue) !important;
        }

        .dataTables_wrapper .dataTables_filter input,
        .dataTables_wrapper .dataTables_length select {
            background: #fff;
        }

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, .05);
            padding: 20px 20px 18px;
            display: flex;
            align-items: center;
            gap: 16px;
            transition: transform .25s cubic-bezier(.4, 0, .2, 1), box-shadow .25s;
            cursor: default;
            position: relative;
            overflow: hidden;
        }

        .stat-card::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: var(--c, rgba(59, 91, 219, .06));
            transform: translate(30%, -30%);
            transition: transform .4s;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(0, 0, 0, .08);
        }

        .stat-card:hover::after {
            transform: translate(20%, -20%) scale(1.3);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            flex-shrink: 0;
        }

        .stat-info {
            flex: 1;
            min-width: 0;
        }

        .stat-num {
            font-size: 1.7rem;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 3px;
        }

        .stat-label {
            font-size: .76rem;
            color: #868e96;
            font-weight: 600;
        }

        .stat-icon.blue {
            background: linear-gradient(135deg, #3B5BDB, #1971C2);
            color: #fff;
        }

        .stat-icon.green {
            background: linear-gradient(135deg, #2F9E44, #1B7A32);
            color: #fff;
        }

        .stat-icon.yellow {
            background: linear-gradient(135deg, #E67700, #D45F00);
            color: #fff;
        }

        .stat-icon.red {
            background: linear-gradient(135deg, #E03131, #B02020);
            color: #fff;
        }

        .stat-icon.purple {
            background: linear-gradient(135deg, #7048E8, #5236B0);
            color: #fff;
        }

        /* ─── POPUP ─── */
        .overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(10, 15, 30, .5);
            backdrop-filter: blur(4px);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }

        .overlay.on {
            display: flex;
        }

        .popup {
            background: #fff;
            border-radius: 18px;
            width: 100%;
            max-width: 560px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, .2);
            animation: popIn .25s cubic-bezier(.34, 1.56, .64, 1) forwards;
        }

        .popup-sm {
            max-width: 420px;
        }

        .popup-lg {
            max-width: 700px;
        }

        @keyframes popIn {
            from {
                opacity: 0;
                transform: scale(.9) translateY(14px);
            }

            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .popup-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            padding: 18px 22px 12px;
            border-bottom: 1px solid var(--border);
        }

        .popup-head h5 {
            margin: 0 0 2px;
            font-weight: 800;
            font-size: 1rem;
        }

        .popup-head p {
            margin: 0;
            color: var(--gray);
            font-size: .82rem;
        }

        .close-btn {
            background: #F1F3F5;
            border: none;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            cursor: pointer;
            font-size: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background .2s;
        }

        .close-btn:hover {
            background: #E9ECEF;
        }

        .popup-body {
            padding: 16px 22px;
        }

        .popup-foot {
            padding: 8px 22px 18px;
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }

        .row2 {
            display: flex;
            gap: 12px;
        }

        .row2>* {
            flex: 1;
            min-width: 0;
        }

        .fg {
            margin-bottom: 14px;
        }

        .fg label {
            display: block;
            font-size: .74rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .4px;
            color: #495057;
            margin-bottom: 4px;
        }

        .fg input,
        .fg select,
        .fg textarea {
            width: 100%;
            padding: 9px 12px;
            border: 2px solid var(--border);
            border-radius: var(--r);
            font-family: var(--font);
            font-size: .87rem;
            outline: none;
            transition: border-color .15s;
            color: var(--dark);
            background: #fff;
        }

        .fg input:focus,
        .fg select:focus,
        .fg textarea:focus {
            border-color: var(--blue);
        }

        .fg small {
            font-size: .73rem;
            color: var(--gray);
            margin-top: 3px;
            display: block;
        }

        .notif-head {
            padding: 14px 18px 0;
            display: flex;
            justify-content: flex-end;
        }

        .notif-body {
            padding: 4px 26px 20px;
        }

        .notif-body h3 {
            font-size: 1.5rem;
            font-weight: 800;
            margin: 0 0 4px;
        }

        .notif-body p {
            color: #495057;
            font-weight: 600;
            font-size: .86rem;
            margin: 0 0 14px;
        }

        .notif-btns {
            display: flex;
            gap: 10px;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .di label {
            font-size: .7rem;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: var(--gray);
            font-weight: 700;
            display: block;
            margin-bottom: 2px;
        }

        .di span {
            font-weight: 600;
            font-size: .88rem;
        }

        .big-av {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3B5BDB, #1971C2);
            color: #fff;
            font-size: 1.1rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
        }

        /* ─── SCAN RFID ─── */
        .scan-wrap {
            display: flex;
            gap: 8px;
            align-items: start;
        }

        .scan-wrap input {
            flex: 1;
        }

        .btn-scan {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 16px;
            border: none;
            border-radius: var(--r);
            background: linear-gradient(135deg, #3B5BDB, #1971C2);
            color: #fff;
            font-weight: 600;
            font-family: var(--font);
            cursor: pointer;
            transition: all .25s;
            white-space: nowrap;
            position: relative;
            overflow: hidden;
        }

        .btn-scan:hover {
            filter: brightness(1.1);
            transform: translateY(-1px);
        }

        .btn-scan:disabled {
            background: linear-gradient(135deg, #ADB5BD, #868E96);
            cursor: not-allowed;
            transform: none;
        }

        .btn-scan i {
            font-size: 1.15rem;
        }

        .btn-scan.scanning::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, transparent 30%, rgba(255, 255, 255, .2) 50%, transparent 70%);
            animation: scanGlide 1.2s ease-in-out infinite;
        }

        @keyframes scanGlide {
            0% {
                transform: translateX(-100%);
            }

            100% {
                transform: translateX(100%);
            }
        }

        .btn-scan .pulse-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #fff;
            animation: pulseDot .8s ease-in-out infinite;
        }

        @keyframes pulseDot {

            0%,
            100% {
                opacity: .3;
                transform: scale(.7);
            }

            50% {
                opacity: 1;
                transform: scale(1.1);
            }
        }

        .status-scan {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 6px;
            font-size: .82rem;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 8px;
            transition: all .3s;
        }

        .status-scan.idle {
            color: var(--gray);
            background: #F1F3F5;
        }

        .status-scan.waiting {
            color: #E67700;
            background: #FFF3E0;
        }

        .status-scan.success {
            color: #2F9E44;
            background: #EBFBEE;
        }

        .status-scan.error {
            color: #E03131;
            background: #FFE8E8;
        }

        .status-scan .rfid-pulse {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: currentColor;
            flex-shrink: 0;
        }

        .status-scan.waiting .rfid-pulse {
            animation: rfidPulse .8s ease-in-out infinite;
        }

        @keyframes rfidPulse {

            0%,
            100% {
                opacity: .3;
                transform: scale(.7);
                box-shadow: 0 0 0 0 currentColor;
            }

            50% {
                opacity: 1;
                transform: scale(1.1);
                box-shadow: 0 0 0 6px rgba(230, 119, 0, .2);
            }
        }

        /* ─── RESPONSIVE ─── */
        @media (max-width:768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.on {
                transform: translateX(0);
            }

            .main-wrap {
                margin-left: 0;
            }

            .page-content {
                padding: 16px;
            }

            .row2 {
                flex-direction: column;
            }

            .topbar-user .user-name {
                display: none;
            }

            .stat-grid {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            }

            .detail-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width:480px) {
            .page-top h2 {
                font-size: 1.15rem;
            }

            .act {
                flex-direction: column;
                gap: 3px;
            }

            .page-content {
                padding: 12px;
            }
        }
    </style>
</head>

<body>

    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <!-- <div class="brand-icon"><i class="bi bi-heart-pulse-fill"></i></div> -->
            <img src="../../../../assets/images/meckupimg.jpg" alt="icon" class="stat-card">
            <div class="brand-text">
                <h3>Smart Healthy</h3>
                <p>Checkup IoT</p>
            </div>
        </div>
        <nav class="sidebar-menu">
            <div class="menu-label">Menu Utama</div>
            <a href="/webmeckup/admin/app/page/dashboard/index.php"
                class="menu-item <?= $page === 'dashboard' ? 'active' : '' ?>">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>

            <!-- <div class="menu-label">Master Data</div> -->
            <a href="/webmeckup/admin/app/page/admin/index.php"
                class="menu-item <?= $page === 'admin' ? 'active' : '' ?>">
                <i class="bi bi-shield-lock-fill"></i> Data Admin
            </a>
            <a href="/webmeckup/admin/app/page/mahasiswa/index.php"
                class="menu-item <?= $page === 'mahasiswa' ? 'active' : '' ?>">
                <i class="bi bi-people-fill"></i> Data Mahasiswa
            </a>
            <a href="/webmeckup/admin/app/page/riwayat/index.php"
                class="menu-item <?= $page === 'riwayat' ? 'active' : '' ?>">
                <i class="bi bi-clipboard2-pulse-fill"></i> Data Riwayat
            </a>

            <div class="menu-label">Monitoring</div>
            <a href="/webmeckup/admin/app/page/monitoring/index.php"
                class="menu-item <?= $page === 'monitoring' ? 'active' : '' ?>">
                <i class="bi bi-motherboard-fill"></i> Monitoring Alat
            </a>

            <div class="menu-label">Laporan</div>
            <a href="/webmeckup/admin/app/page/laporan/riwayat/index.php"
                class="menu-item <?= $page === 'laporan_riwayat' ? 'active' : '' ?>">
                <i class="bi bi-file-earmark-bar-graph-fill"></i> Riwayat Kesehatan
            </a>
        </nav>

        <?php /*
<div class="sidebar-user">
<div class="user-av"><?= $inicial ?></div>
<div class="user-info">
<p><?= htmlspecialchars($nama) ?></p>
<span>Administrator</span>
</div>
</div>
*/ ?>
    </div>

    <div class="main-wrap">
        <div class="topbar">
            <button class="hamburger" id="hamburgerBtn" onclick="toggleSidebar()" aria-label="Toggle sidebar">
                <i class="bi bi-list"></i>
            </button>
            <div class="breadcrumb">
                <a href="/admin/app/page/dashboard/index.php"><i class="bi bi-house-fill"></i></a>
                <span>/</span>
                <span id="breadcrumb-current">Halaman</span>
            </div>
            <div class="topbar-right">
                <div class="topbar-user">
                    <div class="mini-av"><?= $inicial ?></div>
                    <span class="user-name"><?= htmlspecialchars($nama) ?></span>
                </div>
                <a href="/webmeckup/logout.php" class="logout-btn">
                    <i class="bi bi-box-arrow-right"></i>
                </a>
            </div>
        </div>
        <div class="page-content">

            <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
            <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
            <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
            <script>
                function toggleSidebar() {
                    document.getElementById('sidebar').classList.toggle('on');
                    document.getElementById('sidebarOverlay').classList.toggle('on');
                }
            </script>