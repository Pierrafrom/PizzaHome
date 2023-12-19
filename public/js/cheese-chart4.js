
const Cocktail = ["cocktail1", "cocktail2", "cocktail3", "cocktail4", "cocktail5"];
const serie1_4 = 44;
const serie2_4 = 55;
const serie3_4 = 41;
const serie4_4 = 17;
const serie5_4 = 15;

document.addEventListener('DOMContentLoaded', function () {
    let options = {
        series: [serie1_4, serie2_4, serie3_4, serie4_4, serie5_4],
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
                return Cocktail[opts.seriesIndex] + " - " + opts.w.globals.series[opts.seriesIndex];
            },
            labels:{
                colors: ['#FFFFFF', '#FFFFFF', '#FFFFFF', '#FFFFFF', '#FFFFFF'] // Couleur rouge pour tous les labels
            }
        },
        title: {
            text: 'Best-selling Cocktail',
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

    const chart = new ApexCharts(document.querySelector("#cheese-chart4"), options);
    chart.render();
});


