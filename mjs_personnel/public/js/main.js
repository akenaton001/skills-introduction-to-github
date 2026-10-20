document.addEventListener("DOMContentLoaded", function() {
    const currentPath = window.location.pathname;
    const adminLinks = document.querySelectorAll('.admin-submenu a'); // Ajustez la classe selon votre header
    
    // Si on est sur une page du menu Admin RH, on force l'ouverture
    adminLinks.forEach(link => {
        if (link.getAttribute('href') === currentPath) {
            // On trouve le parent (le menu parent) et on lui ajoute la classe 'show'
            const parentMenu = link.closest('.nav-item.dropdown'); 
            if (parentMenu) {
                parentMenu.classList.add('show');
                parentMenu.querySelector('.dropdown-menu').classList.add('show');
            }
        }
    });
});