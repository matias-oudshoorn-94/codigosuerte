/* Panel de administracion: comportamiento comun (sin dependencias). */
(function () {
    'use strict';

    // Menu lateral en celular
    var boton = document.getElementById('pn-menu-movil');
    var velo = document.getElementById('pn-velo');
    function cerrarMenu() { document.body.classList.remove('menu-abierto'); }
    if (boton) boton.addEventListener('click', function () { document.body.classList.toggle('menu-abierto'); });
    if (velo) velo.addEventListener('click', cerrarMenu);
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') cerrarMenu(); });

    // Confirmacion antes de acciones destructivas: <form data-confirmar="¿Seguro?">
    document.addEventListener('submit', function (e) {
        var mensaje = e.target.getAttribute('data-confirmar');
        if (mensaje && !window.confirm(mensaje)) e.preventDefault();
    }, true);

    // Filtros que se aplican solos al cambiar: <select data-autoenviar>
    document.addEventListener('change', function (e) {
        if (e.target.hasAttribute('data-autoenviar') && e.target.form) e.target.form.submit();
    });

    // Evitar doble envio de formularios
    document.addEventListener('submit', function (e) {
        if (e.defaultPrevented) return;
        var enviar = e.target.querySelector('button[type=submit]:not([data-sin-bloqueo])');
        if (enviar) setTimeout(function () { enviar.disabled = true; }, 0);
    });
})();
