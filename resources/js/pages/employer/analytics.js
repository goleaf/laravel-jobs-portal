import Chart from 'chart.js/auto';

function initializeViewsApplicationsChart(data) {
  const ctx = document.getElementById('views-applications-chart');
  if (!ctx) return null;
  const chartData = data || {};
  const instance = new Chart(ctx, {
    type: 'line',
    data: {
      labels: chartData.labels || [],
      datasets: [
        {
          label: 'Views',
          data: chartData.views || [],
          borderColor: 'rgb(59, 130, 246)',
          backgroundColor: 'rgba(59, 130, 246, 0.1)',
          tension: 0.4,
        },
        {
          label: 'Applications',
          data: chartData.applications || [],
          borderColor: 'rgb(16, 185, 129)',
          backgroundColor: 'rgba(16, 185, 129, 0.1)',
          tension: 0.4,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { position: 'top' }, title: { display: false } },
      scales: {
        y: { beginAtZero: true, grid: { color: 'rgba(156, 163, 175, 0.2)' } },
        x: { grid: { color: 'rgba(156, 163, 175, 0.2)' } },
      },
    },
  });
  return instance;
}

function updateMetrics(metrics) {
  if (!metrics) return;
  Object.keys(metrics).forEach((key) => {
    const el = document.querySelector(`[data-metric="${key}"]`);
    if (el) el.textContent = metrics[key];
  });
}

function refreshAnalytics(charts) {
  fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then((r) => r.json())
    .then((data) => {
      updateMetrics(data.metrics);
      if (charts.viewsApps) {
        charts.viewsApps.data.labels = data.chartData.labels;
        charts.viewsApps.data.datasets[0].data = data.chartData.views;
        charts.viewsApps.data.datasets[1].data = data.chartData.applications;
        charts.viewsApps.update();
      }
    })
    .catch(() => {});
}

document.addEventListener('DOMContentLoaded', () => {
  const el = document.getElementById('views-applications-chart');
  const data = el ? JSON.parse(el.getAttribute('data-chart') || '{}') : undefined;
  const viewsAppsChart = initializeViewsApplicationsChart(data);
  setInterval(() => refreshAnalytics({ viewsApps: viewsAppsChart }), 300000);
});


