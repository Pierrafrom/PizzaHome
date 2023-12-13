const listePizza = ["pizza1","pizza2","pizza3","pizza4","pizza5","pizza6","pizza7","pizza8","pizza9","pizza10"];
const dessert1 = [13,30,32];
const pizza2 = [17,21,23];
const pizza3 = [15,26,28];
const pizza4 = [16,26,19];
const pizza5 = [12,25,27];
const pizza6 = [8,28,30];
const pizza7 = [13,22,24];
const pizza8 = [16,23,20];
const pizza9 = [9,19,21];
const pizza10 = [13,23,11];

document.addEventListener('DOMContentLoaded', function () {
    let options = {
        series: [{
            name: 'J-7',
            data: [
                dessert1[0],
                pizza2[0],
                pizza3[0],
                pizza4[0],
                pizza5[0],
                pizza6[0],
                pizza7[0],
                pizza8[0],
                pizza9[0],
                pizza10[0]
            ]
        }, {
            name: 'J-1',
            data: [
                dessert1[1],
                pizza2[1],
                pizza3[1],
                pizza4[1],
                pizza5[1],
                pizza6[1],
                pizza7[1],
                pizza8[1],
                pizza9[1],
                pizza10[1]
            ]
        }, {
            name: 'J-J',
            data: [
                dessert1[2],
                pizza2[2],
                pizza3[2],
                pizza4[2],
                pizza5[2],
                pizza6[2],
                pizza7[2],
                pizza8[2],
                pizza9[2],
                pizza10[2]
            ]
        }],
        chart: {
            type: 'bar',
            height: 600
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '55%',
                endingShape: 'rounded'
            },
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            show: true,
            width: 2,
            //colors: ['transparent']
        },
        xaxis: {
            categories: [
                listePizza[0],
                listePizza[1],
                listePizza[2],
                listePizza[3],
                listePizza[4],
                listePizza[5],
                listePizza[6],
                listePizza[7],
                listePizza[8],
                listePizza[9],
            ],
            labels: {
                style: {
                    colors: '#FFFFFF' // Couleur rouge pour les étiquettes
                }
            }
        },
        yaxis: {
            title: {
                text: '$ (thousands)'
            },
            labels: {
                style: {
                    colors: '#FFFFFF' // Couleur rouge pour les valeurs
                }
            }
        },
        fill: {
            opacity: 1
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return "$ " + val + " thousands"
                }
            }
        },
    };

    const chart = new ApexCharts(document.querySelector("#chart-1"), options);
    chart.render();

});