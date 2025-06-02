<?php
// Include header or any necessary PHP files here
?>
<section>
    <h3 class="text-2xl font-semibold mb-6">System Analytics & Reporting</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="bg-white p-6 rounded-lg shadow">
            <h4 class="text-lg font-medium mb-2">Monthly Transactions</h4>
            <canvas id="transactionsChart" width="300" height="200"></canvas>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h4 class="text-lg font-medium mb-2">Supplier Engagement</h4>
            <canvas id="engagementChart" width="300" height="200"></canvas>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Monthly Transactions Chart
const transactionsChart = new Chart(document.getElementById('transactionsChart'), {
    type: 'bar',
    data: {
        labels: ['Jan', 'Feb', 'Mar'],
        datasets: [{
            label: 'Transactions',
            data: [50, 75, 100],
            backgroundColor: 'rgba(59, 130, 246, 0.7)'
        }]
    },
    options: {
        responsive: false,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
    }
});

// Supplier Engagement Chart
const engagementChart = new Chart(document.getElementById('engagementChart'), {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar'],
        datasets: [{
            label: 'Active Suppliers',
            data: [20, 35, 45],
            borderColor: 'rgba(34, 197, 94, 1)'
        }]
    },
    options: {
        responsive: false,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
    }
});
</script>
