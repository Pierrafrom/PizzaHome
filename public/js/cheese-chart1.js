class CheeseChart {
    constructor(type,title, pizzaList, serie1, serie2, serie3, serie4, serie5) {
        this.type = type;
        this.title = title;
        this.pizzaList = pizzaList;
        this.series = [serie1, serie2, serie3, serie4, serie5];
    }
    initializeChart() {
        let options = {
            series: this.series,
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
                formatter: (val, opts) => {
                    return this.pizzaList[opts.seriesIndex] + " - " + opts.w.globals.series[opts.seriesIndex];
                },
                labels: {
                    colors: ['#FFFFFF', '#FFFFFF', '#FFFFFF', '#FFFFFF', '#FFFFFF']
                }
            },
            title: {
                text: this.title,
                style: {
                    color: '#FFFFFF'
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

        const chart = new ApexCharts(document.querySelector("#"+this.type), options);
        chart.render();
    }
}
