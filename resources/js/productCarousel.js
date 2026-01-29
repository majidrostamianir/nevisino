export default function (initialProducts = []) {
    return {
        products: initialProducts,
        currentIndex: 0,
        progressFrame: null,
        progressWidth: 'width: 0%',

        init() {
            if (this.products.length) this.startProgress()
        },

        next() {
            this.currentIndex = (this.currentIndex + 1) % this.products.length
            this.resetProgress()
        },

        resetProgress() {
            this.progressWidth = 'width: 0%'
            cancelAnimationFrame(this.progressFrame)
            this.startProgress()
        },

        startProgress() {
            const duration = 4000
            const start = performance.now()

            const step = (now) => {
                const progress = Math.min((now - start) / duration, 1)
                this.progressWidth = `width: ${progress * 100}%`

                if (progress < 1) {
                    this.progressFrame = requestAnimationFrame(step)
                } else {
                    this.next()
                }
            }

            this.progressFrame = requestAnimationFrame(step)
        },

        formatPrice(price) {
            return Number(price).toLocaleString('fa-IR') + ' تومان'
        },
    }
}
