<?php
$pageTitle = 'Dashboard';
require_once '../../../../include/header.php';
require_once '../../../../config/koneksi.php';

$adm = (int) mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM data_admin"))[0];
$mhs = (int) mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM data_mahasiswa"))[0];
$riw = (int) mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM data_riwayat"))[0];
$lakilaki = (int) mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM data_mahasiswa WHERE jenis_kelamin='laki-laki'"))[0];
$perempuan = (int) mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM data_mahasiswa WHERE jenis_kelamin='perempuan'"))[0];
$avg = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT AVG(suhu_tubuh) s, AVG(spo2) o, AVG(detak_jantung) d FROM data_riwayat"));

$act = mysqli_query($koneksi, "SELECT DATE(waktu) tgl, COUNT(*) jml FROM data_riwayat GROUP BY DATE(waktu) ORDER BY tgl DESC LIMIT 7");
$dates = [];
$counts = [];
while ($r = mysqli_fetch_assoc($act)) {
    $dates[] = date('d M', strtotime($r['tgl']));
    $counts[] = (int) $r['jml'];
}
$dates = array_reverse($dates);
$counts = array_reverse($counts);
?>
<style>
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

    .welcome-banner {
        background: linear-gradient(135deg, #1A1B4B 0%, #2B3A8E 50%, #3B5BDB 100%);
        border-radius: 16px;
        padding: 32px 36px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
        position: relative;
        overflow: hidden;
        min-height: 140px;
    }

    .welcome-banner::before {
        content: '';
        position: absolute;
        width: 300px;
        height: 300px;
        border-radius: 50%;
        background: rgba(255, 255, 255, .04);
        top: -100px;
        right: -60px;
    }

    .welcome-banner::after {
        content: '';
        position: absolute;
        width: 180px;
        height: 180px;
        border-radius: 50%;
        background: rgba(255, 255, 255, .03);
        bottom: -60px;
        left: 40%;
    }

    .welcome-text h1 {
        color: #fff;
        font-size: 1.35rem;
        font-weight: 800;
        margin: 0 0 4px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .welcome-text h1 i {
        color: rgba(255, 255, 255, .2);
        font-size: 1.6rem;
    }

    .welcome-text p {
        color: rgba(255, 255, 255, .6);
        font-size: .88rem;
        margin: 0;
        font-weight: 500;
    }

    .welcome-img-side {
        width: 150px;
        height: 150px;
        object-fit: contain;
        opacity: .85;
        flex-shrink: 0;
        border-radius: 12px;
        background: rgba(255, 255, 255, .08);
        padding: 12px;
    }

    .chart-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
        gap: 18px;
    }

    .chart-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, .04);
        padding: 18px 18px 12px;
        transition: transform .25s, box-shadow .25s;
    }

    .chart-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 28px rgba(0, 0, 0, .07);
    }

    .chart-header {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 700;
        font-size: .88rem;
        margin-bottom: 14px;
        color: #343a40;
    }

    .chart-header i {
        color: var(--blue, #3B5BDB);
        font-size: 1.05rem;
    }

    .chart-box {
        position: relative;
        height: 200px;
        width: 100%;
    }

    @media (max-width:640px) {
        .welcome-banner {
            flex-direction: column;
            text-align: center;
            padding: 24px 20px;
            min-height: auto;
        }

        .welcome-img-side {
            width: 100px;
            height: 100px;
        }

        .stat-grid {
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        }

        .chart-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.stat-num').forEach(el => {
            const target = parseFloat(el.dataset.target) || 0;
            const dec = target % 1 !== 0 ? 1 : 0;
            const dur = 800; const start = performance.now();
            const step = (ts) => {
                const p = Math.min((ts - start) / dur, 1);
                const ease = 1 - Math.pow(1 - p, 3);
                el.textContent = dec ? (target * ease).toFixed(1) : Math.floor(target * ease);
                if (p < 1) requestAnimationFrame(step);
                else el.textContent = target;
            };
            requestAnimationFrame(step);
        });
    });
</script>

<div class="welcome-banner">
    <div class="welcome-text">
        <h1><i class="bi bi-heart-pulse-fill"></i> Selamat Datang,
            <?= htmlspecialchars($_SESSION['admin_nama'] ?? 'Admin') ?>
        </h1>
        <p>Sistem Monitoring IoT Kesehatan Mahasiswa — Smart Healthy Checkup</p>
    </div>
    <img src="../../../../assets/images/KLINIK_AKPRIND.png" alt="Klinik" class="welcome-img-side">
</div>

<div class="stat-grid">
    <div class="stat-card" style="--c:rgba(59,91,219,.08)">
        <div class="stat-icon blue"><i class="bi bi-people-fill"></i></div>
        <div class="stat-info">
            <div class="stat-num" data-target="<?= $mhs ?>">0</div>
            <div class="stat-label">Total Mahasiswa</div>
        </div>
    </div>
    <div class="stat-card" style="--c:rgba(112,72,232,.08)">
        <div class="stat-icon purple"><i class="bi bi-shield-lock-fill"></i></div>
        <div class="stat-info">
            <div class="stat-num" data-target="<?= $adm ?>">0</div>
            <div class="stat-label">Total Admin</div>
        </div>
    </div>
    <div class="stat-card" style="--c:rgba(47,158,68,.08)">
        <div class="stat-icon green"><i class="bi bi-clipboard2-pulse-fill"></i></div>
        <div class="stat-info">
            <div class="stat-num" data-target="<?= $riw ?>">0</div>
            <div class="stat-label">Riwayat Pemeriksaan</div>
        </div>
    </div>
    <div class="stat-card" style="--c:rgba(230,119,0,.08)">
        <div class="stat-icon yellow"><i class="bi bi-gender-male"></i></div>
        <div class="stat-info">
            <div class="stat-num" data-target="<?= $lakilaki ?>">0</div>
            <div class="stat-label">Laki-laki</div>
        </div>
    </div>
    <div class="stat-card" style="--c:rgba(224,49,49,.08)">
        <div class="stat-icon red"><i class="bi bi-gender-female"></i></div>
        <div class="stat-info">
            <div class="stat-num" data-target="<?= $perempuan ?>">0</div>
            <div class="stat-label">Perempuan</div>
        </div>
    </div>
</div>

<div class="chart-grid">
    <div class="chart-card">
        <div class="chart-header"><i class="bi bi-people-fill"></i> Komposisi Pengguna</div>
        <div class="chart-box"><canvas id="chartUser"></canvas></div>
    </div>
    <div class="chart-card">
        <div class="chart-header"><i class="bi bi-gender-ambiguous"></i> Distribusi Gender</div>
        <div class="chart-box"><canvas id="chartGender"></canvas></div>
    </div>
    <div class="chart-card">
        <div class="chart-header"><i class="bi bi-heart-pulse-fill"></i> Rata-rata Tanda Vital</div>
        <div class="chart-box"><canvas id="chartVital"></canvas></div>
    </div>
    <div class="chart-card">
        <div class="chart-header"><i class="bi bi-graph-up"></i> Aktivitas Pemeriksaan (7 Hari)</div>
        <div class="chart-box"><canvas id="chartActivity"></canvas></div>
    </div>
</div>

<?php include 'charts.php'; ?>
<?php require_once '../../../../include/footer.php'; ?>