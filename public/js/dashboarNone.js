document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.getElementById('toggleSidebar');
    const sidebar = document.getElementById('sidebar');
    const main = document.querySelector('.main');
    const backdrop = document.getElementById('sidebarBackdrop');

    if (!toggleBtn || !sidebar || !main) {
        return;
    }

    function isMobile() {
        return window.innerWidth <= 768;
    }

    function closeMobileSidebar() {
        sidebar.classList.remove('mobile-open');
        if (backdrop) {
            backdrop.classList.remove('show');
        }
    }

    function openMobileSidebar() {
        sidebar.classList.add('mobile-open');
        if (backdrop) {
            backdrop.classList.add('show');
        }
    }

    toggleBtn.addEventListener('click', function () {
        if (isMobile()) {
            if (sidebar.classList.contains('mobile-open')) {
                closeMobileSidebar();
            } else {
                openMobileSidebar();
            }
        } else {
            sidebar.classList.toggle('hidden');
            main.classList.toggle('expanded');
            closeMobileSidebar();
        }
    });

    if (backdrop) {
        backdrop.addEventListener('click', function () {
            closeMobileSidebar();
        });
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && isMobile() && sidebar.classList.contains('mobile-open')) {
            closeMobileSidebar();
        }
    });

    const sidebarLinks = sidebar.querySelectorAll('.sidebar-link');
    sidebarLinks.forEach(function (link) {
        link.addEventListener('click', function () {
            if (isMobile()) {
                closeMobileSidebar();
            }
        });
    });

    window.addEventListener('resize', function () {
        if (!isMobile()) {
            closeMobileSidebar();
        } else {
            sidebar.classList.remove('hidden');
            main.classList.remove('expanded');
        }
    });
});
