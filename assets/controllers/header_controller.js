import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['cartCount']
    static values = {
        mercureUrl: String,
        userId: Number
    }

    connect() {
        if (this.hasUserIdValue) {
            this.connectToMercure();
        }
    }

    connectToMercure() {
        const url = new URL(this.mercureUrlValue);
        url.searchParams.append('topic', `cart/${this.userIdValue}`);
        
        const eventSource = new EventSource(url);
        
        eventSource.onmessage = (event) => {
            const data = JSON.parse(event.data);
            if (data.cartCount !== undefined) {
                this.updateCartCount(data.cartCount);
            }
        };
    }

    updateCartCount(count) {
        if (this.hasCartCountTarget) {
            this.cartCountTarget.textContent = count;
        }
    }
} 