<?php
$pageTitle = 'Data Admin';
require_once '../../../../include/header.php';
require_once '../../../../config/koneksi.php';

$status_notif = $pesan_notif = $tipe_notif = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan_data'])) {
    $id = mysqli_real_escape_string($koneksi, $_POST['id_admin']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $user = mysqli_real_escape_string($koneksi, $_POST['username']);
    $pass_input = $_POST['password'];

    if (empty($id)) {
        $new_id = uniqid('adm_');
        $q = "INSERT INTO data_admin (id_admin,nama,username,password) VALUES ('$new_id','$nama','$user','$pass_input')";
        $msg = "ditambahkan.";
    } else {
        if (!empty($pass_input)) {
            $q = "UPDATE data_admin SET nama='$nama', username='$user', password='$pass_input' WHERE id_admin='$id'";
        } else {
            $q = "UPDATE data_admin SET nama='$nama', username='$user' WHERE id_admin='$id'";
        }
        $msg = "diperbarui.";
    }
    if (mysqli_query($koneksi, $q)) {
        $status_notif = "BERHASIL";
        $pesan_notif = "Data admin berhasil $msg";
        $tipe_notif = "success";
    } else {
        $status_notif = "GAGAL";
        $pesan_notif = "Terjadi kesalahan.";
        $tipe_notif = "error";
    }
}

