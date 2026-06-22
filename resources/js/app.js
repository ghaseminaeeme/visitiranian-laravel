import Alpine from 'alpinejs';
import { toJalaali, toGregorian, jalaaliMonthLength, isValidJalaaliDate } from 'jalaali-js';
import Cropper from 'cropperjs';

window.Alpine = Alpine;
window.Cropper = Cropper;
window.jalaali = { toJalaali, toGregorian, jalaaliMonthLength, isValidJalaaliDate };

Alpine.data('mobileNav', () => ({
    open: false,
    toggle() {
        this.open = !this.open;
    },
    close() {
        this.open = false;
    },
}));

Alpine.data('heroSlider', (count = 1) => ({
    current: 0,
    total: count,
    interval: null,
    start() {
        if (this.total <= 1) return;
        this.interval = setInterval(() => this.next(), 6000);
    },
    stop() {
        if (this.interval) clearInterval(this.interval);
    },
    next() {
        this.current = (this.current + 1) % this.total;
    },
    prev() {
        this.current = (this.current - 1 + this.total) % this.total;
    },
    goTo(index) {
        this.current = index;
    },
}));

Alpine.data('appointmentBooking', (slotsUrl) => ({
    date: '',
    slots: [],
    selectedSlot: '',
    loading: false,
    async loadSlots() {
        if (!this.date) return;
        this.loading = true;
        this.selectedSlot = '';
        try {
            const response = await fetch(`${slotsUrl}?date=${this.date}`);
            const data = await response.json();
            this.slots = data.slots ?? [];
        } catch {
            this.slots = [];
        } finally {
            this.loading = false;
        }
    },
}));

Alpine.start();
