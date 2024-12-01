import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        this.initializeCharts();
    }

    initializeCharts() {
        const ctx = document.getElementById('monthlySalesChart');
        if (ctx) {
            const rawData = document.getElementById('rawData');
            const monthlySales = JSON.parse(rawData.textContent);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: monthlySales.labels,
                    datasets: [{
                        label: 'Sales (€)',
                        data: monthlySales.data,
                        borderColor: 'rgb(75, 192, 192)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true
                }
            });
        }
    }
}
