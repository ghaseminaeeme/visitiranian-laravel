import Alpine from 'alpinejs';
import { toJalaali, toGregorian, jalaaliMonthLength, isValidJalaaliDate } from 'jalaali-js';
import Cropper from 'cropperjs';
import TomSelect from 'tom-select';

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

function initSearchableSelects(root = document) {
    root.querySelectorAll('select.select-field:not(.tomselected)').forEach((element) => {
        new TomSelect(element, {
            allowEmptyOption: true,
            create: false,
            maxOptions: null,
            sortField: { field: 'text', direction: 'asc' },
            plugins: ['dropdown_input'],
            render: {
                no_results: () => '<div class="no-results px-3 py-2 text-sm text-slate-500">موردی یافت نشد</div>',
            },
        });
    });
}

document.addEventListener('DOMContentLoaded', () => initSearchableSelects());

Alpine.start();
