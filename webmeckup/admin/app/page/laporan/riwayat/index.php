<?php
$pageTitle = 'Laporan Riwayat Kesehatan';
require_once '../../../../../include/header.php';
require_once '../../../../../config/koneksi.php';
$list_mhs = mysqli_query($koneksi, "SELECT id_mahasiswa, nama_mahasiswa, nim FROM data_mahasiswa ORDER BY nama_mahasiswa ASC");
?>
<style>
    .page-title {
        margin-bottom: 24px;
        padding-bottom: 10px;
        border-bottom: 2px solid var(--border);
    }

    .page-title h2 {
        font-size: 1.6rem;
        font-weight: 800;
        color: #1A1B2E;
        margin: 0;
    }

    .report-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 12px;
        margin-bottom: 20px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
    }

    .report-header {
        background: #F8F9FA;
        padding: 14px 20px;
        font-weight: 800;
        font-size: 0.85rem;
        color: #495057;
        text-transform: uppercase;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .report-header i {
        font-size: 1rem;
        color: var(--blue);
    }

    .report-body {
        padding: 20px;
    }

    .form-row {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        gap: 12px;
        flex-wrap: wrap;
    }

    .form-row label {
        min-width: 140px;
        font-weight: 700;
        font-size: 0.83rem;
        color: #495057;
    }

    .form-row .form-control {
        flex: 1;
        min-width: 200px;
        padding: 10px 13px;
        border: 2px solid var(--border);
        border-radius: 10px;
        font-family: var(--font);
        font-size: 0.88rem;
        outline: none;
        color: var(--dark);
        background: #fff;
        transition: border-color .15s;
    }

    .form-row .form-control:focus {
        border-color: var(--blue);
    }

    .form-row .form-control:disabled {
        background: #F1F3F5;
        cursor: not-allowed;
    }

    .action-buttons {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 18px;
        flex-wrap: wrap;
    }

    .btn-action {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 9px 18px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.84rem;
        border: none;
        cursor: pointer;
        transition: filter .2s, transform .15s;
        text-decoration: none;
        font-family: var(--font);
    }

    .btn-action:hover {
        filter: brightness(1.1);
        transform: translateY(-1px);
    }

    .btn-action:active {
        transform: none;
    }

    .btn-preview {
        background: #17A2B8;
        color: #fff;
    }

    .btn-print {
        background: #FFC107;
        color: #212529;
    }

    .btn-excel {
        background: #DC3545;
        color: #fff;
    }

    .badge-method {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 50px;
        font-size: .72rem;
        font-weight: 700;
        margin-left: 8px;
    }

    .badge-method.all {
        background: #D3F9D8;
        color: #1B5E20;
    }

    .badge-method.mhs {
        background: #DBEAFE;
        color: #1E40AF;
    }

    .badge-method.periode {
        background: #FFF3CD;
        color: #7B4F00;
    }

    .badge-method.filter {
        background: #F3E8FF;
        color: #6741D9;
    }
</style>

<script>document.getElementById('breadcrumb-current').textContent = 'Laporan Riwayat Kesehatan'</script>

<div class="page-content">
    <div class="page-title">
        <h2><i class="bi bi-clipboard2-pulse-fill" style="color:var(--blue)"></i> Laporan Riwayat Kesehatan</h2>
    </div>

    <!-- ─── FILTER PER MAHASISWA ─── -->
    <div class="report-card">
        <div class="report-header">
            <i class="bi bi-person-badge-fill"></i> Per Mahasiswa
            <span class="badge-method mhs">Individual</span>
        </div>
        <div class="report-body">
            <form action="cetak.php" method="GET" target="_blank">
                <input type="hidden" name="tipe" value="mhs">
                <div class="form-row">
                    <label>Pilih Mahasiswa *</label>
                    <select name="mhs" class="form-control" required>
                        <option value="">— Pilih Mahasiswa —</option>
                        <?php while ($m = mysqli_fetch_assoc($list_mhs)): ?>
                            <option value="<?= $m['id_mahasiswa'] ?>">
                                <?= htmlspecialchars($m['nama_mahasiswa']) ?> (<?= htmlspecialchars($m['nim']) ?>)
                            </option>
                        <?php endwhile;
                        mysqli_data_seek($list_mhs, 0); ?>
                    </select>
                </div>
                <div class="form-row">
                    <label>Periode (opsional)</label>
                    <input type="date" name="dari" class="form-control" style="min-width:160px;flex:none;">
                    <span style="color:var(--gray)">sampai</span>
                    <input type="date" name="sampai" class="form-control" style="min-width:160px;flex:none;">
                </div>
                <div class="action-buttons">
                    <button type="submit" name="action" value="preview" class="btn-action btn-preview">
                        <i class="bi bi-eye-fill"></i> Preview
                    </button>
                    <button type="submit" name="action" value="print" class="btn-action btn-print">
                        <i class="bi bi-printer-fill"></i> Print
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ─── FILTER PER PERIODE ─── -->
    <div class="report-card">
        <div class="report-header">
            <i class="bi bi-calendar-range-fill"></i> Per Periode
            <span class="badge-method periode">Rentang Tanggal</span>
        </div>
        <div class="report-body">
            <form action="cetak.php" method="GET" target="_blank">
                <input type="hidden" name="tipe" value="periode">
                <div class="form-row">
                    <label>Dari Tanggal *</label>
                    <input type="date" name="dari" class="form-control" required>
                </div>
                <div class="form-row">
                    <label>Sampai Tanggal *</label>
                    <input type="date" name="sampai" class="form-control" required>
                </div>
                <div class="action-buttons">
                    <button type="submit" name="action" value="preview" class="btn-action btn-preview">
                        <i class="bi bi-eye-fill"></i> Preview
                    </button>
                    <button type="submit" name="action" value="print" class="btn-action btn-print">
                        <i class="bi bi-printer-fill"></i> Print
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ─── FILTER KEYWORD ─── -->
    <div class="report-card">
        <div class="report-header">
            <i class="bi bi-search"></i> Cari & Cetak
            <span class="badge-method filter">Filter</span>
        </div>
        <div class="report-body">
            <form action="cetak.php" method="GET" target="_blank">
                <input type="hidden" name="tipe" value="filter">
                <div class="form-row">
                    <label>Berdasarkan</label>
                    <select name="kolom" class="form-control" style="flex:none;min-width:180px;">
                        <option value="m.nama_mahasiswa">Nama Mahasiswa</option>
                        <option value="r.suhu_tubuh">Suhu Tubuh</option>
                        <option value="r.spo2">SpO2</option>
                        <option value="r.detak_jantung">Detak Jantung</option>
                        <option value="r.tekanan_darah">Tekanan Darah</option>
                    </select>
                </div>
                <div class="form-row">
                    <label>Kata Kunci *</label>
                    <input type="text" name="keyword" class="form-control" placeholder="Masukkan kata kunci..."
                        required>
                </div>
                <div class="action-buttons">
                    <button type="submit" name="action" value="preview" class="btn-action btn-preview">
                        <i class="bi bi-eye-fill"></i> Preview
                    </button>
                    <button type="submit" name="action" value="print" class="btn-action btn-print">
                        <i class="bi bi-printer-fill"></i> Print
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ─── SEMUA DATA ─── -->
    <div class="report-card">
        <div class="report-header">
            <i class="bi bi-database-fill"></i> Semua Data
            <span class="badge-method all">Keseluruhan</span>
        </div>
        <div class="report-body">
            <form action="cetak.php" method="GET" target="_blank">
                <input type="hidden" name="tipe" value="semua">
                <p style="color:var(--gray);font-size:.85rem;margin:0 0 12px;">
                    <i class="bi bi-info-circle"></i> Cetak seluruh riwayat pemeriksaan tanpa filter.
                </p>
                <div class="action-buttons">
                    <button type="submit" name="action" value="preview" class="btn-action btn-preview">
                        <i class="bi bi-eye-fill"></i> Preview
                    </button>
                    <button type="submit" name="action" value="print" class="btn-action btn-print">
                        <i class="bi bi-printer-fill"></i> Print
                    </button>
                    <button type="submit" name="action" value="excel" class="btn-action btn-excel">
                        <i class="bi bi-file-earmark-spreadsheet-fill"></i> Export Excel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once '../../../../../include/footer.php'; ?>