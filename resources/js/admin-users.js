import Chart from 'chart.js/auto';

document.addEventListener('DOMContentLoaded', function() {
    const userModal = document.getElementById('userModal');
    if (userModal) {
        userModal.addEventListener('show.bs.modal', function(event) {
            const row = event.relatedTarget;
            if (!row) return;
            document.getElementById('modal-username').textContent = row.getAttribute('data-name');
            document.getElementById('modal-useremail').textContent = row.getAttribute('data-email');
        });
    }

    const root = document.getElementById('userCharts');
    if (!root) return;

    let data = {};
    try {
        data = JSON.parse(root.dataset.charts || '{}');
    } catch (e) {
        return;
    }

    const COLORS = {
        student: '#0d6efd',
        teacher: '#198754',
        admin: '#dc3545',
        user: '#6c757d',
        active: '#198754',
        inactive: '#dc3545',
    };

    function isDark() {
        return document.body.classList.contains('bg-dark-mode');
    }

    function applyThemeDefaults() {
        Chart.defaults.color = isDark() ? '#cbd5e1' : '#495057';
        Chart.defaults.borderColor = isDark() ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.08)';
    }

    applyThemeDefaults();
    const percentLabels = {
        id: 'percentLabels',
        afterDatasetsDraw: function(chart) {
            const dataset = chart.data.datasets[0];
            const meta = chart.getDatasetMeta(0);
            const total = dataset.data.reduce(function(a, b) { return a + b; }, 0);
            if (!total) return;

            const ctx = chart.ctx;
            ctx.save();
            ctx.font = '600 13px sans-serif';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillStyle = '#ffffff';
            ctx.shadowColor = 'rgba(0,0,0,0.45)';
            ctx.shadowBlur = 3;

            meta.data.forEach(function(arc, i) {
                const value = dataset.data[i];
                if (!value) return;

                const pct = (value / total) * 100;
                if (pct < 4) return;

                const pos = arc.tooltipPosition();
                const text = (Number.isInteger(pct) ? pct : pct.toFixed(1)) + '%';
                ctx.fillText(text, pos.x, pos.y);
            });

            ctx.restore();
        },
    };

    const doughnutTooltip = {
        callbacks: {
            label: function(context) {
                const values = context.dataset.data;
                const total = values.reduce(function(a, b) { return a + b; }, 0);
                const value = context.parsed;
                const pct = total ? Math.round((value / total) * 100) : 0;
                return ' ' + context.label + ': ' + value + (value === 1 ? ' usuario' : ' usuarios') + ' (' + pct + '%)';
            },
        },
    };

    const charts = [];
    const baseScales = {
        x: { grid: { display: false } },
        y: { beginAtZero: true, ticks: { precision: 0 } },
    };

    const regCanvas = document.getElementById('chartRegistrations');
    let regChart = null;
    if (regCanvas && data.daily) {
        regChart = new Chart(regCanvas, {
            type: 'line',
            data: {
                labels: data.daily.labels,
                datasets: [{
                    label: 'Nuevos registros',
                    data: data.daily.data,
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13,110,253,0.15)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 2,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: baseScales,
            },
        });
        charts.push(regChart);

        root.querySelectorAll('[data-range]').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const range = btn.getAttribute('data-range');
                const set = data[range];
                if (!set) return;
                root.querySelectorAll('[data-range]').forEach(function(b) { b.classList.remove('active'); });
                btn.classList.add('active');
                regChart.data.labels = set.labels;
                regChart.data.datasets[0].data = set.data;
                regChart.update();
            });
        });
    }

    const rolesCanvas = document.getElementById('chartRoles');
    if (rolesCanvas && data.roles) {
        charts.push(new Chart(rolesCanvas, {
            type: 'doughnut',
            data: {
                labels: data.roles.map(function(r) { return r.label; }),
                datasets: [{
                    data: data.roles.map(function(r) { return r.total; }),
                    backgroundColor: data.roles.map(function(r) { return COLORS[r.role]; }),
                    borderWidth: 0,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '55%',
                plugins: { legend: { position: 'bottom' }, tooltip: doughnutTooltip },
            },
            plugins: [percentLabels],
        }));
    }

    const statusCanvas = document.getElementById('chartStatus');
    if (statusCanvas && data.status) {
        charts.push(new Chart(statusCanvas, {
            type: 'doughnut',
            data: {
                labels: ['Activas', 'Inactivas'],
                datasets: [{
                    data: [data.status.active, data.status.inactive],
                    backgroundColor: [COLORS.active, COLORS.inactive],
                    borderWidth: 0,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '55%',
                plugins: { legend: { position: 'bottom' }, tooltip: doughnutTooltip },
            },
            plugins: [percentLabels],
        }));
    }

    const rmCanvas = document.getElementById('chartRoleMonthly');
    if (rmCanvas && data.roleMonthly && data.monthly) {
        charts.push(new Chart(rmCanvas, {
            type: 'bar',
            data: {
                labels: data.monthly.labels,
                datasets: data.roleMonthly.map(function(r) {
                    return {
                        label: r.label,
                        data: r.data,
                        backgroundColor: COLORS[r.role],
                        borderRadius: 4,
                    };
                }),
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } },
                scales: baseScales,
            },
        }));
    }

    new MutationObserver(function() {
        applyThemeDefaults();
        charts.forEach(function(c) { c.update(); });
    }).observe(document.body, { attributes: true, attributeFilter: ['class'] });
});