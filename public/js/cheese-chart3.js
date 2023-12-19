
const Wine = ["wine1", "wine2", "wine3", "wine4", "wine5"];
const serie1_3 = 44;
const serie2_3 = 55;
const serie3_3 = 41;
const serie4_3 = 17;
const serie5_3 = 15;

document.addEventListener('DOMContentLoaded', function () {
    let options = {
        series: [serie1_3, serie2_3, serie3_3, serie4_3, serie5_3],
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
                return Wine[opts.seriesIndex] + " - " + opts.w.globals.series[opts.seriesIndex];
            },
            labels:{
                colors: ['#FFFFFF', '#FFFFFF', '#FFFFFF', '#FFFFFF', '#FFFFFF'] // Couleur rouge pour tous les labels
            }
        },
        title: {
            text: 'Best-selling Wine',
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

    const chart = new ApexCharts(document.querySelector("#cheese-chart3"), options);
    chart.render();
});


