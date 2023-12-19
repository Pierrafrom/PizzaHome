
const listePizza1 = ["pizza1", "pizza2", "pizza3", "pizza4", "pizza5"];
const serie1_1 = 44;
const serie2_1 = 55;
const serie3_1 = 41;
const serie4_1 = 17;
const serie5_1 = 15;

document.addEventListener('DOMContentLoaded', function () {
    let options = {
        series: [serie1_1, serie2_1, serie3_1, serie4_1, serie5_1],
        chart: {
            width: 380,
            type: 'donut',
        },
        plotOptions: {
            pie: {
                startAngle: -90,
                endAngle: 270
            }
        },
        dataLabels: {
            enabled: false
        },
        fill: {
            type: 'gradient',
        },
        legend: {
            formatter: function (val, opts) {
                return listePizza1[opts.seriesIndex] + " - " + opts.w.globals.series[opts.seriesIndex];
            },
            labels:{
                colors: ['#FFFFFF', '#FFFFFF', '#FFFFFF', '#FFFFFF', '#FFFFFF'] // Couleur rouge pour tous les labels
            }
        },
        title: {
            text: 'Best-selling pizza',
            style: {
                color: '#FFFFFF' // Couleur rouge pour le titre
            }
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 200
                },
                legend: {
                    position: 'bottom'
                }
            }
        }]
    };

    const chart = new ApexCharts(document.querySelector("#cheese-chart1"), options);
    chart.render();
});


