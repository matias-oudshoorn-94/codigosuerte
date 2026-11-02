/*
 * Clima de Mar del Plata (Open-Meteo: gratis, sin clave).
 *
 * Uso:  Clima.obtener().then(function (datos) { ... });
 *       Clima.icono(codigo, esDeDia)  -> SVG
 *       Clima.descripcion(codigo)     -> texto en español
 *
 * Los datos se guardan 15 minutos en el navegador para no consultar
 * el servicio en cada página. Si falla, la promesa se rechaza y quien
 * la usa simplemente no muestra nada.
 */
(function (window) {
    'use strict';

    var URL = 'https://api.open-meteo.com/v1/forecast'
        + '?latitude=-38.0055&longitude=-57.5426'
        + '&current=temperature_2m,apparent_temperature,relative_humidity_2m,is_day,weather_code,wind_speed_10m'
        + '&daily=temperature_2m_max,temperature_2m_min,precipitation_probability_max'
        + '&hourly=temperature_2m,weather_code,precipitation_probability,is_day'
        + '&forecast_hours=12&forecast_days=1'
        + '&timezone=America%2FArgentina%2FBuenos_Aires';
    var CLAVE = 'clima_mdp_v1';
    var DURACION = 15 * 60 * 1000;
    var pedido = null;

    function leerCache() {
        try {
            var guardado = JSON.parse(window.localStorage.getItem(CLAVE));
            if (guardado && Date.now() - guardado.cuando < DURACION) return guardado.datos;
        } catch (e) { /* sin localStorage o dato roto: se vuelve a pedir */ }
        return null;
    }

    function guardarCache(datos) {
        try { window.localStorage.setItem(CLAVE, JSON.stringify({ cuando: Date.now(), datos: datos })); } catch (e) {}
    }

    function obtener() {
        if (pedido) return pedido;

        var cache = leerCache();
        if (cache) return (pedido = Promise.resolve(cache));

        var control = window.AbortController ? new AbortController() : null;
        var espera = setTimeout(function () { if (control) control.abort(); }, 6000);

        pedido = fetch(URL, control ? { signal: control.signal } : {})
            .then(function (r) {
                if (!r.ok) throw new Error('Clima: HTTP ' + r.status);
                return r.json();
            })
            .then(function (json) {
                clearTimeout(espera);
                var datos = {
                    temperatura: json.current.temperature_2m,
                    sensacion: json.current.apparent_temperature,
                    humedad: json.current.relative_humidity_2m,
                    viento: json.current.wind_speed_10m,
                    codigo: json.current.weather_code,
                    esDeDia: json.current.is_day === 1,
                    maxima: json.daily.temperature_2m_max[0],
                    minima: json.daily.temperature_2m_min[0],
                    lluvia: json.daily.precipitation_probability_max[0],
                    horas: json.hourly.time.map(function (hora, i) {
                        return {
                            hora: hora.slice(11, 16),
                            temperatura: json.hourly.temperature_2m[i],
                            codigo: json.hourly.weather_code[i],
                            lluvia: json.hourly.precipitation_probability[i],
                            esDeDia: json.hourly.is_day[i] === 1
                        };
                    })
                };
                guardarCache(datos);
                return datos;
            })
            .catch(function (error) {
                clearTimeout(espera);
                pedido = null; // permitir reintentar en la próxima llamada
                throw error;
            });

        return pedido;
    }

    // Códigos del clima de la Organización Meteorológica Mundial (WMO)
    function descripcion(codigo) {
        if (codigo === 0) return 'Despejado';
        if (codigo === 1) return 'Mayormente despejado';
        if (codigo === 2) return 'Parcialmente nublado';
        if (codigo === 3) return 'Nublado';
        if (codigo === 45 || codigo === 48) return 'Niebla';
        if (codigo >= 51 && codigo <= 57) return 'Llovizna';
        if (codigo === 61 || codigo === 80) return 'Lluvia leve';
        if (codigo === 63 || codigo === 81) return 'Lluvia';
        if (codigo === 65 || codigo === 82) return 'Lluvia fuerte';
        if (codigo === 66 || codigo === 67) return 'Lluvia helada';
        if ((codigo >= 71 && codigo <= 77) || codigo === 85 || codigo === 86) return 'Nieve';
        if (codigo === 95) return 'Tormenta';
        if (codigo === 96 || codigo === 99) return 'Tormenta con granizo';
        return '';
    }

    function tipo(codigo) {
        if (codigo <= 1) return 'despejado';
        if (codigo === 2) return 'parcial';
        if (codigo === 3) return 'nublado';
        if (codigo === 45 || codigo === 48) return 'niebla';
        if ((codigo >= 71 && codigo <= 77) || codigo === 85 || codigo === 86) return 'nieve';
        if (codigo >= 95) return 'tormenta';
        return 'lluvia';
    }

    var NUBE = '<path d="M7 19h10.5a4 4 0 0 0 .4-7.98A6 6 0 0 0 6.3 12.1 3.5 3.5 0 0 0 7 19z"/>';
    var SOL_RAYOS = '<path d="M12 2.5v2M12 19.5v2M4.6 4.6l1.4 1.4M18 18l1.4 1.4M2.5 12h2M19.5 12h2M4.6 19.4L6 18M18 6l1.4-1.4"/>';

    function icono(codigo, esDeDia) {
        var cuerpo;
        switch (tipo(codigo)) {
            case 'despejado':
                cuerpo = esDeDia
                    ? '<g style="color:#f5b544"><circle cx="12" cy="12" r="4.2"/>' + SOL_RAYOS + '</g>'
                    : '<path style="color:#c9d2e3" d="M20 14.5A8 8 0 0 1 9.5 4a8 8 0 1 0 10.5 10.5z"/>';
                break;
            case 'parcial':
                cuerpo = (esDeDia
                    ? '<g style="color:#f5b544"><circle cx="8.5" cy="8.5" r="3"/><path d="M8.5 2.5v1.2M2.5 8.5h1.2M4.3 4.3l.9.9M12.7 4.3l-.9.9"/></g>'
                    : '<path style="color:#c9d2e3" d="M12.5 7.5A4.5 4.5 0 0 1 6.7 3.2a4.5 4.5 0 1 0 5.8 4.3z"/>')
                    + '<path d="M8.5 20h9a3.5 3.5 0 0 0 .35-6.98A5 5 0 0 0 8.1 13.9 3 3 0 0 0 8.5 20z"/>';
                break;
            case 'nublado':
                cuerpo = NUBE;
                break;
            case 'niebla':
                cuerpo = '<path d="M4 9h16M3 13h18M5 17h14"/>';
                break;
            case 'nieve':
                cuerpo = '<path d="M7 15h10.5a4 4 0 0 0 .4-7.98A6 6 0 0 0 6.3 8.1 3.5 3.5 0 0 0 7 15z"/><path d="M8 19h.01M12 20h.01M16 19h.01" stroke-width="2.6"/>';
                break;
            case 'tormenta':
                cuerpo = '<path d="M7 15h10.5a4 4 0 0 0 .4-7.98A6 6 0 0 0 6.3 8.1 3.5 3.5 0 0 0 7 15z"/><path style="color:#f5b544" d="M12.5 15.5l-2 3.5h3l-2 3.5"/>';
                break;
            default: // lluvia y llovizna
                cuerpo = '<path d="M7 15h10.5a4 4 0 0 0 .4-7.98A6 6 0 0 0 6.3 8.1 3.5 3.5 0 0 0 7 15z"/><path style="color:#5fa8e8" d="M8.5 18l-1 2.5M12.5 18l-1 2.5M16.5 18l-1 2.5"/>';
        }
        return '<svg class="clima-icono" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' + cuerpo + '</svg>';
    }

    window.Clima = { obtener: obtener, icono: icono, descripcion: descripcion };
})(window);
