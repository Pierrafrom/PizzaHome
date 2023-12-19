
const Dessert = ["dessert1", "dessert2", "dessert3", "dessert4", "dessert5"];
const serie1_2 = 44;
const serie2_2 = 55;
const serie3_2 = 41;
const serie4_2 = 17;
const serie5_2 = 15;

document.addEventListener('DOMContentLoaded', function () {
    let options = {
        series: [serie1_2, serie2_2, serie3_2, serie4_2, serie5_2],
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
                return Dessert[opts.seriesIndex] + " - " + opts.w.globals.series[opts.seriesIndex];
            },
            labels:{
                colors: ['#FFFFFF', '#FFFFFF', '#FFFFFF', '#FFFFFF', '#FFFFFF'] // Couleur rouge pour tous les labels
            }
        },
        title: {
            text: 'Best-selling Dessert',
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

    const chart = new ApexCharts(document.querySelector("#cheese-chart2"), options);
    chart.render();
});


