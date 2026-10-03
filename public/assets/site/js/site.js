/* Runwrk marketing site: tiny vanilla JS (menu + counters). No libraries. */
(function () {
    var burger = document.querySelector('.rw-burger');
    var menu = document.getElementById('rw-menu');
    if (burger && menu) {
        burger.addEventListener('click', function () {
            var open = menu.classList.toggle('open');
            burger.setAttribute('aria-expanded', open);
        });
    }

    var nums = document.querySelectorAll('[data-count]');
    if (!nums.length || !('IntersectionObserver' in window)) { return; }
    var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (e) {
            if (!e.isIntersecting) { return; }
            io.unobserve(e.target);
            var el = e.target, to = parseInt(el.getAttribute('data-count'), 10), start = null;
            function step(t) {
                start = start || t;
                var p = Math.min((t - start) / 1200, 1);
                el.textContent = Math.round(to * p);
                if (p < 1) { requestAnimationFrame(step); }
            }
            requestAnimationFrame(step);
        });
    }, { threshold: 0.4 });
    nums.forEach(function (n) { io.observe(n); });
})();
