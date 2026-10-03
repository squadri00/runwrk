/* Runwrk marketing site: tiny vanilla JS (menu, light/dark, back to top, counters). No libraries. */
(function () {
    var root = document.documentElement;

    // Smooth colour changes only after first paint, so the page never flashes on load.
    setTimeout(function () { root.classList.add('rw-ready'); }, 60);

    var burger = document.querySelector('.rw-burger');
    var menu = document.getElementById('rw-menu');
    if (burger && menu) {
        burger.addEventListener('click', function () {
            var open = menu.classList.toggle('open');
            burger.setAttribute('aria-expanded', open);
        });
    }

    var toggle = document.querySelector('.rw-theme');
    if (toggle) {
        toggle.addEventListener('click', function () {
            var next = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            root.setAttribute('data-theme', next);
            root.setAttribute('data-bs-theme', next);
            try { localStorage.setItem('rw-site-theme', next); } catch (e) {}
        });
    }

    var top = document.querySelector('.rw-top');
    if (top) {
        var toggleTop = function () { top.classList.toggle('show', window.scrollY > 500); };
        window.addEventListener('scroll', toggleTop, { passive: true });
        toggleTop();
        top.addEventListener('click', function () { window.scrollTo({ top: 0, behavior: 'smooth' }); });
    }

    // YouTube loads only when the visitor presses play (keeps the page fast and private).
    document.querySelectorAll('.rw-video[data-video]').forEach(function (box) {
        var btn = box.querySelector('.rw-video-play');
        btn.addEventListener('click', function () {
            var f = document.createElement('iframe');
            f.src = 'https://www.youtube-nocookie.com/embed/' + box.getAttribute('data-video') + '?autoplay=1&rel=0&modestbranding=1';
            f.title = 'Runwrk intro video';
            f.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share';
            f.allowFullscreen = true;
            box.appendChild(f);
            btn.remove();
        });
    });

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
