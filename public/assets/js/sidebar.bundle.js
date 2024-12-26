// Restaura o estado da barra lateral assim que possível
(function () {
    var savedSidebar = localStorage.getItem('sidebarHTML');
    var url = localStorage.getItem('sidebarMenuActive');

    // Restaura o HTML da barra lateral
    if (savedSidebar) {
        document.getElementById('kt_app_sidebar').innerHTML = savedSidebar;
    }

    // Marca a URL ativa
    if (url) {
        document.querySelectorAll('.menu-link').forEach(function (link) {
            link.classList.remove('active');
        });
        var activeLink = document.querySelector('a[href="' + url + '"]');
        if (activeLink) {
            activeLink.classList.add('active');
        }
    }
})();

// Após o DOM completo, inicializa os plugins
document.addEventListener('DOMContentLoaded', function () {
    KTMenu.createInstances();

    // Detecta cliques na barra lateral
    document.getElementById('kt_app_sidebar').addEventListener('click', function (e) {
        var target = e.target.closest('.menu-link');
        if (!target) return;

        // Salva URL
        var url = target.getAttribute('href');
        localStorage.setItem('sidebarMenuActive', url);

        // Ajusta o visual da opção selecionada
        document.querySelectorAll('.menu-link').forEach(function (link) {
            link.classList.remove('active');
        });
        target.classList.add('active');

        // Verifica se o item clicado NÃO está dentro de um menu-accordion
        if (!target.closest('.menu-accordion')) {
            localStorage.removeItem('sidebarHTML');
        } else {
            var sidebarHTML = document.getElementById('kt_app_sidebar').innerHTML;
            localStorage.setItem('sidebarHTML', sidebarHTML);
        }
    });

    // Limpar o cache da barra lateral
    document.getElementById('clear-sidebar').addEventListener('click', function () {
        localStorage.removeItem('sidebarHTML');
    });
});
