<?php
$pageTitle = 'Riwayat Kesehatan';
require_once '../../../../include/header.php';
require_once '../../../../config/koneksi.php';

$status_notif = $pesan_notif = $tipe_notif = "";

// ============================================================
// SIMPAN / UPDATE DATA
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan_data'])) {
    $id = mysqli_real_escape_string($koneksi, $_POST['id_riwayat'] ?? '');
    $id_mhs = mysqli_real_escape_string($koneksi, $_POST['id_mahasiswa']);
    $waktu = mysqli_real_escape_string($koneksi, str_replace('T', ' ', $_POST['waktu']));
    $suhu = (float) $_POST['suhu_tubuh'];
    $spo2 = (int) $_POST['spo2'];
    $detak = (int) $_POST['detak_jantung'];
    $td = mysqli_real_escape_string($koneksi, $_POST['tekanan_darah']);

    if (empty($id)) {
        $q = "INSERT INTO data_riwayat
                    (id_mahasiswa, waktu, suhu_tubuh, spo2, detak_jantung, tekanan_darah)
                VALUES ('$id_mhs','$waktu',$suhu,$spo2,$detak,'$td')";
        $msg = "ditambahkan";
    } else {
        $q = "UPDATE data_riwayat
                SET id_mahasiswa='$id_mhs', waktu='$waktu',
                    suhu_tubuh=$suhu, spo2=$spo2,
                    detak_jantung=$detak, tekanan_darah='$td'
                WHERE id_riwayat='$id'";
        $msg = "diperbarui";
    }

    if (mysqli_query($koneksi, $q)) {
        $status_notif = "BERHASIL";
        $pesan_notif = "Data riwayat berhasil $msg.";
        $tipe_notif = "success";
    } else {
        $status_notif = "GAGAL";
        $pesan_notif = "Gagal memproses data: " . mysqli_error($koneksi);
        $tipe_notif = "error";
    }
}

