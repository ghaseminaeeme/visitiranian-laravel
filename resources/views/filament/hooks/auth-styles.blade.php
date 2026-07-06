<style>
    /* ===== Branded login / auth pages (scoped to Filament simple layout) ===== */
    .fi-simple-layout {
        position: relative;
        min-height: 100vh;
        background:
            radial-gradient(ellipse 60% 50% at 100% 0%, rgba(45, 208, 191, 0.35), transparent 60%),
            radial-gradient(ellipse 55% 45% at 0% 100%, rgba(251, 191, 64, 0.16), transparent 60%),
            linear-gradient(135deg, #114c49 0%, #0c746e 55%, #0a9187 100%);
    }

    .fi-simple-layout::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        pointer-events: none;
    }

    .fi-simple-main {
        position: relative;
        z-index: 10;
        border-radius: 1.5rem !important;
        border: 1px solid rgba(255, 255, 255, 0.6) !important;
        background: rgba(255, 255, 255, 0.97) !important;
        box-shadow: 0 25px 60px -15px rgba(3, 46, 45, 0.55) !important;
        backdrop-filter: blur(12px);
    }

    .fi-simple-header .fi-logo {
        margin-inline: auto;
    }

    /* Refine the auth submit button */
    .fi-simple-main .fi-btn.fi-btn-color-primary {
        border-radius: 0.75rem;
        padding-block: 0.7rem;
        font-weight: 700;
    }

    /* Inputs a touch softer */
    .fi-simple-main .fi-input {
        border-radius: 0.75rem;
    }

    .dark .fi-simple-main {
        background: rgba(15, 23, 42, 0.92) !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
    }
</style>
