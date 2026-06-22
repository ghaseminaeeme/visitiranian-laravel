<style>
    html {
        direction: rtl;
    }

    html[dir="rtl"] .fi-sidebar-nav-groups {
        text-align: right;
    }
</style>
<link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#0F766E">
<script>
    document.documentElement.setAttribute('dir', 'rtl');
    document.documentElement.setAttribute('lang', 'fa');
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw-admin.js').catch(() => {});
    }
</script>
