import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['mainImage', 'thumbnail']

    connect() {
        this.currentIndex = 0;
        this.maxIndex = this.mainImageTargets.length - 1;
    }

    next() {
        this.showImage((this.currentIndex + 1) % (this.maxIndex + 1));
    }

    previous() {
        this.showImage(this.currentIndex === 0 ? this.maxIndex : this.currentIndex - 1);
    }

    selectImage(event) {
        const index = parseInt(event.currentTarget.dataset.index);
        this.showImage(index);
    }

    showImage(index) {
        this.mainImageTargets.forEach(image => image.style.display = 'none');
        
        this.mainImageTargets[index].style.display = 'block';
        
        this.thumbnailTargets.forEach((thumb, i) => {
            thumb.classList.toggle('border-primary', i === index);
            thumb.classList.toggle('border-transparent', i !== index);
        });
        
        this.currentIndex = index;
    }
} 