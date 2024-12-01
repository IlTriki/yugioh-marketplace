import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['select']

    connect() {
        this.element.querySelectorAll('.order-status-select').forEach(select => {
            select.addEventListener('change', this.updateStatus.bind(this));
        });
    }

    async updateStatus(event) {
        const select = event.target;
        const orderId = select.dataset.orderId;
        const newStatus = select.value;

        try {
            const response = await fetch(`/admin/orders/${orderId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `status=${newStatus}`
            });

            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.error || 'Update failed');
            }

            const successMessage = document.createElement('div');
            successMessage.textContent = 'Status updated successfully';
            successMessage.className = 'text-green-600 text-sm mt-1';
            select.parentNode.appendChild(successMessage);
            setTimeout(() => successMessage.remove(), 3000);

        } catch (error) {
            console.error('Error updating status:', error);
            select.value = select.getAttribute('data-original-value');
            
            const errorMessage = document.createElement('div');
            errorMessage.textContent = error.message || 'Failed to update status';
            errorMessage.className = 'text-red-600 text-sm mt-1';
            select.parentNode.appendChild(errorMessage);
            setTimeout(() => errorMessage.remove(), 3000);
        }
    }
} 