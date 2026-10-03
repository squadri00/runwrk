<script>
    (function () {
        try {
            var t = localStorage.getItem('rw-theme');
            if (t === 'dark' || (!t && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        } catch (e) {}
        document.documentElement.classList.add('theme-ready');
    })();
</script>
<?php /**PATH D:\xampp\htdocs\runwrk\resources\views/partials/theme-head.blade.php ENDPATH**/ ?>