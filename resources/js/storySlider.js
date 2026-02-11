window.storySlider = function (initialIndex) {
    return {
        currentIndex: initialIndex,
        progress: 0,
        interval: null,
        duration: 3500,
        loading: true,

        startTimer() {
            if (this.interval) clearInterval(this.interval);

            let startTime = Date.now();

            this.interval = setInterval(() => {
                let elapsed = Date.now() - startTime;
                this.progress = (elapsed / this.duration) * 100;

                if (this.progress >= 100) {
                    clearInterval(this.interval);
                    this.$wire.nextItem();
                    return;
                }
            }, 30);
        },

        imageLoaded() {
            this.loading = false;
            this.startTimer();
        },

        init() {
            this.loading = true;

            this.$watch('currentIndex', () => {
                if (this.interval) clearInterval(this.interval);

                this.progress = 0;
                this.loading = true;

                this.$nextTick(() => {
                    let img = this.$el.querySelector('img[x-ref="mainImage"]');
                    if (img && img.complete) {
                        this.imageLoaded();
                    }
                });
            });
        },
    }
}
