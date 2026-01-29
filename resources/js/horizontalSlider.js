export default function () {
    return {
        current: 0,
        itemWidth: 0,

        init() {
            this.$nextTick(() => {
                this.itemWidth = this.$refs.item0.offsetWidth + 16
            })
        },

        next(total) {
            if (this.current < total - 1) this.current++
        },

        prev() {
            if (this.current > 0) this.current--
        },
    }
}
