<?php
$pageTitle = 'Data Mahasiswa';
require_once '../../../../include/header.php';
require_once '../../../../config/koneksi.php';

$status_notif = $pesan_notif = $tipe_notif = "";

/* Helper: ubah input kosong jadi NULL (bukan string ''), supaya kolom
   nullable seperti usia (INT) / uid / program_studi / fakultas (ENUM)
   tidak menyebabkan error "Incorrect value" saat dikosongkan. */
function sqlVal($koneksi, $val)
{
    $val = is_null($val) ? '' : trim($val);
    if ($val === '')
        return 'NULL';
    return "'" . mysqli_real_escape_string($koneksi, $val) . "'";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan_data'])) {
    $id = mysqli_real_escape_string($koneksi, $_POST['id_mahasiswa']);
    $nama = sqlVal($koneksi, $_POST['nama_mahasiswa'] ?? '');
    $nim = sqlVal($koneksi, $_POST['nim'] ?? '');
    $uid = sqlVal($koneksi, $_POST['uid'] ?? '');
    $usia = sqlVal($koneksi, $_POST['usia'] ?? '');
    $prodi = sqlVal($koneksi, $_POST['program_studi'] ?? '');
    $fakultas = sqlVal($koneksi, $_POST['fakultas'] ?? '');
    $jk = sqlVal($koneksi, $_POST['jenis_kelamin'] ?? '');
    $tgl = sqlVal($koneksi, $_POST['tanggal_lahir'] ?? '');

    if (empty($id)) {
        $new_id = uniqid('mhs_');
        $q = "INSERT INTO data_mahasiswa (id_mahasiswa,nama_mahasiswa,nim,uid,usia,program_studi,fakultas,jenis_kelamin,tanggal_lahir) VALUES ('$new_id',$nama,$nim,$uid,$usia,$prodi,$fakultas,$jk,$tgl)";
        $msg = "ditambahkan.";
    } else {
        $q = "UPDATE data_mahasiswa SET nama_mahasiswa=$nama, nim=$nim, uid=$uid, usia=$usia, program_studi=$prodi, fakultas=$fakultas, jenis_kelamin=$jk, tanggal_lahir=$tgl WHERE id_mahasiswa='$id'";
        $msg = "diperbarui.";
    }
    if (mysqli_query($koneksi, $q)) {
        $status_notif = "BERHASIL";
        $pesan_notif = "Data mahasiswa berhasil $msg";
        $tipe_notif = "success";
    } else {
        $status_notif = "GAGAL";
        $pesan_notif = "Terjadi kesalahan saat menyimpan data.";
        $tipe_notif = "error";
    }
}

