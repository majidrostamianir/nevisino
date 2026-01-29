export default function () {
    return {
        slides: [],
        currentIndex: 0,
        interval: null,

        init() {
            this.startAutoPlay()
        },

        next() {
            this.currentIndex = (this.currentIndex + 1) % this.slides.length
            this.resetInterval()
        },

        prev() {
            this.currentIndex =
                this.currentIndex === 0
                    ? this.slides.length - 1
                    : this.currentIndex - 1
            this.resetInterval()
        },

        resetInterval() {
            clearInterval(this.interval)
            this.startAutoPlay()
        },

        startAutoPlay() {
            this.interval = setInterval(() => this.next(), 4500)
        },
    }
}
