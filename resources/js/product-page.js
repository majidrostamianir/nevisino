const FA_DIGITS = '۰۱۲۳۴۵۶۷۸۹';

const toFa = (value) => String(value).replace(/[0-9]/g, (d) => FA_DIGITS[d]);
const toEn = (value) => String(value).replace(/[۰-۹]/g, (d) => FA_DIGITS.indexOf(d)).replace(/\D/g, '');

document.addEventListener('alpine:init', () => {
    // فیلد تعداد: نمایش فارسی، ولی به سرور عدد انگلیسی (int) فرستاده می‌شود
    Alpine.data('quantityInput', () => ({
        toFa,

        onInput(event) {
            const digits = toEn(event.target.value);
            event.target.value = toFa(digits);

            if (digits !== '') {
                this.$wire.$set('quantity', parseInt(digits, 10));
            }
        },

        onBlur(event) {
            event.target.value = toFa(this.$wire.quantity);
        },
    }));

    // زوم تصویر محصول (هاور در دسکتاپ، تپ در موبایل)
    Alpine.data('imageZoom', () => ({
        isZoomed: false,
        zoomOnHover: false,
        originX: '50%',
        originY: '50%',
        lastTap: 0,

        init() {
            this.zoomOnHover = window.matchMedia('(hover: hover)').matches;
        },

        setZoomOrigin(event) {
            const point = event.touches?.[0] ?? event.changedTouches?.[0] ?? event;
            if (point.clientX === undefined) return;

            const rect = this.$el.getBoundingClientRect();
            const toPercent = (value) => `${Math.max(0, Math.min(100, value))}%`;

            this.originX = toPercent(((point.clientX - rect.left) / rect.width) * 100);
            this.originY = toPercent(((point.clientY - rect.top) / rect.height) * 100);
        },

        onMouseEnter() {
            if (this.zoomOnHover) this.isZoomed = true;
        },

        onMouseMove(event) {
            if (this.zoomOnHover && this.isZoomed) this.setZoomOrigin(event);
        },

        onMouseLeave() {
            if (this.zoomOnHover) this.isZoomed = false;
        },

        onTap(event) {
            if (this.zoomOnHover) return;

            const now = Date.now();
            const isDoubleTap = this.isZoomed && now - this.lastTap < 500;

            this.setZoomOrigin(event);
            this.isZoomed = !this.isZoomed;
            this.lastTap = isDoubleTap ? 0 : now;
        },
    }));
});