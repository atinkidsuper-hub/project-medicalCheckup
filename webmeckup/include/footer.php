<!-- ↓ footer.php -->
</div><!-- end .page-content -->
</div><!-- end .main-wrap -->

<div class="toast-container" id="toastContainer"></div>

<script>
    const on = id => document.getElementById(id)?.classList.add('on');
    const off = id => document.getElementById(id)?.classList.remove('on');

    function showToast(msg, type = 'success', duration = 3500) {
        const c = document.getElementById('toastContainer');
        if (!c) return;
        const icon = type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill';
        const el = document.createElement('div');
        el.className = 'toast toast-' + type;
        el.innerHTML = '<i class="bi ' + icon + ' toast-icon"></i> ' + msg +
            ' <button class="toast-close" onclick="var p=this.parentElement;p.style.animation=\'toastOut .3s ease forwards\';setTimeout(function(){p.remove()},300)">\u00d7</button>';
        c.appendChild(el);
        if (duration > 0) {
            setTimeout(function () {
                if (el.parentNode) {
                    el.style.animation = 'toastOut .3s ease forwards';
                    setTimeout(function () { if (el.parentNode) el.remove(); }, 300);
                }
            }, duration);
        }
    }

    function loadingBtn(btn) {
        if (!btn || btn.classList.contains('loading')) return;
        btn.classList.add('loading');
        const i = btn.querySelector('i');
        if (i) { i.dataset.old = i.className; i.className = 'bi bi-arrow-repeat'; }
    }

    function unloadBtn(btn) {
        btn.classList.remove('loading');
        const i = btn.querySelector('i');
        if (i && i.dataset.old) { i.className = i.dataset.old; delete i.dataset.old; }
    }

    // Confirm cancel on dirty forms
    function trackDirty(formId) {
        const f = document.getElementById(formId);
        if (!f) return;
        f.addEventListener('input', function () { f.dataset.dirty = 'true'; });
        f.addEventListener('change', function () { f.dataset.dirty = 'true'; });
    }
    function confirmClose(overlayId, formId) {
        const f = document.getElementById(formId);
        if (f && f.dataset.dirty === 'true') {
            if (!confirm('Perubahan belum disimpan. Yakin ingin menutup?')) return;
        }
        off(overlayId);
    }

    // Tutup overlay saat klik background
    document.querySelectorAll('.overlay').forEach(function (el) {
        el.addEventListener('click', function (e) {
            if (e.target === el) {
                const form = el.querySelector('form');
                if (form && form.dataset.dirty === 'true') {
                    if (!confirm('Perubahan belum disimpan. Yakin ingin menutup?')) return;
                }
                el.classList.remove('on');
            }
        });
    });
</script>
</body>

</html>