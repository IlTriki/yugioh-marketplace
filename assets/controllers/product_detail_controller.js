import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['stockStatus', 'addToCartButton']
    static values = {
        topic: String,
        mercureUrl: String
    }

    connect() {
        this.connectToMercure();
    }

    connectToMercure() {
        const url = new URL(this.mercureUrlValue);
        url.searchParams.append('topic', this.topicValue);

        const eventSource = new EventSource(url);
        
        eventSource.onmessage = (event) => {
            const data = JSON.parse(event.data);
            this.updateProductInfo(data);
        };
    }

    updateProductInfo(data) {
        if (data.stock !== undefined) {
            if (data.stock > 0) {
                this.stockStatusTarget.innerHTML = `<span class="text-green-600">En stock (${data.stock} disponible)</span>`;
                this.addToCartButtonTarget.disabled = false;
            } else {
                this.stockStatusTarget.innerHTML = '<span class="text-red-600">En rupture de stock</span>';
                this.addToCartButtonTarget.disabled = true;
            }
        }
    }

    async addToCart(event) {
        event.preventDefault();
        
        try {
            const response = await fetch(`/cart/add/${this.productIdValue}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `quantity=1`
            });

            const data = await response.json();
            if (response.ok) {
                // Update cart count in header
                const cartCountElement = document.querySelector('[data-cart-count]');
                if (cartCountElement) {
                    cartCountElement.textContent = data.cartCount;
                }
                
                // Show success message
                alert('Product added to cart');
            } else {
                alert(data.error);
            }
        } catch (error) {
            console.error('Error adding to cart:', error);
        }
    }
}