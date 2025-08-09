import Chart from 'chart.js/auto';

function initializeUserEngagementChart(data) {
  const ctx = document.getElementById('user-engagement-chart');
  if (!ctx) return null;
  const chartData = data || {};
  const instance = new Chart(ctx, {
    type: 'line',
    data: {
      labels: chartData.labels || [],
      datasets: [
        {
          label: 'Daily Active Users',
          data: chartData.dau || [],
          borderColor: 'rgb(59, 130, 246)',
          backgroundColor: 'rgba(59, 130, 246, 0.1)',
          tension: 0.4,
        },
        {
          label: 'New Registrations',
          data: chartData.registrations || [],
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

function initializeRevenueChart(data) {
  const ctx = document.getElementById('revenue-chart');
  if (!ctx) return null;
  const chartData = data || {};
  const instance = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: chartData.labels || [],
      datasets: [
        {
          label: 'Revenue',
          data: chartData.revenue || [],
          backgroundColor: 'rgba(147, 51, 234, 0.8)',
          borderColor: 'rgb(147, 51, 234)',
          borderWidth: 1,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { position: 'top' }, title: { display: false } },
      scales: {
        y: {
          beginAtZero: true,
          grid: { color: 'rgba(156, 163, 175, 0.2)' },
          ticks: { callback: (value) => '$' + Number(value).toLocaleString() },
        },
        x: { grid: { color: 'rgba(156, 163, 175, 0.2)' } },
      },
    },
  });
  return instance;
}

function updateKPIs(kpis) {
  if (!kpis) return;
  Object.keys(kpis).forEach((key) => {
    const el = document.querySelector(`[data-kpi="${key}"]`);
    if (el) el.textContent = kpis[key];
  });
}

function refreshAnalytics(charts) {
  fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then((r) => r.json())
    .then((data) => {
      updateKPIs(data.kpis);
      if (charts.user) {
        charts.user.data.labels = data.chartData.userEngagement.labels;
        charts.user.data.datasets[0].data = data.chartData.userEngagement.dau;
        charts.user.data.datasets[1].data = data.chartData.userEngagement.registrations;
        charts.user.update();
      }
      if (charts.revenue) {
        charts.revenue.data.labels = data.chartData.revenue.labels;
        charts.revenue.data.datasets[0].data = data.chartData.revenue.revenue;
        charts.revenue.update();
      }
    })
    .catch(() => {});
}

document.addEventListener('DOMContentLoaded', () => {
  const userEl = document.getElementById('user-engagement-chart');
  const revenueEl = document.getElementById('revenue-chart');
  const userData = userEl ? JSON.parse(userEl.getAttribute('data-chart') || '{}') : undefined;
  const revenueData = revenueEl ? JSON.parse(revenueEl.getAttribute('data-chart') || '{}') : undefined;

  const userChart = initializeUserEngagementChart(userData);
  const revenueChart = initializeRevenueChart(revenueData);
  setInterval(() => refreshAnalytics({ user: userChart, revenue: revenueChart }), 600000);
});


