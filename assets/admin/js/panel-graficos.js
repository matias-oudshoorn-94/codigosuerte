/*
 * Graficos del panel (sobre Chart.js 4).
 *
 *   PanelGraficos.barras('id-canvas', etiquetas, [{ nombre, datos }], { formato: 'pesos' })
 *   PanelGraficos.lineas('id-canvas', etiquetas, [{ nombre, datos }], { formato: 'numero' })
 *
 * Colores: paleta validada (azul, naranja, aqua, amarillo) en orden fijo, nunca ciclada.
 * Hasta 4 series en barras/lineas; mas de 4 se agrupan en "Otras".
 * Una sola serie no lleva leyenda (el titulo de la tarjeta la nombra).
 */
(function (window) {
    'use strict';

    var SERIES = ['#2a78d6', '#eb6834', '#1baf7a', '#eda100'];
    var TEXTO = '#5f6b78';
    var GRILLA = '#eef1f4';

    if (window.Chart) {
        Chart.defaults.font.family = '"Open Sans", "Helvetica Neue", Helvetica, Arial, sans-serif';
        Chart.defaults.font.size = 12;
        Chart.defaults.color = TEXTO;
        Chart.defaults.maintainAspectRatio = false;
        Chart.defaults.animation.duration = 300;
    }

    var pesos = new Intl.NumberFormat('es-AR', { maximumFractionDigits: 0 });
    var decimal = new Intl.NumberFormat('es-AR', { maximumFractionDigits: 1 });

    function formatear(valor, formato) {
        if (formato === 'pesos') return '$ ' + pesos.format(valor);
        if (formato === 'porcentaje') return decimal.format(valor) + '%';
        return pesos.format(valor);
    }

    // Eje abreviado: $ 1,2 M · $ 350 mil
    function abreviar(valor, formato) {
        var abs = Math.abs(valor), texto;
        if (abs >= 1e6) texto = decimal.format(valor / 1e6) + ' M';
        else if (abs >= 1e4) texto = pesos.format(valor / 1e3) + ' mil';
        else texto = pesos.format(valor);
        if (formato === 'porcentaje') return decimal.format(valor) + '%';
        return formato === 'pesos' ? '$ ' + texto : texto;
    }

    function opcionesBase(formato, conLeyenda, horizontal) {
        var ejeValor = {
            beginAtZero: true,
            border: { display: false },
            grid: { color: GRILLA, drawTicks: false },
            ticks: { padding: 8, maxTicksLimit: 6, callback: function (v) { return abreviar(v, formato); } }
        };
        var ejeCategoria = {
            border: { color: '#d4dbe3' },
            grid: { display: false },
            ticks: { padding: 6, autoSkip: true, maxRotation: 0, autoSkipPadding: 12 }
        };
        return {
            responsive: true,
            interaction: { mode: 'index', intersect: false, axis: horizontal ? 'y' : 'x' },
            indexAxis: horizontal ? 'y' : 'x',
            scales: horizontal ? { x: ejeValor, y: ejeCategoria } : { x: ejeCategoria, y: ejeValor },
            plugins: {
                legend: {
                    display: conLeyenda,
                    position: 'top',
                    align: 'start',
                    labels: { usePointStyle: true, pointStyle: 'rectRounded', boxWidth: 10, boxHeight: 10, padding: 16, color: '#1f2933' }
                },
                tooltip: {
                    backgroundColor: '#1f2329',
                    titleColor: '#ffffff',
                    bodyColor: '#e7eaee',
                    padding: 10,
                    cornerRadius: 6,
                    boxPadding: 4,
                    usePointStyle: true,
                    callbacks: {
                        label: function (ctx) {
                            var valor = horizontal ? ctx.parsed.x : ctx.parsed.y;
                            return ' ' + (ctx.dataset.label ? ctx.dataset.label + ': ' : '') + formatear(valor, formato);
                        }
                    }
                }
            }
        };
    }

    function lienzo(id) {
        var el = document.getElementById(id);
        return el ? el.getContext('2d') : null;
    }

    function barras(id, etiquetas, series, opciones) {
        opciones = opciones || {};
        var ctx = lienzo(id);
        if (!ctx || !window.Chart) return null;
        var horizontal = !!opciones.horizontal;
        var config = opcionesBase(opciones.formato, series.length > 1, horizontal);
        if (opciones.apilado) {
            config.scales.x.stacked = true;
            config.scales.y.stacked = true;
        }
        return new Chart(ctx, {
            type: 'bar',
            data: {
                labels: etiquetas,
                datasets: series.map(function (s, i) {
                    return {
                        label: series.length > 1 ? s.nombre : (s.nombre || ''),
                        data: s.datos,
                        backgroundColor: s.color || SERIES[i],
                        hoverBackgroundColor: s.color || SERIES[i],
                        borderRadius: opciones.apilado ? 0 : 4,
                        borderSkipped: 'start',
                        // separacion de 2px del color de la superficie entre barras vecinas
                        borderColor: '#ffffff',
                        borderWidth: opciones.apilado ? { top: 2 } : 0,
                        maxBarThickness: 36,
                        categoryPercentage: 0.8,
                        barPercentage: series.length > 1 ? 0.92 : 0.85
                    };
                })
            },
            options: config
        });
    }

    function lineas(id, etiquetas, series, opciones) {
        opciones = opciones || {};
        var ctx = lienzo(id);
        if (!ctx || !window.Chart) return null;
        return new Chart(ctx, {
            type: 'line',
            data: {
                labels: etiquetas,
                datasets: series.map(function (s, i) {
                    var color = s.color || SERIES[i];
                    return {
                        label: series.length > 1 ? s.nombre : (s.nombre || ''),
                        data: s.datos,
                        borderColor: color,
                        backgroundColor: color,
                        borderWidth: 2,
                        tension: 0.25,
                        pointRadius: etiquetas.length > 40 ? 0 : 3,
                        pointHoverRadius: 5,
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        fill: false,
                        spanGaps: true
                    };
                })
            },
            options: opcionesBase(opciones.formato, series.length > 1, false)
        });
    }

    window.PanelGraficos = { barras: barras, lineas: lineas, formatear: formatear, SERIES: SERIES };
})(window);
