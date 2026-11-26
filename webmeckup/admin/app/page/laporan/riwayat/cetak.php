<?php
require_once '../../../../../config/koneksi.php';

$tipe = $_GET['tipe'] ?? 'semua';
$action = $_GET['action'] ?? 'preview';

// ─── FORMAT TANGGAL & JAM ───
function fmtTglLap($d)
{
    $bln = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    if (!$d || $d === '0000-00-00')
        return '-';
    $t = strtotime($d);
    return date('d', $t) . ' ' . $bln[(int) date('m', $t)] . ' ' . date('Y', $t);
}

function fmtJamLap($d)
{
    if (!$d || $d === '0000-00-00 00:00:00')
        return '-';
    return date('H:i', strtotime($d));
}

// ─── BANGUN WHERE ───
$where = "1=1";
$judul_filter = 'Semua Data';
$info_mahasiswa = null;

if ($tipe === 'mhs' && !empty($_GET['mhs'])) {
    $mhs = mysqli_real_escape_string($koneksi, $_GET['mhs']);
    $where .= " AND r.id_mahasiswa = '$mhs'";

    $q = mysqli_fetch_assoc(mysqli_query(
        $koneksi,
        "SELECT nama_mahasiswa, nim, tanggal_lahir, usia, program_studi, fakultas, jenis_kelamin
         FROM data_mahasiswa WHERE id_mahasiswa='$mhs'"
    ));
    $judul_filter = 'Mahasiswa: ' . ($q['nama_mahasiswa'] ?? '-') . ' (' . ($q['nim'] ?? '-') . ')';
    $info_mahasiswa = $q;

    if (!empty($_GET['dari']) && !empty($_GET['sampai'])) {
        $dari = mysqli_real_escape_string($koneksi, $_GET['dari']);
        $sampai = mysqli_real_escape_string($koneksi, $_GET['sampai']);
        $where .= " AND DATE(r.waktu) BETWEEN '$dari' AND '$sampai'";
    }
} elseif ($tipe === 'periode') {
    $dari = mysqli_real_escape_string($koneksi, $_GET['dari']);
    $sampai = mysqli_real_escape_string($koneksi, $_GET['sampai']);
    $where .= " AND DATE(r.waktu) BETWEEN '$dari' AND '$sampai'";
    $judul_filter = 'Periode: ' . fmtTglLap($dari) . ' s.d. ' . fmtTglLap($sampai);
}

// ─── QUERY DATA ───
$query = "SELECT r.*, m.nama_mahasiswa, m.nim
          FROM data_riwayat r
          LEFT JOIN data_mahasiswa m ON r.id_mahasiswa = m.id_mahasiswa
          WHERE $where
          ORDER BY r.waktu DESC";
$result = mysqli_query($koneksi, $query);
$rows = [];
while ($r = mysqli_fetch_assoc($result))
    $rows[] = $r;
$total = count($rows);

// Pemeriksaan terbaru (baris pertama karena ORDER BY DESC)
$latest = $rows[0] ?? null;

