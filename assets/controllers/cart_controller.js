import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['item', 'subtotal']
    static values = {
        mercureUrl: String,
        userId: Number
    }

    connect() {
        this.connectToMercure();
    }

    connectToMercure() {
        const url = new URL(this.mercureUrlValue);
        url.searchParams.append('topic', `cart/${this.userIdValue}`);
        console.log('Connecting to Mercure:', url.toString());

        const eventSource = new EventSource(url);
        
        eventSource.onmessage = (event) => {
            console.log('Mercure message received:', event.data);
            const data = JSON.parse(event.data);
            
            switch(data.action) {
                case 'item_added':
                    console.log('Item added, refreshing cart');
                    this.refreshCart();
                    break;
                case 'quantity_updated':
                    this.updateItemQuantity(data.itemId, data.quantity);
                    this.updateSubtotal(data.newTotal);
                    break;
                case 'item_removed':
                    this.removeItemElement(data.itemId);
                    this.updateSubtotal(data.newTotal);
                    break;
            }
        };

        eventSource.onerror = (error) => {
            console.error('Mercure EventSource error:', error);
        };

        eventSource.onopen = () => {
            console.log('Mercure connection established');
        };
    }

    updateItemQuantity(itemId, quantity) {
        const input = this.element.querySelector(`input[data-item-id="${itemId}"]`);
        if (input) {
            input.value = quantity;
        }
    }

    updateSubtotal(newTotal) {
        if (this.hasSubtotalTarget) {
            this.subtotalTarget.textContent = `${newTotal} €`;
        }
    }

    removeItemElement(itemId) {
        const itemElement = this.element.querySelector(`[data-item-id="${itemId}"]`);
        if (itemElement) {
            itemElement.remove();
            if (this.itemTargets.length === 0) {
                window.location.reload();
            }
        }
    }

    async refreshCart() {
        try {
            // Update total
            const totalResponse = await fetch('/carts/total');
            const totalData = await totalResponse.json();
            if (this.hasSubtotalTarget) {
                this.subtotalTarget.textContent = `${totalData.total} €`;
            }

            window.location.reload();
        } catch (error) {
            console.error('Error refreshing cart:', error);
        }
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
            const response = await fetch(`/carts/update/${itemId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `quantity=${quantity}`
            });

            const data = await response.json();
            if (response.ok) {
                this.subtotalTarget.textContent = `${data.newTotal} €`;
                const input = this.element.querySelector(`input[data-item-id="${itemId}"]`);
                input.value = quantity;
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
            const response = await fetch(`/carts/remove/${itemId}`, {
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
        window.location.href = '/orders/checkout';
    }
} 