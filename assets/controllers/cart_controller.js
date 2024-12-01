import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['item', 'subtotal']
    static values = {
        mercureUrl: String
    }

    async incrementQuantity(event) {
        const itemId = event.currentTarget.dataset.itemId;
        const input = this.element.querySelector(`input[data-item-id="${itemId}"]`);
        const newQuantity = parseInt(input.value) + 1;
        await this.updateQuantity(itemId, newQuantity);
    }

    async decrementQuantity(event) {
        const itemId = event.currentTarget.dataset.itemId;
        const input = this.element.querySelector(`input[data-item-id="${itemId}"]`);
        const newQuantity = parseInt(input.value) - 1;
        if (newQuantity >= 1) {
            await this.updateQuantity(itemId, newQuantity);
        }
    }

    async updateQuantity(itemId, quantity) {
        try {
            const response = await fetch(`/cart/update/${itemId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `quantity=${quantity}`
            });

            const data = await response.json();
            if (response.ok) {
                this.subtotalTarget.textContent = `${data.newTotal} €`;
            } else {
                alert(data.error);
            }
        } catch (error) {
            console.error('Error updating quantity:', error);
        }
    }

    async removeItem(event) {
        const itemId = event.currentTarget.dataset.itemId;
        try {
            const response = await fetch(`/cart/remove/${itemId}`, {
                method: 'POST'
            });

            if (response.ok) {
                const itemElement = this.element.querySelector(`[data-item-id="${itemId}"]`);
                itemElement.remove();
                
                if (this.itemTargets.length === 0) {
                    window.location.reload();
                }
            }
        } catch (error) {
            console.error('Error removing item:', error);
        }
    }

    async checkout() {
        window.location.href = '/checkout';
    }
} 