<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    /* ===== Admin panel — mobile-first redesign ===== */
    :root {
        --vi-admin-touch: 2.75rem;
        --vi-admin-radius: 0.75rem;
    }

    .fi-body,
    .fi-body *:not([class*="icon"]):not(svg):not(path) {
        font-family: 'Vazirmatn', ui-sans-serif, system-ui, sans-serif !important;
    }

    /* --- Sidebar: always show labels, even spacing --- */
    .fi-sidebar {
        width: 17rem !important;
    }

    .fi-sidebar-header {
        padding-block: 1rem;
    }

    .fi-sidebar-nav {
        gap: 0.25rem;
        padding-inline: 0.625rem;
    }

    .fi-sidebar-group {
        margin-top: 0.5rem;
    }

    .fi-sidebar-group-label {
        font-size: 0.7rem;
        font-weight: 800;
        letter-spacing: 0.02em;
        color: rgb(100 116 139);
        padding-inline: 0.75rem;
        margin-bottom: 0.25rem;
    }

    .fi-sidebar-group-items {
        gap: 0.125rem;
    }

    .fi-sidebar-item-button {
        gap: 0.75rem !important;
        padding-inline: 0.75rem !important;
        padding-block: 0.625rem !important;
        min-height: var(--vi-admin-touch);
        border-radius: var(--vi-admin-radius);
    }

    .fi-sidebar-item-icon {
        width: 1.25rem !important;
        height: 1.25rem !important;
        flex-shrink: 0;
    }

    .fi-sidebar-item-label {
        font-size: 0.875rem;
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .fi-sidebar-item.fi-active .fi-sidebar-item-button {
        font-weight: 700;
    }

    /* Hide desktop collapse-to-icons toggle — keep text visible on large screens */
    @media (min-width: 1024px) {
        .fi-sidebar-close-collapse-button,
        .fi-topbar-close-sidebar-btn {
            display: none !important;
        }
    }

    /* --- Top bar --- */
    .fi-topbar {
        min-height: 3.5rem;
        padding-inline: 0.75rem;
    }

    .fi-topbar-open-sidebar-btn {
        min-width: var(--vi-admin-touch);
        min-height: var(--vi-admin-touch);
    }

    /* --- Page content --- */
    .fi-main {
        padding-inline: 0.75rem;
    }

    @media (min-width: 768px) {
        .fi-main {
            padding-inline: 1.25rem;
        }
    }

    .fi-header-heading {
        font-size: 1.25rem;
        font-weight: 800;
    }

    /* --- Forms --- */
    .fi-fo-field-wrp-label label {
        font-weight: 600;
        font-size: 0.875rem;
    }

    .fi-input-wrp,
    .fi-select-input,
    .fi-fo-textarea textarea {
        border-radius: var(--vi-admin-radius) !important;
        min-height: 2.75rem;
        font-size: 1rem;
    }

    .fi-fo-textarea textarea {
        min-height: 5rem;
    }

    .fi-section {
        border-radius: 1rem !important;
    }

    .fi-section-header {
        padding-block: 0.875rem;
    }

    .fi-section-header-heading {
        font-size: 1rem;
        font-weight: 700;
    }

    .fi-fo-repeater-item {
        border-radius: var(--vi-admin-radius);
    }

    /* --- Buttons: larger touch targets on mobile --- */
    .fi-btn {
        min-height: 2.75rem;
        border-radius: var(--vi-admin-radius);
        font-weight: 600;
    }

    .fi-ac-btn-action {
        min-height: 2.5rem;
    }

    /* --- Tables --- */
    .fi-ta-table {
        font-size: 0.875rem;
    }

    .fi-ta-cell {
        padding-block: 0.75rem !important;
    }

    .fi-ta-header-cell {
        font-weight: 700;
        font-size: 0.8rem;
    }

    .fi-ta-actions-cell .fi-link {
        min-height: 2.25rem;
        display: inline-flex;
        align-items: center;
    }

    /* Stack table on very small screens */
    @media (max-width: 639px) {
        .fi-ta-content {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .fi-ta-table {
            min-width: 36rem;
        }
    }

    /* --- File upload: compact, whole area tappable --- */
    .vi-image-upload .filepond--root {
        cursor: pointer;
        margin-bottom: 0;
    }

    .vi-image-upload .filepond--drop-label {
        cursor: pointer;
        min-height: 6.5rem;
        border: 2px dashed rgb(203 213 225);
        border-radius: var(--vi-admin-radius);
        background: rgb(248 250 252);
        transition: border-color 0.15s, background 0.15s;
    }

    .vi-image-upload .filepond--drop-label:hover,
    .vi-image-upload .filepond--drop-label:focus-within {
        border-color: rgb(20 184 166);
        background: rgb(240 253 250);
    }

    .vi-image-upload .filepond--drop-label label {
        cursor: pointer;
        width: 100%;
        padding: 1rem;
        font-size: 0.875rem;
        font-weight: 600;
        color: rgb(71 85 105);
    }

    .vi-image-upload .filepond--panel-root {
        background: transparent;
        border-radius: var(--vi-admin-radius);
    }

    .vi-image-upload .filepond--item-panel {
        border-radius: var(--vi-admin-radius);
    }

    .vi-image-upload--avatar .filepond--drop-label {
        min-height: 5.5rem;
        max-width: 8rem;
        margin-inline: auto;
        border-radius: 9999px;
    }

    @media (min-width: 768px) {
        .vi-image-upload:not(.vi-image-upload--avatar) .filepond--root {
            max-width: 18rem;
        }

        .vi-image-upload:not(.vi-image-upload--avatar) .filepond--drop-label {
            min-height: 5.5rem;
        }
    }

    /* --- Modals on mobile --- */
    @media (max-width: 639px) {
        .fi-modal-window {
            margin: 0.5rem;
            max-height: calc(100dvh - 1rem);
            border-radius: 1rem;
        }
    }

    /* --- Dashboard stats --- */
    .fi-wi-stats-overview-stat {
        border-radius: 1rem;
    }

    .fi-wi-stats-overview-stat-value {
        font-weight: 800;
    }

    /* --- Tabs (doctor form) --- */
    .fi-fo-tabs-tab {
        min-height: 2.75rem;
        font-weight: 600;
    }

    /* --- RTL sidebar alignment fix --- */
    html[dir="rtl"] .fi-sidebar-item-button {
        flex-direction: row;
    }

    html[dir="rtl"] .fi-sidebar-nav-groups {
        text-align: right;
    }
</style>