if (isset($_GET['hapus'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    if (mysqli_query($koneksi, "DELETE FROM data_admin WHERE id_admin='$id'")) {
        $status_notif = "BERHASIL";
        $pesan_notif = "Data admin berhasil dihapus.";
        $tipe_notif = "success";
    } else {
        $status_notif = "GAGAL";
        $pesan_notif = "Gagal menghapus.";
        $tipe_notif = "error";
    }
}

$result = mysqli_query($koneksi, "SELECT * FROM data_admin ORDER BY nama ASC");
$total = mysqli_num_rows($result);
?>
<script>document.getElementById('breadcrumb-current').textContent = 'Data Admin'</script>

<div class="page-top">
    <div>
        <h2><i class="bi bi-shield-lock-fill" style="color:#3B5BDB"></i> Data Admin</h2>
        <p>Kelola akun administrator sistem</p>
    </div>
    <div class="page-top-act">
        <span class="chip"><i class="bi bi-shield"></i> <span><?= $total ?></span> Admin</span>
        <button class="btn btn-green" onclick="openAdd()"><i class="bi bi-plus-lg"></i> Tambah Admin</button>
        <a href="index.php" class="btn btn-outline"><i class="bi bi-arrow-clockwise"></i> Refresh</a>
    </div>
</div>

<div class="card">
    <table id="tblAdmin" class="display responsive nowrap" style="width:100%">
        <thead>
            <tr>
                <th>Action</th>
                <th>Nama Admin</th>
                <th>Username</th>
                <th>Password</th>
                <th>Tanggal Dibuat</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1;
            while ($r = mysqli_fetch_assoc($result)):
                $js_nama = htmlspecialchars($r['nama'], ENT_QUOTES);
                $js_user = htmlspecialchars($r['username'], ENT_QUOTES); ?>
                <tr>
                    <td>
                        <div class="act">
                            <button class="btn btn-yellow btn-sm"
                                onclick="openEdit('<?= $r['id_admin'] ?>','<?= $js_nama ?>','<?= $js_user ?>')"><i
                                    class="bi bi-pencil-fill"></i></button>
                            <button class="btn btn-red btn-sm"
                                onclick="confirmDelete('<?= $r['id_admin'] ?>','<?= $js_nama ?>')"><i
                                    class="bi bi-trash-fill"></i></button>
                        </div>
                    </td>
                    <td>
                        <div class="name-cell">
                            <div class="avatar"><?= strtoupper(substr(trim($r['nama']), 0, 2)) ?></div>
                            <span><?= htmlspecialchars($r['nama']) ?></span>
                        </div>
                    </td>
                    <td><code
                            style="background:#F1F3F5;padding:3px 8px;border-radius:6px;font-size:.82rem"><?= htmlspecialchars($r['username']) ?></code>
                    </td>
                    <td><span style="color:#ADB5BD;font-size:.82rem;letter-spacing:2px">••••••••</span></td>
                    <td style="color:#868E96;font-size:.82rem">
                        <?= isset($r['created_at']) ? date('d/m/Y H:i', strtotime($r['created_at'])) : '—' ?>
                    </td>
                </tr>
            <?php endwhile; ?>
            <?php if ($total === 0): ?>
                <tr>
                    <td colspan="5">
                        <div class="empty"><i class="bi bi-inbox"></i><b>Belum ada data admin</b>
                            <p>Klik Tambah Admin untuk memulai</p>
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

<div class="overlay" id="ovForm">
    <div class="popup">
        <div class="popup-head">
            <div>
                <h5 id="popTitle">Tambah Admin</h5>
                <p id="popSub">Isi data administrator baru</p>
            </div>
            <button class="close-btn" onclick="confirmClose('ovForm','frmAdmin')">✕</button>
        </div>
        <form method="POST" action="index.php" id="frmAdmin">
            <div class="popup-body">
                <input type="hidden" id="fld_id" name="id_admin">
                <div class="fg">
                    <label>Nama Lengkap *</label>
                    <input type="text" id="fld_nama" name="nama" required placeholder="Nama lengkap administrator">
                </div>
                <div class="row2">
                    <div class="fg">
                        <label>Username *</label>
                        <input type="text" id="fld_username" name="username" required placeholder="Username login">
                    </div>
                    <div class="fg">
                        <label>Password</label>
                        <div class="pw-wrap" style="position:relative">
                            <input type="password" id="fld_password" name="password" placeholder="Password"
                                style="padding-right:40px">
                            <button type="button" onclick="togglePw()"
                                style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#ADB5BD;font-size:1.1rem;padding:4px;line-height:1">
                                <i class="bi bi-eye" id="pwEye"></i>
                            </button>
                        </div>
                        <small id="pwHint" style="display:none">Kosongkan jika tidak ingin mengubah password</small>
                    </div>
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

<div class="overlay" id="ovHapus">
    <div class="popup popup-sm">
        <div class="notif-head"><button class="close-btn" onclick="off('ovHapus')">X</button></div>
        <div class="notif-body text-center" style="text-align:center">
            <h3 style="color:#E03131">HAPUS DATA</h3>
            <p>Apakah anda ingin menghapus admin <b id="hapusNama"></b>?</p>
            <div class="notif-btns" style="justify-content:center;margin-top:15px">
                <button class="btn btn-outline" onclick="off('ovHapus')">Batal</button>
                <button class="btn btn-red" onclick="doDelete()"><i class="bi bi-trash-fill"></i> Hapus</button>
            </div>
        </div>
    </div>
</div>

<script>
    let hapusId = null;

    function togglePw() {
        const f = document.getElementById('fld_password');
        const e = document.getElementById('pwEye');
        f.type === 'password' ? (f.type = 'text', e.className = 'bi bi-eye-slash') : (f.type = 'password', e.className = 'bi bi-eye');
    }

    function openAdd() {
        ['fld_id', 'fld_nama', 'fld_username', 'fld_password'].forEach(id => document.getElementById(id).value = '');
        document.getElementById('fld_password').required = true;
        document.getElementById('pwHint').style.display = 'none';
        document.getElementById('popTitle').textContent = 'Tambah Admin';
        document.getElementById('btnTxt').textContent = 'Simpan';
        document.getElementById('frmAdmin').dataset.dirty = '';
        trackDirty('frmAdmin');
        on('ovForm');
    }

    function openEdit(id, nama, user) {
        document.getElementById('fld_id').value = id;
        document.getElementById('fld_nama').value = nama;
        document.getElementById('fld_username').value = user;
        document.getElementById('fld_password').value = '';
        document.getElementById('fld_password').required = false;
        document.getElementById('pwHint').style.display = 'block';
        document.getElementById('popTitle').textContent = 'Edit Admin';
        document.getElementById('btnTxt').textContent = 'Update';
        document.getElementById('frmAdmin').dataset.dirty = '';
        trackDirty('frmAdmin');
        on('ovForm');
    }

    function confirmDelete(id, nama) { hapusId = id; document.getElementById('hapusNama').textContent = nama; on('ovHapus'); }
    function doDelete() { window.location = 'index.php?hapus=' + hapusId; }

    $(document).ready(function () {
        $('#tblAdmin').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json',
                emptyTable: 'Belum ada data admin. Klik "Tambah Admin" untuk memulai.'
            },
            columnDefs: [{ orderable: false, targets: 0 }],
            order: [[1, 'asc']]
        });
    });
</script>
<?php require_once '../../../../include/footer.php'; ?>