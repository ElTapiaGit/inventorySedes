document.getElementById('toggleSidebar').addEventListener('click', function () {
    const sidebar = document.getElementById('sidebar');
    const main = document.querySelector('.main');
    
    // Alternar la clase 'hidden' para el sidebar
    sidebar.classList.toggle('hidden');
    
    // Alternar la clase 'expanded' para el contenido principal
    main.classList.toggle('expanded');

    // Para pantallas pequeñas, aplicar la clase active
    if (window.innerWidth <= 768) {
        sidebar.classList.toggle('active');
    }
});