$tampilkanIdentitas = ($tipe === 'mhs' && !empty($info_mahasiswa['nama_mahasiswa']));
$colspanKosong = $tampilkanIdentitas ? 6 : 7;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Riwayat Kesehatan Mahasiswa</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Times+New+Roman&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 13pt;
            color: #111;
            background: #fff;
            padding: 50px 70px 60px 70px;
            line-height: 1.6;
        }

        /* ── KOP SURAT ── */
        .kop {
            display: flex;
            align-items: center;
            gap: 18px;
            padding-bottom: 14px;
            border-bottom: 3px solid #111;
            margin-bottom: 4px;
        }

        .kop-logo {
            width: 70px;
            height: 70px;
            border: 2px solid #111;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28pt;
            font-weight: bold;
            flex-shrink: 0;
        }

        .kop-text {
            flex: 1;
        }

        .kop-text .instansi {
            font-size: 18pt;
            font-weight: bold;
            letter-spacing: 1px;
            line-height: 1.1;
        }

        .kop-text .sub-instansi {
            font-size: 12pt;
            font-style: italic;
            color: #333;
        }

        .kop-text .alamat {
            font-size: 10.5pt;
            color: #444;
            margin-top: 2px;
        }

        .kop-divider {
            border: none;
            border-top: 1.5px solid #111;
            margin-bottom: 22px;
        }

        /* ── JUDUL DOKUMEN ── */
        .judul-surat {
            text-align: center;
            margin-bottom: 20px;
        }

        .judul-surat h2 {
            font-size: 14pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        .judul-surat .nomor {
            font-size: 11pt;
            color: #333;
            margin-top: 3px;
        }

        /* ── PEMBUKA ── */
        .pembuka {
            margin-bottom: 18px;
            font-size: 13pt;
            text-align: justify;
        }

        /* ── IDENTITAS DIRI ── */
        .section-heading {
            font-size: 13pt;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 10px;
            margin-top: 20px;
            text-transform: uppercase;
        }

        .identitas-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0 20px;
            margin-bottom: 6px;
        }

        .id-row {
            display: flex;
            align-items: flex-start;
            margin-bottom: 3px;
            font-size: 12.5pt;
        }

        .id-label {
            width: 160px;
            flex-shrink: 0;
        }

        .id-colon {
            width: 14px;
            flex-shrink: 0;
        }

        .id-value {
            flex: 1;
        }

        /* ── PEMERIKSAAN TERBARU (highlight box) ── */
        .highlight-section {
            margin: 24px 0 10px 0;
        }

        .highlight-label {
            font-size: 10.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #444;
            margin-bottom: 10px;
        }

        .parameter-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 8px;
        }

        .param-card {
            border: 1.5px solid #111;
            border-radius: 6px;
            text-align: center;
            padding: 14px 10px 12px;
        }

        .param-card .param-icon {
            font-size: 18pt;
            margin-bottom: 4px;
        }

        .param-card .param-name {
            font-size: 9.5pt;
            color: #555;
            font-style: italic;
            margin-bottom: 6px;
        }

        .param-card .param-value {
            font-size: 18pt;
            font-weight: bold;
            line-height: 1;
        }

        .param-card .param-unit {
            font-size: 9.5pt;
            color: #333;
            margin-top: 3px;
        }

        .param-card .param-time {
            font-size: 9pt;
            color: #777;
            margin-top: 6px;
            border-top: 1px solid #ddd;
            padding-top: 4px;
        }

        /* ── TABEL RIWAYAT ── */
        .riwayat-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11.5pt;
            margin-top: 14px;
            margin-bottom: 10px;
        }

        .riwayat-table th {
            border: 1.5px solid #111;
            padding: 7px 9px;
            text-align: center;
            font-weight: bold;
            background: #f0f0f0;
        }

        .riwayat-table td {
            border: 1px solid #555;
            padding: 6px 9px;
            text-align: center;
            vertical-align: middle;
        }

        .riwayat-table td.td-left {
            text-align: left;
        }

        .riwayat-table tbody tr:nth-child(even) {
            background: #fafafa;
        }

        /* ── KETERANGAN ── */
        .keterangan {
            font-size: 10.5pt;
            color: #444;
            margin-top: 8px;
            margin-bottom: 28px;
            line-height: 1.8;
        }

        .keterangan strong {
            color: #111;
        }

        /* ── PENUTUP ── */
        .penutup {
            margin-bottom: 30px;
            font-size: 12.5pt;
            text-align: justify;
        }

        /* ── TANDA TANGAN ── */
        .ttd-wrapper {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }

        .ttd-box {
            width: 40%;
            text-align: center;
            font-size: 12.5pt;
        }

        .ttd-box .ttd-kota {
            margin-bottom: 4px;
        }

        .ttd-space {
            height: 70px;
        }

        .ttd-box .ttd-nama {
            font-weight: bold;
            text-decoration: underline;
        }

        .ttd-box .ttd-nip {
            font-size: 11pt;
            color: #444;
        }

        /* ── PRINT BUTTON ── */
        .no-print {
            text-align: center;
            margin-bottom: 28px;
        }

        .no-print button {
            padding: 9px 22px;
            font-size: 12pt;
            font-weight: bold;
            cursor: pointer;
            background: #1a1a1a;
            color: #fff;
            border: none;
            border-radius: 5px;
            margin: 0 5px;
            font-family: Arial, sans-serif;
        }

        .no-print button.btn-close {
            background: #64748b;
        }

        .no-print button:hover {
            opacity: 0.85;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                padding: 30px 55px 40px 55px;
            }

            .param-card {
                border: 1.5px solid #111 !important;
            }

            .riwayat-table th {
                background: #f0f0f0 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .riwayat-table tbody tr:nth-child(even) {
                background: #fafafa !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>

    <div class="no-print">
        <button onclick="window.print()">&#128438; Cetak / Simpan PDF</button>
        <button class="btn-close" onclick="window.close()">Tutup</button>
    </div>

    <!-- ══ KOP SURAT ══ -->
    <div class="kop">
        <div class="kop-logo">✙</div>
        <div class="kop-text">
            <div class="instansi">KLINIK KAMPUS</div>
            <div class="sub-instansi">Universitas AKPRIND Indonesia</div>
            <!-- <div class="alamat">Jl. Kalisahak No.28, Komplek Balapan, Yogyakarta 55222 &nbsp;|&nbsp; Telp. (0274) 563029 -->
            <!-- &nbsp;|&nbsp; klinik@akprind.ac.id -->
        </div>
    </div>
    </div>
    <hr class="kop-divider">

    <!-- ══ JUDUL ══ -->
    <div class="judul-surat">
        <h2>Laporan Riwayat Kesehatan Mahasiswa</h2>
        <div class="nomor">No../SKS/../<?= date('Y') ?><? str_pad($total, 4, '0', STR_PAD_LEFT) ?>
            &nbsp;&mdash;&nbsp; </div>
        <!-- <div class=Tanggal Cetak: <?= fmtTglLap(date('Y-m-d')) ?>, Pukul <?= date('H:i') ?> WIB</div> -->
    </div>

    <!-- ══ PEMBUKA ══ -->
    <p class="pembuka">
        Berikut ini disampaikan laporan hasil pemeriksaan kesehatan berkala yang tercatat secara otomatis melalui
        sistem perangkat monitoring kesehatan (<em>Smart Healthy Checkup IoT</em>) milik Klinik Kampus
        Universitas AKPRIND Indonesia, dengan rincian sebagai berikut:
    </p>

    <!-- ══ IDENTITAS DIRI ══ -->
    <div class="section-heading">Identitas Diri</div>

    <?php if ($tampilkanIdentitas): ?>
        <div class="identitas-grid">
            <div>
                <div class="id-row">
                    <span class="id-label">Nama Mahasiswa</span>
                    <span class="id-colon">:</span>
                    <span class="id-value"><?= htmlspecialchars($info_mahasiswa['nama_mahasiswa'] ?? '-') ?></span>
                </div>
                <div class="id-row">
                    <span class="id-label">NIM</span>
                    <span class="id-colon">:</span>
                    <span class="id-value"><?= htmlspecialchars($info_mahasiswa['nim'] ?? '-') ?></span>
                </div>
                <div class="id-row">
                    <span class="id-label">Jenis Kelamin</span>
                    <span class="id-colon">:</span>
                    <span class="id-value"><?= ucwords(htmlspecialchars($info_mahasiswa['jenis_kelamin'] ?? '-')) ?></span>
                </div>
                <div class="id-row">
                    <span class="id-label">Tanggal Lahir</span>
                    <span class="id-colon">:</span>
                    <span class="id-value"><?= fmtTglLap($info_mahasiswa['tanggal_lahir'] ?? '') ?></span>
                </div>
            </div>
            <div>
                <div class="id-row">
                    <span class="id-label">Usia</span>
                    <span class="id-colon">:</span>
                    <span
                        class="id-value"><?= !empty($info_mahasiswa['usia']) ? htmlspecialchars($info_mahasiswa['usia']) . ' Tahun' : '-' ?></span>
                </div>
                <div class="id-row">
                    <span class="id-label">Fakultas</span>
                    <span class="id-colon">:</span>
                    <span class="id-value"><?= htmlspecialchars($info_mahasiswa['fakultas'] ?? '-') ?></span>
                </div>
                <div class="id-row">
                    <span class="id-label">Program Studi</span>
                    <span class="id-colon">:</span>
                    <span class="id-value"><?= htmlspecialchars($info_mahasiswa['program_studi'] ?? '-') ?></span>
                </div>
                <div class="id-row">
                    <span class="id-label">Tanggal Cetak</span>
                    <span class="id-colon">:</span>
                    <span class="id-value"><?= fmtTglLap(date('Y-m-d')) ?></span>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div style="margin-bottom:10px;">
            <div class="id-row">
                <span class="id-label">Kategori Laporan</span>
                <span class="id-colon">:</span>
                <span class="id-value">Rekapitulasi Kolektif — <?= htmlspecialchars($judul_filter) ?></span>
            </div>
            <div class="id-row">
                <span class="id-label">Tanggal Cetak</span>
                <span class="id-colon">:</span>
                <span class="id-value"><?= fmtTglLap(date('Y-m-d')) ?></span>
            </div>
        </div>
    <?php endif; ?>


    <!-- ══ HASIL PEMERIKSAAN TERBARU ══ -->
    <?php if ($latest): ?>
        <div class="section-heading" style="margin-top:24px;">Hasil Pemeriksaan Terbaru</div>
        <div class="highlight-label">
            Pemeriksaan pada: <?= fmtTglLap($latest['waktu']) ?>, Pukul <?= fmtJamLap($latest['waktu']) ?> WIB
        </div>

        <div class="parameter-grid">
            <!-- Suhu Tubuh -->
            <div class="param-card">
                <div class="param-icon">🌡</div>
                <div class="param-name">Suhu Tubuh</div>
                <div class="param-value">
                    <?= !empty($latest['suhu_tubuh']) ? number_format((float) $latest['suhu_tubuh'], 1) : '-' ?>
                </div>
                <div class="param-unit">°C</div>
                <div class="param-time">Normal: 36,1 – 37,2 °C</div>
            </div>

            <!-- SpO2 -->
            <div class="param-card">
                <div class="param-icon">🩸</div>
                <div class="param-name">Saturasi Oksigen (SpO₂)</div>
                <div class="param-value"><?= $latest['spo2'] ?? '-' ?></div>
                <div class="param-unit">%</div>
                <div class="param-time">Normal: ≥ 95 %</div>
            </div>

            <!-- Detak Jantung -->
            <div class="param-card">
                <div class="param-icon">💓</div>
                <div class="param-name">Detak Jantung</div>
                <div class="param-value"><?= $latest['detak_jantung'] ?? '-' ?></div>
                <div class="param-unit">bpm</div>
                <div class="param-time">Normal: 60 – 100 bpm</div>
            </div>

            <!-- Tekanan Darah -->
            <div class="param-card">
                <div class="param-icon">🩺</div>
                <div class="param-name">Tekanan Darah</div>
                <div class="param-value" style="font-size:15pt;"><?= $latest['tekanan_darah'] ?? '-' ?></div>
                <div class="param-unit">mmHg</div>
                <div class="param-time">Normal: &lt;120/80 mmHg</div>
            </div>
        </div>
    <?php endif; ?>


    <!-- ══ RIWAYAT PEMERIKSAAN ══ -->
    <div class="section-heading" style="margin-top:28px;">Riwayat Pemeriksaan Seluruh Data</div>

    <table class="riwayat-table">
        <thead>
            <tr>
                <th style="width:5%;">No.</th>
                <?php if (!$tampilkanIdentitas): ?>
                    <th style="width:20%; text-align:left;">Nama Mahasiswa</th>
                <?php endif; ?>
                <th style="width:14%;">Tanggal</th>
                <th style="width:9%;">Pukul</th>
                <th style="width:13%;">Suhu Tubuh (°C)</th>
                <th style="width:11%;">SpO₂ (%)</th>
                <th style="width:14%;">Detak Jantung (bpm)</th>
                <th style="width:14%;">Tekanan Darah (mmHg)</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($total === 0): ?>
                <tr>
                    <td colspan="<?= $colspanKosong ?>" style="padding:18px; color:#777; font-style:italic;">
                        Belum ada riwayat pemeriksaan kesehatan yang terdeteksi.
                    </td>
                </tr>
            <?php else: ?>
                <?php $no = 1;
                foreach ($rows as $r): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <?php if (!$tampilkanIdentitas): ?>
                            <td class="td-left"><?= htmlspecialchars($r['nama_mahasiswa'] ?? '-') ?></td>
                        <?php endif; ?>
                        <td><?= fmtTglLap($r['waktu']) ?></td>
                        <td><?= fmtJamLap($r['waktu']) ?></td>
                        <td style="font-weight:bold;">
                            <?= !empty($r['suhu_tubuh']) ? number_format((float) $r['suhu_tubuh'], 1) : '-' ?>
                        </td>
                        <td><?= $r['spo2'] ?? '-' ?></td>
                        <td><?= $r['detak_jantung'] ?? '-' ?></td>
                        <td><?= $r['tekanan_darah'] ?? '-' ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="keterangan">
        <strong>Keterangan Batasan Ukur Parameter:</strong><br>
        &bull; &nbsp;<strong>Suhu Tubuh</strong> : Nilai normal berkisar antara 36,1 °C – 37,2 °C.<br>
        &bull; &nbsp;<strong>SpO₂</strong> : Saturasi oksigen dalam darah, nilai normal ≥ 95 %.<br>
        &bull; &nbsp;<strong>Detak Jantung</strong> : Dihitung dalam satuan Beats Per Minute (bpm), normal 60–100
        bpm.<br>
        &bull; &nbsp;<strong>Tekanan Darah</strong> : Indikator Sistolik / Diastolik (mmHg), nilai normal di bawah
        120/80 mmHg.
    </div>


    <!-- ══ PENUTUP ══ -->
    <p class="penutup">
        Demikian laporan riwayat kesehatan ini dibuat berdasarkan hasil rekam data sensor secara otomatis.
        Laporan ini dapat digunakan sebagai bahan rujukan pemantauan kesehatan mahasiswa dan bukan merupakan
        pengganti diagnosis medis dari tenaga kesehatan yang berwenang.
    </p>

    <!-- ══ TANDA TANGAN ══ -->
    <div class="ttd-wrapper">
        <div class="ttd-box">
            <div class="ttd-kota">Mengetahui,</div>
            <div>Petugas Pemeriksa,</div>
            <div class="ttd-space"></div>
            <div class="ttd-nama">&nbsp;</div>
            <div class="ttd-nip">&nbsp;</div>
        </div>
        <div class="ttd-box">
            <div class="ttd-kota">Yogyakarta, <?= fmtTglLap(date('Y-m-d')) ?></div>
            <div>Dokter Pemeriksa,</div>
            <div class="ttd-space"></div>
            <div class="ttd-nama">&nbsp;</div>
            <div class="ttd-nip">&nbsp;</div>
        </div>
    </div>

    <?php if ($action === 'print'): ?>
        <script>window.onload = function () { window.print(); }</script>
    <?php endif; ?>

</body>

</html>