if (isset($_GET['hapus'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    if (mysqli_query($koneksi, "DELETE FROM data_mahasiswa WHERE id_mahasiswa='$id'")) {
        $status_notif = "BERHASIL";
        $pesan_notif = "Data mahasiswa berhasil dihapus.";
        $tipe_notif = "success";
    } else {
        $status_notif = "GAGAL";
        $pesan_notif = "Gagal menghapus data.";
        $tipe_notif = "error";
    }
}

$result = mysqli_query($koneksi, "SELECT * FROM data_mahasiswa ORDER BY nama_mahasiswa ASC");
$total = mysqli_num_rows($result);

function inisial($n)
{
    $w = explode(' ', trim($n));
    return strtoupper(substr($w[0], 0, 1) . (isset($w[1]) ? substr($w[1], 0, 1) : ''));
}
function fmtTgl($d)
{
    $bln = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    if (!$d || $d === '0000-00-00')
        return '—';
    return date('d', strtotime($d)) . ' ' . $bln[(int) date('m', strtotime($d))] . ' ' . date('Y', strtotime($d));
}
?>
<script>document.getElementById('breadcrumb-current').textContent = 'Data Mahasiswa'</script>

<div class="page-top">
    <div>
        <h2><i class="bi bi-people-fill" style="color:#3B5BDB"></i> Data Mahasiswa</h2>
        <p>Kelola data mahasiswa dan kartu RFID</p>
    </div>
    <div class="page-top-act">
        <span class="chip"><i class="bi bi-people"></i> <span id="totalCount"><?= $total ?></span> Mahasiswa</span>
        <button class="btn btn-green" onclick="openAdd()"><i class="bi bi-plus-lg"></i> Tambah</button>
        <a href="index.php" class="btn btn-outline"><i class="bi bi-arrow-clockwise"></i> Refresh</a>
    </div>
</div>

<div class="card">
    <table id="tblMahasiswa" class="display responsive nowrap" style="width:100%">
        <thead>
            <tr>
                <th>Action</th>
                <th>Nama Mahasiswa</th>
                <th>UID</th>
                <th>Usia</th>
                <th>NIM</th>
                <th>Program Studi</th>
                <th>Fakultas</th>
                <th>Jenis Kelamin</th>
                <th>Tanggal Lahir</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($r = mysqli_fetch_assoc($result)):
                $js_id = htmlspecialchars($r['id_mahasiswa'] ?? '', ENT_QUOTES);
                $js_nama = htmlspecialchars($r['nama_mahasiswa'] ?? '', ENT_QUOTES);
                $js_nim = htmlspecialchars($r['nim'] ?? '', ENT_QUOTES);
                $js_uid = htmlspecialchars($r['uid'] ?? '', ENT_QUOTES);
                $js_usia = htmlspecialchars($r['usia'] ?? '', ENT_QUOTES);
                $js_prodi = htmlspecialchars($r['program_studi'] ?? '', ENT_QUOTES);
                $js_fakultas = htmlspecialchars($r['fakultas'] ?? '', ENT_QUOTES);
                $js_jk = htmlspecialchars($r['jenis_kelamin'] ?? '', ENT_QUOTES);
                $js_tgl = htmlspecialchars($r['tanggal_lahir'] ?? '', ENT_QUOTES);
                $js_fmt = fmtTgl($r['tanggal_lahir']); ?>
                <tr>
                    <td>
                        <div class="act">
                            <button class="btn btn-cyan btn-sm"
                                onclick="openDetail('<?= $js_nama ?>','<?= $js_nim ?>','<?= $js_uid ?>','<?= $js_jk ?>','<?= $js_fmt ?>','<?= $js_usia ?>','<?= $js_prodi ?>','<?= $js_fakultas ?>')"><i
                                    class="bi bi-eye-fill"></i></button>
                            <button class="btn btn-yellow btn-sm"
                                onclick="openEdit('<?= $js_id ?>','<?= $js_nama ?>','<?= $js_nim ?>','<?= $js_uid ?>','<?= $js_jk ?>','<?= $js_tgl ?>','<?= $js_usia ?>','<?= $js_prodi ?>','<?= $js_fakultas ?>')"><i
                                    class="bi bi-pencil-fill"></i></button>
                            <button class="btn btn-red btn-sm" onclick="confirmDelete('<?= $js_id ?>','<?= $js_nama ?>')"><i
                                    class="bi bi-trash-fill"></i></button>
                        </div>
                    </td>
                    <td>
                        <div class="name-cell">
                            <div class="avatar"><?= inisial($r['nama_mahasiswa']) ?></div>
                            <span><?= htmlspecialchars($r['nama_mahasiswa'] ?? '') ?></span>
                        </div>
                    </td>
                    <td><span class="uid"><?= htmlspecialchars($r['uid'] ?: '—') ?></span></td>
                    <td><?= ($r['usia'] !== null && $r['usia'] !== '') ? htmlspecialchars((string) $r['usia']) . ' Tahun' : '—' ?>
                    </td>
                    <td style="font-weight:700;color:#3B5BDB"><?= htmlspecialchars($r['nim'] ?? '') ?></td>
                    <td><span class="chip"><?= htmlspecialchars($r['program_studi'] ?: '—') ?></span></td>
                    <td><?= htmlspecialchars($r['fakultas'] ?: '—') ?></td>
                    <td><?= strtolower($r['jenis_kelamin'] ?? '') === 'laki-laki' ? '<span class="badge badge-l">Laki-laki</span>' : '<span class="badge badge-p">Perempuan</span>' ?>
                    </td>
                    <td style="color:#868E96"><?= fmtTgl($r['tanggal_lahir']) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php if (!empty($status_notif)): ?>
    <script>showToast('<?= addslashes($pesan_notif) ?>', '<?= $tipe_notif ?>')</script>
<?php endif; ?>

<div class="overlay" id="ovForm">
    <div class="popup popup-lg">
        <div class="popup-head">
            <div>
                <h5 id="popTitle">Tambah Mahasiswa</h5>
                <p id="popSub">Isi data mahasiswa baru</p>
            </div>
            <button class="close-btn" onclick="confirmClose('ovForm','frmMhs')">✕</button>
        </div>
        <form method="POST" action="index.php" id="frmMhs">
            <div class="popup-body">
                <input type="hidden" id="fld_id" name="id_mahasiswa">

                <div class="fg"><label>Nama Lengkap *</label><input type="text" id="fld_nama" name="nama_mahasiswa"
                        required placeholder="Nama lengkap"></div>

                <div class="fg">
                    <label>UID Kartu RFID</label>
                    <div class="scan-wrap">
                        <input type="text" id="fld_uid" name="uid" readonly
                            placeholder="Klik Scan Kartu untuk mengisi otomatis">
                        <button type="button" class="btn-scan" onclick="scanKartu()" id="btnScan">
                            <i class="bi bi-rfid"></i> Scan Kartu
                        </button>
                    </div>
                    <div class="status-scan idle" id="statusScan">
                        <span class="rfid-pulse"></span>
                        <span class="scan-text"><i class="bi bi-info-circle"></i> Klik "Scan Kartu" lalu tap kartu ke
                            alat IoT</span>
                    </div>
                </div>

                <div class="row2">
                    <div class="fg">
                        <label>Usia</label>
                        <input type="number" id="fld_usia" name="usia" min="0" max="120"
                            placeholder="Otomatis dari tanggal lahir">
                    </div>
                    <div class="fg"><label>NIM *</label><input type="text" id="fld_nim" name="nim" required
                            placeholder="Nomor Induk Mahasiswa"></div>
                </div>

                <div class="row2">
                    <div class="fg">
                        <label>Program Studi</label>
                        <select id="fld_prodi" name="program_studi">
                            <option value="Rekayasa Sistem Komputer">Rekayasa Sistem Komputer</option>
                            <option value="Informatika">Informatika</option>
                            <option value="Statistika">Statistika</option>
                        </select>
                    </div>
                    <div class="fg">
                        <label>Fakultas</label>
                        <input type="text" id="fld_fakultas" name="fakultas" placeholder="Otomatis dari program studi">
                    </div>
                </div>

                <div class="row2">
                    <div class="fg">
                        <label>Jenis Kelamin</label>
                        <select id="fld_jk" name="jenis_kelamin">
                            <option value="laki-laki">Laki-laki</option>
                            <option value="perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div class="fg"><label>Tanggal Lahir</label><input type="date" id="fld_tgl" name="tanggal_lahir"
                            required></div>
                </div>
            </div>
            <div class="popup-foot">
                <button type="button" class="btn btn-outline" onclick="off('ovForm')"><i class="bi bi-x-lg"></i>
                    Batal</button>
                <button type="submit" name="simpan_data" class="btn btn-blue" onclick="loadingBtn(this)"><i
                        class="bi bi-check-lg"></i> <span id="btnTxt">Simpan</span></button>
            </div>
        </form>
    </div>
</div>

<div class="overlay" id="ovDetail">
    <div class="popup">
        <div class="popup-head">
            <div>
                <h5>Detail Mahasiswa</h5>
                <p>Informasi lengkap</p>
            </div>
            <button class="close-btn" onclick="off('ovDetail')">✕</button>
        </div>
        <div class="popup-body" id="detailContent"></div>
        <div class="popup-foot"><button class="btn btn-outline" onclick="off('ovDetail')">Tutup</button></div>
    </div>
</div>

<div class="overlay" id="ovHapus">
    <div class="popup popup-sm">
        <div class="notif-head"><button class="close-btn" onclick="off('ovHapus')">X</button></div>
        <div class="notif-body text-center" style="text-align:center">
            <h3 style="color:#E03131">HAPUS DATA</h3>
            <p>Apakah anda ingin menghapus data <b id="hapusNama"></b>?</p>
            <div class="notif-btns" style="justify-content:center;margin-top:15px">
                <button class="btn btn-outline" onclick="off('ovHapus')">Batal</button>
                <button class="btn btn-red" onclick="doDelete()"><i class="bi bi-trash-fill"></i> Hapus</button>
            </div>
        </div>
    </div>
</div>

<script>
    let hapusId = null;

    /* Pemetaan otomatis Program Studi -> Fakultas.
       PENTING: kolom `fakultas` di database adalah ENUM, jadi nilai di sini
       HARUS PERSIS SAMA dengan pilihan enum di database. Jalankan ALTER TABLE
       yang diberikan di chat supaya enum di database sama persis dengan ini. */
    const fakultasMap = {
        'Rekayasa Sistem Komputer': 'Fakultas Sains dan Teknologi Informasi',
        'Informatika': 'Fakultas Informatika',
        'Statistika': 'Fakultas Komputer'
    };

    function hitungUsia(tglLahirStr) {
        if (!tglLahirStr) return '';
        const lahir = new Date(tglLahirStr);
        if (isNaN(lahir.getTime())) return '';
        const now = new Date();
        let usia = now.getFullYear() - lahir.getFullYear();
        const m = now.getMonth() - lahir.getMonth();
        if (m < 0 || (m === 0 && now.getDate() < lahir.getDate())) usia--;
        return usia >= 0 ? usia : '';
    }

    function openAdd() {
        ['fld_id', 'fld_nama', 'fld_nim', 'fld_uid', 'fld_usia', 'fld_tgl'].forEach(id => document.getElementById(id).value = '');
        document.getElementById('fld_prodi').selectedIndex = 0;
        document.getElementById('fld_fakultas').value = fakultasMap[document.getElementById('fld_prodi').value] || '';
        document.getElementById('popTitle').textContent = 'Tambah Mahasiswa';
        document.getElementById('popSub').textContent = 'Isi data mahasiswa baru';
        document.getElementById('btnTxt').textContent = 'Simpan';
        document.getElementById('frmMhs').dataset.dirty = '';
        trackDirty('frmMhs');
        on('ovForm');
    }

    function openEdit(id, nama, nim, uid, jk, tgl, usia, prodi, fakultas) {
        document.getElementById('fld_id').value = id;
        document.getElementById('fld_nama').value = nama;
        document.getElementById('fld_nim').value = nim;
        document.getElementById('fld_uid').value = uid;
        document.getElementById('fld_jk').value = jk;
        document.getElementById('fld_tgl').value = tgl;
        document.getElementById('fld_usia').value = usia;
        document.getElementById('fld_prodi').value = prodi;
        document.getElementById('fld_fakultas').value = fakultas;
        document.getElementById('popTitle').textContent = 'Edit Mahasiswa';
        document.getElementById('popSub').textContent = 'Perbarui data mahasiswa';
        document.getElementById('btnTxt').textContent = 'Update';
        document.getElementById('frmMhs').dataset.dirty = '';
        trackDirty('frmMhs');
        on('ovForm');
    }

    function openDetail(nama, nim, uid, jk, tgl, usia, prodi, fakultas) {
        const initial = nama.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
        const badge = jk.toLowerCase() === 'laki-laki' ? '<span class="badge badge-l">Laki-laki</span>' : '<span class="badge badge-p">Perempuan</span>';
        document.getElementById('detailContent').innerHTML = `
        <div class="big-av">${initial}</div>
        <div class="detail-grid">
            <div class="di"><label>Nama</label><span>${nama}</span></div>
            <div class="di"><label>UID Kartu</label><span class="uid">${uid || '—'}</span></div>
            <div class="di"><label>Usia</label><span>${usia ? usia + ' Tahun' : '—'}</span></div>
            <div class="di"><label>NIM</label><span style="color:#3B5BDB;font-weight:800">${nim}</span></div>
            <div class="di"><label>Program Studi</label><span>${prodi || '—'}</span></div>
            <div class="di"><label>Fakultas</label><span>${fakultas || '—'}</span></div>
            <div class="di"><label>Jenis Kelamin</label><span>${badge}</span></div>
            <div class="di"><label>Tanggal Lahir</label><span>${tgl}</span></div>
        </div>`;
        on('ovDetail');
    }

    /* Usia otomatis dari Tanggal Lahir, tetap bisa diedit manual setelahnya */
    document.getElementById('fld_tgl').addEventListener('change', function () {
        const usia = hitungUsia(this.value);
        if (usia !== '') document.getElementById('fld_usia').value = usia;
    });

    /* Fakultas otomatis dari Program Studi, tetap bisa diedit manual setelahnya */
    document.getElementById('fld_prodi').addEventListener('change', function () {
        document.getElementById('fld_fakultas').value = fakultasMap[this.value] || '';
    });

    /* ─── SCAN KARTU INTERAKTIF ─── */
    let scanInterval = null;
    let mulaiScan = null;

    function resetScanState() {
        if (scanInterval) { clearInterval(scanInterval); scanInterval = null; }
        mulaiScan = null;
        const btn = document.getElementById('btnScan');
        btn.disabled = false; btn.className = 'btn-scan';
        btn.innerHTML = '<i class="bi bi-rfid"></i> Scan Kartu';
        const st = document.getElementById('statusScan');
        st.className = 'status-scan idle';
        st.querySelector('.scan-text').innerHTML = '<i class="bi bi-info-circle"></i> Klik "Scan Kartu" lalu tap kartu ke alat IoT';
    }

    function scanKartu() {
        if (scanInterval) return;
        document.getElementById('fld_uid').value = '';
        mulaiScan = new Date();

        const btn = document.getElementById('btnScan');
        btn.disabled = true; btn.className = 'btn-scan scanning';
        btn.innerHTML = '<span class="pulse-dot"></span> Menunggu kartu...';

        const st = document.getElementById('statusScan');
        st.className = 'status-scan waiting';
        st.querySelector('.scan-text').innerHTML = 'Tap kartu RFID ke alat IoT...';

        scanInterval = setInterval(function () {
            if ((new Date() - mulaiScan) > 30000) {
                clearInterval(scanInterval); scanInterval = null;
                btn.disabled = false; btn.className = 'btn-scan';
                btn.innerHTML = '<i class="bi bi-rfid"></i> Scan Kartu';
                st.className = 'status-scan error';
                st.querySelector('.scan-text').innerHTML = '<i class="bi bi-exclamation-triangle-fill"></i> Timeout — coba lagi';
                return;
            }
            fetch('../../../../api/ambil_scan.php?t=' + Date.now())
                .then(r => r.json())
                .then(data => {
                    if (!data.waktu) return;
                    const waktuScan = new Date(data.waktu.replace(' ', 'T'));
                    if (waktuScan >= mulaiScan && data.uid) {
                        clearInterval(scanInterval); scanInterval = null;
                        document.getElementById('fld_uid').value = data.uid;
                        btn.disabled = false; btn.className = 'btn-scan';
                        btn.innerHTML = '<i class="bi bi-check-lg"></i> Terbaca';
                        st.className = 'status-scan success';
                        st.querySelector('.scan-text').innerHTML = '<i class="bi bi-check-circle-fill"></i> Kartu terbaca! UID: ' + data.uid;
                    }
                })
                .catch(function () { });
        }, 1000);
    }

    function confirmDelete(id, nama) {
        hapusId = id;
        document.getElementById('hapusNama').textContent = nama;
        on('ovHapus');
    }

    function doDelete() { window.location = 'index.php?hapus=' + hapusId; }

    $(document).ready(function () {
        $('#tblMahasiswa').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json',
                emptyTable: 'Belum ada data mahasiswa. Klik "Tambah" untuk memulai.'
            },
            columnDefs: [
                { orderable: false, targets: 0 },
                { searchable: false, targets: [0, 7] }
            ],
            order: [[1, 'asc']]
        });
    });
</script>
<?php require_once '../../../../include/footer.php'; ?>