// ============================================================
// HAPUS DATA
// ============================================================
if (isset($_GET['hapus'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    if (mysqli_query($koneksi, "DELETE FROM data_riwayat WHERE id_riwayat='$id'")) {
        $status_notif = "BERHASIL";
        $pesan_notif = "Data riwayat berhasil dihapus.";
        $tipe_notif = "success";
    } else {
        $status_notif = "GAGAL";
        $pesan_notif = "Gagal menghapus data.";
        $tipe_notif = "error";
    }
}

// ============================================================
// AMBIL DATA RIWAYAT + JOIN MAHASISWA
// Gunakan COALESCE agar nama tidak pernah NULL
// ============================================================
$result = mysqli_query(
    $koneksi,
    "SELECT r.*,
            COALESCE(m.nama_mahasiswa, '(Data mahasiswa tidak ditemukan)') AS nama_mahasiswa,
            COALESCE(m.usia, '-')         AS usia,
            COALESCE(m.nim, '-')          AS nim,
            COALESCE(m.jenis_kelamin, '-') AS jenis_kelamin
     FROM data_riwayat r
     LEFT JOIN data_mahasiswa m ON r.id_mahasiswa = m.id_mahasiswa
     ORDER BY r.waktu DESC"
);
$total = mysqli_num_rows($result);

// ============================================================
// AMBIL DAFTAR MAHASISWA UNTUK DROPDOWN FORM
// ============================================================
$list_mhs = mysqli_query(
    $koneksi,
    "SELECT id_mahasiswa, nama_mahasiswa, usia, nim, jenis_kelamin
     FROM data_mahasiswa
     ORDER BY nama_mahasiswa ASC"
);

// Siapkan array mahasiswa untuk dikirim ke JS (auto-fill form)
$arr_mhs = [];
$tmp = $list_mhs;
while ($m = mysqli_fetch_assoc($tmp)) {
    $arr_mhs[] = $m;
}
// Reset pointer agar bisa di-loop lagi di form
$list_mhs = mysqli_query(
    $koneksi,
    "SELECT id_mahasiswa, nama_mahasiswa, usia, nim, jenis_kelamin
     FROM data_mahasiswa
     ORDER BY nama_mahasiswa ASC"
);
?>
<script>document.getElementById('breadcrumb-current').textContent = 'Riwayat Kesehatan'</script>

<div class="page-top">
    <div>
        <h2><i class="bi bi-clipboard2-pulse-fill" style="color:#3B5BDB"></i> Riwayat Kesehatan</h2>
        <p>Data hasil pemeriksaan kesehatan mahasiswa</p>
    </div>
    <div class="page-top-act">
        <span class="chip"><i class="bi bi-activity"></i> <span id="totalCount"><?= $total ?></span> Data</span>
        <button class="btn btn-green" onclick="openAdd()"><i class="bi bi-plus-lg"></i> Tambah</button>
        <a href="index.php" class="btn btn-outline"><i class="bi bi-arrow-clockwise"></i> Refresh</a>
    </div>
</div>

<div class="card">
    <table id="tblRiwayat" class="display responsive nowrap" style="width:100%">
        <thead>
            <tr>
                <th>Action</th>
                <th>Waktu</th>
                <th>NIM</th>
                <th>Nama Mahasiswa</th>
                <th>Usia</th>
                <th>Suhu</th>
                <th>SpO₂</th>
                <th>Detak</th>
                <th>Tekanan Darah</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($r = mysqli_fetch_assoc($result)): ?>
                <?php
                // Tentukan status badge berdasarkan nilai normal
                $suhu_val = (float) $r['suhu_tubuh'];
                $spo2_val = (int) $r['spo2'];
                $detak_val = (int) $r['detak_jantung'];

                $suhu_class = ($suhu_val >= 36.1 && $suhu_val <= 37.2) ? 'badge-ok' : (($suhu_val > 37.2) ? 'badge-warn' : 'badge-l');
                $spo2_class = ($spo2_val >= 95) ? 'badge-ok' : 'badge-warn';
                $detak_class = ($detak_val >= 60 && $detak_val <= 100) ? 'badge-ok' : 'badge-warn';

                // Avatar: 2 huruf pertama nama
                $nama_display = htmlspecialchars($r['nama_mahasiswa']);
                $avatar_huruf = strtoupper(substr(trim($r['nama_mahasiswa']), 0, 2));

                // Parameter untuk modal detail (gaya Mahasiswa: kirim individual)
                $dt_nama = htmlspecialchars($r['nama_mahasiswa'], ENT_QUOTES);
                $dt_nim = htmlspecialchars($r['nim'], ENT_QUOTES);
                $dt_usia = htmlspecialchars((string) $r['usia'], ENT_QUOTES);
                $dt_waktu = date('d/m/Y H:i', strtotime($r['waktu']));
                ?>
                <tr>
                    <td>
                        <div class="act">
                            <button class="btn btn-cyan btn-sm"
                                onclick="openDetail('<?= $dt_nama ?>','<?= $dt_nim ?>','<?= $dt_usia ?>','<?= $dt_waktu ?>','<?= $r['suhu_tubuh'] ?>','<?= $r['spo2'] ?>','<?= $r['detak_jantung'] ?>','<?= $dt_td ?>')">
                                <i class="bi bi-eye-fill"></i>
                            </button>
                            <!-- <button class="btn btn-yellow btn-sm" onclick="openEdit(
                                    '<?= $r['id_riwayat'] ?>',
                                    '<?= $r['id_mahasiswa'] ?>',
                                    '<?= $r['waktu'] ?>',
                                    '<?= $r['suhu_tubuh'] ?>',
                                    '<?= $r['spo2'] ?>',
                                    '<?= $r['detak_jantung'] ?>',
                                    '<?= addslashes($r['tekanan_darah']) ?>' -->
                            <!-- )"> -->
                            <!-- <i class="bi bi-pencil-fill"></i>
                            </button> -->
                            <button class="btn btn-red btn-sm"
                                onclick="confirmDelete('<?= $r['id_riwayat'] ?>','<?= addslashes($r['nama_mahasiswa']) ?>')">
                                <i class="bi bi-trash-fill"></i>
                            </button>
                        </div>
                    </td>
                    <td><?= date('d/m/Y H:i', strtotime($r['waktu'])) ?></td>
                    <td><?= htmlspecialchars($r['nim']) ?></td>
                    <td>
                        <div class="name-cell">
                            <div class="avatar"><?= $avatar_huruf ?></div>
                            <span><?= $nama_display ?></span>
                        </div>
                    </td>
                    <td><?= htmlspecialchars($r['usia']) ?> thn</td>
                    <td><span class="badge <?= $suhu_class ?>"><?= $r['suhu_tubuh'] ?> °C</span></td>
                    <td><span class="badge <?= $spo2_class ?>"><?= $r['spo2'] ?> %</span></td>
                    <td><span class="badge <?= $detak_class ?>"><?= $r['detak_jantung'] ?> bpm</span></td>
                    <td><?= htmlspecialchars($r['tekanan_darah']) ?> mmHg</td>
                </tr>
            <?php endwhile; ?>
            <?php if ($total === 0): ?>
                <tr>
                    <td colspan="9">
                        <div class="empty">
                            <i class="bi bi-inbox"></i>
                            <b>Belum ada data</b>
                            <p>Klik Tambah untuk memulai</p>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if (!empty($status_notif)): ?>
    <script>showToast('<?= addslashes($pesan_notif) ?>', '<?= $tipe_notif ?>')</script>
<?php endif; ?>

<!-- ============================================================
     MODAL: FORM TAMBAH / EDIT RIWAYAT
     ============================================================ -->
<div class="overlay" id="ovForm">
    <div class="popup">
        <form method="POST" action="index.php" id="frmRiwayat">
            <div class="popup-head">
                <h5 id="formTitle">Form Riwayat Kesehatan</h5>
                <button type="button" class="close-btn" onclick="confirmClose('ovForm','frmRiwayat')">✕</button>
            </div>
            <div class="popup-body">
                <input type="hidden" id="fld_id" name="id_riwayat">

                <!-- Pilih Mahasiswa — setelah dipilih, info otomatis terisi -->
                <div class="fg">
                    <label>Mahasiswa <span style="color:red">*</span></label>
                    <select name="id_mahasiswa" id="fld_mhs" required onchange="autoFillMhs(this.value)">
                        <option value="">-- Pilih Mahasiswa --</option>
                        <?php while ($m = mysqli_fetch_assoc($list_mhs)): ?>
                            <option value="<?= $m['id_mahasiswa'] ?>">
                                <?= htmlspecialchars($m['nama_mahasiswa']) ?>
                                <?= $m['nim'] ? ' — ' . htmlspecialchars($m['nim']) : '' ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Info otomatis dari data mahasiswa (read-only) -->
                <div class="row2" id="infoMhsBox" style="display:none; margin-bottom:10px;">
                    <div class="fg">
                        <label>NIM</label>
                        <input type="text" id="info_nim" readonly style="background:#f4f6fb; cursor:default;">
                    </div>
                    <div class="fg">
                        <label>Usia (tahun)</label>
                        <input type="text" id="info_usia" readonly style="background:#f4f6fb; cursor:default;">
                    </div>
                </div>

                <!-- Waktu -->
                <div class="fg">
                    <label>Waktu Pemeriksaan <span style="color:red">*</span></label>
                    <input type="datetime-local" id="fld_waktu" name="waktu" required>
                </div>

                <!-- Suhu & SpO2 -->
                <div class="row2">
                    <div class="fg">
                        <label>Suhu Tubuh (°C)</label>
                        <input type="number" step="0.1" min="30" max="45" id="fld_suhu" name="suhu_tubuh"
                            placeholder="36.5" required>
                    </div>
                    <div class="fg">
                        <label>SpO₂ (%)</label>
                        <input type="number" min="1" max="100" id="fld_spo2" name="spo2" placeholder="98" required>
                    </div>
                </div>

                <!-- Detak & Tekanan -->
                <div class="row2">
                    <div class="fg">
                        <label>Detak Jantung (bpm)</label>
                        <input type="number" min="1" max="250" id="fld_detak" name="detak_jantung" placeholder="72"
                            required>
                    </div>
                    <div class="fg">
                        <label>Tekanan Darah (mmHg)</label>
                        <input type="text" id="fld_td" name="tekanan_darah" placeholder="120/80"
                            pattern="\d{2,3}\/\d{2,3}">
                    </div>
                </div>
            </div>
            <div class="popup-foot">
                <button type="submit" name="simpan_data" class="btn btn-blue" onclick="loadingBtn(this)">
                    <i class="bi bi-check-lg"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================
     MODAL: DETAIL RIWAYAT (gaya selaras dengan halaman Mahasiswa)
     ============================================================ -->
<div class="overlay" id="ovDetail">
    <div class="popup">
        <div class="popup-head">
            <div>
                <h5>Detail Riwayat Kesehatan</h5>
                <p>Informasi pemeriksaan lengkap</p>
            </div>
            <button class="close-btn" onclick="off('ovDetail')">✕</button>
        </div>
        <div class="popup-body" id="detailContent"></div>
        <div class="popup-foot"><button class="btn btn-outline" onclick="off('ovDetail')">Tutup</button></div>
    </div>
</div>

<!-- ============================================================
     MODAL: KONFIRMASI HAPUS
     ============================================================ -->
<div class="overlay" id="ovHapus">
    <div class="popup popup-sm">
        <div class="notif-head">
            <button class="close-btn" onclick="off('ovHapus')">✕</button>
        </div>
        <div class="notif-body text-center" style="text-align:center">
            <h3 style="color:#E03131">HAPUS DATA</h3>
            <p>Yakin ingin menghapus data riwayat milik <b id="hapusNama"></b>?</p>
            <div class="notif-btns" style="justify-content:center; margin-top:15px">
                <button class="btn btn-outline" onclick="off('ovHapus')">Batal</button>
                <button class="btn btn-red" onclick="doDelete()">
                    <i class="bi bi-trash-fill"></i> Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================
     DATA MAHASISWA UNTUK AUTO-FILL (dari PHP ke JS)
     ============================================================ -->
<script>
    // Kirim data mahasiswa dari PHP ke JavaScript
    const dataMahasiswa = <?= json_encode($arr_mhs) ?>;

    // Buat map: id_mahasiswa => objek mahasiswa
    const mapMhs = {};
    dataMahasiswa.forEach(m => { mapMhs[m.id_mahasiswa] = m; });

    // -------------------------------------------------------
    // Auto-fill info mahasiswa ketika dropdown berubah
    // -------------------------------------------------------
    function autoFillMhs(id_mhs) {
        const box = document.getElementById('infoMhsBox');
        if (!id_mhs || !mapMhs[id_mhs]) {
            box.style.display = 'none';
            return;
        }
        const m = mapMhs[id_mhs];
        document.getElementById('info_nim').value = m.nim || '-';
        document.getElementById('info_usia').value = m.usia ? m.usia + ' tahun' : '-';
        box.style.display = 'flex';          // tampilkan baris info
    }

    // -------------------------------------------------------
    // Set waktu default ke sekarang
    // -------------------------------------------------------
    function setDefaultWaktu() {
        const now = new Date();
        const p = n => String(n).padStart(2, '0');
        document.getElementById('fld_waktu').value =
            `${now.getFullYear()}-${p(now.getMonth() + 1)}-${p(now.getDate())}T${p(now.getHours())}:${p(now.getMinutes())}`;
    }

    // -------------------------------------------------------
    // Buka form TAMBAH
    // -------------------------------------------------------
    function openAdd() {
        document.getElementById('fld_id').value = '';
        document.getElementById('formTitle').textContent = 'Tambah Riwayat Kesehatan';
        document.getElementById('fld_mhs').value = '';
        document.getElementById('infoMhsBox').style.display = 'none';
        setDefaultWaktu();
        ['fld_suhu', 'fld_spo2', 'fld_detak', 'fld_td'].forEach(id =>
            document.getElementById(id).value = ''
        );
        if (typeof trackDirty === 'function') {
            document.getElementById('frmRiwayat').dataset.dirty = '';
            trackDirty('frmRiwayat');
        }
        on('ovForm');
    }

    // -------------------------------------------------------
    // Buka form EDIT
    // -------------------------------------------------------
    function openEdit(id, mhs, wkt, s, spo, d, td) {
        document.getElementById('fld_id').value = id;
        document.getElementById('formTitle').textContent = 'Edit Riwayat Kesehatan';
        document.getElementById('fld_mhs').value = mhs;
        document.getElementById('fld_waktu').value = wkt.replace(' ', 'T').substring(0, 16);
        document.getElementById('fld_suhu').value = s;
        document.getElementById('fld_spo2').value = spo;
        document.getElementById('fld_detak').value = d;
        document.getElementById('fld_td').value = td;

        // Tampilkan info mahasiswa yang dipilih
        autoFillMhs(mhs);
        on('ovForm');
    }

    // -------------------------------------------------------
    // Detail — gaya selaras dengan halaman Mahasiswa
    // (big-av + detail-grid, status badge dihitung otomatis)
    // -------------------------------------------------------
    function openDetail(nama, nim, usia, waktu, suhu, spo2, detak, td) {
        const initial = nama.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();

        const suhuVal = parseFloat(suhu);
        const spo2Val = parseInt(spo2);
        const detakVal = parseInt(detak);

        const suhuClass = (suhuVal >= 36.1 && suhuVal <= 37.2) ? 'badge-ok' : (suhuVal > 37.2 ? 'badge-warn' : 'badge-l');
        const spo2Class = (spo2Val >= 95) ? 'badge-ok' : 'badge-warn';
        const detakClass = (detakVal >= 60 && detakVal <= 100) ? 'badge-ok' : 'badge-warn';

        const statusOk = (suhuClass === 'badge-ok' && spo2Class === 'badge-ok' && detakClass === 'badge-ok');
        const statusBadge = statusOk
            ? '<span class="badge badge-ok">Normal</span>'
            : '<span class="badge badge-warn">Perlu Perhatian</span>';

        document.getElementById('detailContent').innerHTML = `
        <div class="big-av">${initial}</div>
        <div class="detail-grid">
            <div class="di"><label>Nama Mahasiswa</label><span>${nama}</span></div>
            <div class="di"><label>NIM</label><span style="color:#3B5BDB;font-weight:800">${nim}</span></div>
            <div class="di"><label>Usia</label><span>${usia && usia !== '-' ? usia + ' Tahun' : '—'}</span></div>
            <div class="di"><label>Waktu Pemeriksaan</label><span>${waktu}</span></div>
            <div class="di"><label>Suhu Tubuh</label><span class="badge ${suhuClass}">${suhuVal} °C</span></div>
            <div class="di"><label>SpO₂</label><span class="badge ${spo2Class}">${spo2Val} %</span></div>
            <div class="di"><label>Detak Jantung</label><span class="badge ${detakClass}">${detakVal} bpm</span></div>
            <div class="di"><label>Tekanan Darah</label><span>${td} mmHg</span></div>
            <div class="di"><label>Status Pemeriksaan</label><span>${statusBadge}</span></div>
        </div>`;
        on('ovDetail');
    }

    // -------------------------------------------------------
    // Hapus
    // -------------------------------------------------------
    let hapusId = null;
    function confirmDelete(id, nama) {
        hapusId = id;
        document.getElementById('hapusNama').textContent = nama;
        on('ovHapus');
    }
    function doDelete() {
        window.location = 'index.php?hapus=' + hapusId;
    }

    // -------------------------------------------------------
    // DataTable
    // -------------------------------------------------------
    $(document).ready(function () {
        $('#tblRiwayat').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json',
                emptyTable: 'Belum ada data riwayat. Klik "Tambah" untuk memulai.'
            },
            columnDefs: [{ orderable: false, targets: 0 }],
            order: [[1, 'desc']]
        });
    });
</script>
<?php require_once '../../../../include/footer.php'; ?>