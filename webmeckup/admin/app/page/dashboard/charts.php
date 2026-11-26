<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    new Chart(document.getElementById('chartUser'), {
        type: 'doughnut',
        data: {
            labels: ['Admin', 'Mahasiswa'],
            datasets: [{ data: [<?= $adm ?>, <?= $mhs ?>], backgroundColor: ['#7950f2', '#339af0'] }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
    });

    new Chart(document.getElementById('chartGender'), {
        type: 'bar',
        data: {
            labels: ['Laki-laki', 'Perempuan'],
            datasets: [{ label: 'Jumlah', data: [<?= $lakilaki ?>, <?= $perempuan ?>], backgroundColor: ['#339af0', '#f06595'] }]
        },
        options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false } } }
    });

    new Chart(document.getElementById('chartVital'), {
        type: 'bar',
        data: {
            labels: ['Suhu (°C)', 'SpO₂ (%)', 'Detak (bpm)'],
            datasets: [{ label: 'Rata-rata', data: [<?= round($avg['s'] ?? 0, 1) ?>, <?= round($avg['o'] ?? 0, 1) ?>, <?= round($avg['d'] ?? 0, 1) ?>], backgroundColor: ['#fcc419', '#20c997', '#fa5252'] }]
        },
        options: { responsive: true, maintainAspectRatio: false, indexAxis: 'y', plugins: { legend: { display: false } } }
    });

    new Chart(document.getElementById('chartActivity'), {
        type: 'line',
        data: {
            labels: <?= json_encode($dates) ?>,
            datasets: [{
                label: 'Pemeriksaan',
                data: <?= json_encode($counts) ?>,
                borderColor: '#3B5BDB',
                backgroundColor: 'rgba(59, 91, 219, 0.15)',
                fill: true, tension: 0.4,
                pointBackgroundColor: '#3B5BDB',
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });
</script>