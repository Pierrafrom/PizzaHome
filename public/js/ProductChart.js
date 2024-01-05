/**
 * ProductChart class
 * @class
 * @classdesc This class is used to create a chart for each product type
 *
 * @param {HTMLElement} chartElement - The element that will contain the chart
 * @param {string} title - The title of the chart
 * @param {Array} series - The data series for the chart
 * @param {Array} productList - The list of products
 */
export class ProductChart {
  /**
   *
   * @param {HTMLElement} chartElement
   * @param {string} title
   * @param {Array} series
   * @param {Array} productList
   */
  constructor(chartElement, title, series, productList) {
    this.chartElement = chartElement;
    this.title = title;
    this.series = series;
    this.productList = productList;
  }

  /**
   * Creates the chart options for ApexCharts.
   * Configures the appearance and behavior of the chart.
   *
   * @returns {Object} - The options object for ApexCharts configuration.
   */
  initializeChart() {
    let options = {
      series: this.series,
      chart: {
        width: 380,
        type: "donut",
      },
      plotOptions: {
        pie: {
          startAngle: -90,
          endAngle: 270,
        },
      },
      dataLabels: {
        enabled: false,
      },
      fill: {
        type: "gradient",
      },
      legend: {
        formatter: (val, opts) => {
          return (
            this.productList[opts.seriesIndex] +
            " - " +
            opts.w.globals.series[opts.seriesIndex]
          );
        },
        labels: {
          colors: ["#FFFFFF", "#FFFFFF", "#FFFFFF", "#FFFFFF", "#FFFFFF"],
        },
      },
      title: {
        text: this.title,
        style: {
          color: "#FFFFFF",
        },
      },
      responsive: [
        {
          breakpoint: 480,
          options: {
            chart: {
              width: 200,
            },
            legend: {
              position: "bottom",
            },
          },
        },
      ],
    };

    new ApexCharts(this.chartElement, options).render();
  }

  /**
   * Fetches the top products from the server and creates a chart for each product type.
   *
   * @async
   * @static
   * @method
   * @returns {Promise} - The Promise object that resolves when the charts are created.
   * @throws {Error} - Network response was not ok.
   * @throws {Error} - Invalid product quantity.
   */
  static async fetchAndCreateCharts() {
    try {
      const url = "/api/getTopProducts";

      const response = await fetch(url, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({}),
      });

      if (!response.ok) {
        throw new Error("Network response was not ok");
      }

      const data = await response.json();

      if (data.topProducts) {
        const productTypes = Array.from(
          new Set(data.topProducts.map((p) => p.ProductType))
        );

        productTypes.forEach((type) => {
          const filteredProducts = data.topProducts.filter(
            (p) => p.ProductType === type
          );
          const names = filteredProducts.map((p) => p.ProductName);
          const quantities = filteredProducts.map((p) => p.QuantitySold);

          const chartContainer = document.querySelector(
            `#${type.toLowerCase()}`
          );
          const chart = new ProductChart(
            chartContainer,
            type,
            quantities,
            names
          );
          chart.initializeChart();
        });
      }
    } catch (error) {
      console.error("Fetch error: ", error);
    }
  }
}
