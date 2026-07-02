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

Alpine.data('searchableSelect', () => ({
    open: false,
    query: '',
    options: [],
    value: '',
    selectedLabel: '',
    placeholder: 'انتخاب کنید…',

    init() {
        const select = this.$refs.select;

        this.options = Array.from(select.options).map((option) => ({
            value: option.value,
            label: option.textContent.trim(),
        }));

        const emptyOption = this.options.find((option) => option.value === '');
        this.placeholder = emptyOption?.label || this.placeholder;
        this.value = select.value;
        this.syncLabel();
    },

    get filteredOptions() {
        const term = this.query.trim().toLowerCase();

        if (!term) {
            return this.options;
        }

        return this.options.filter((option) => option.label.toLowerCase().includes(term));
    },

    syncLabel() {
        const selected = this.options.find((option) => option.value === this.value);
        this.selectedLabel = selected && selected.value !== '' ? selected.label : '';
    },

    select(option) {
        this.value = option.value;
        this.$refs.select.value = option.value;
        this.$refs.select.dispatchEvent(new Event('change', { bubbles: true }));
        this.syncLabel();
        this.close();
    },

    toggle() {
        this.open = !this.open;

        if (this.open) {
            this.query = '';
            this.$nextTick(() => this.$refs.search?.focus());
        }
    },

    close() {
        this.open = false;
        this.query = '';
    },
}));

Alpine.start();
