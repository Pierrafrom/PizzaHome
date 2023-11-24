document.addEventListener('DOMContentLoaded', function () {

    let options = {
        series: [{
            name: 'J-7',
            data: [3, 7, 5, 6, 2, 8, 3, 6, 1, 3]
        }, {
            name: 'J-1',
            data: [35, 41, 36, 26, 45, 48, 52, 53, 41, 53]
        }, {
            name: 'J-J',
            data: [76, 85, 101, 98, 87, 105, 91, 114, 94, 100]
        }],
        chart: {
            type: 'bar',
            height: 350
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
            colors: ['transparent']
        },
        xaxis: {
            categories: ['Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct'],
        },
        yaxis: {
            title: {
                text: '$ (thousands)'
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
        }
    };

    const chart = new ApexCharts(document.querySelector("#chart-1"), options);
    chart.render();

});
