/**
 * SalesChart class
 * @class
 * @classdesc This class is used to create a sales chart using ApexCharts
 *
 * @param {HTMLElement} chartElement - The element that will contain the chart
 * @param {string} title - The title of the chart
 * @param {Array} series - The data series for the chart
 * @param {Array} categories - The categories (labels) for the chart
 */
export class SalesChart {
  /**
   *
   * @param {HTMLElement} chartElement - The element that will contain the chart
   * @param {string} title - The title of the chart
   * @param {Array} series - The data series for the chart
   * @param {Array} categories - The categories (labels) for the chart
   */
  constructor(chartElement, title, series, categories) {
    this.chartElement = chartElement;
    this.title = title;
    this.series = series;
    this.categories = categories;
  }

  /**
   * Creates the chart options for ApexCharts.
   * Configures the appearance and behavior of the chart.
   *
   * @returns {Object} - The options object for ApexCharts configuration.
   * @memberof SalesChart
   * @instance
   * @method initializeChart
   * @public
   */
  initializeChart() {
    let options = {
      series: [
        {
          name: this.title,
          data: this.series,
        },
      ],
      chart: {
        height: 350,
        type: "line",
        zoom: {
          enabled: false,
        },
        foreColor: "#FFFFFF",
      },
      dataLabels: {
        enabled: false,
      },
      stroke: {
        curve: "straight",
      },
      title: {
        text: this.title,
        align: "left",
        style: {
          color: "#FFFFFF",
        },
      },
      grid: {
        row: {
          colors: ["#f3f3f3", "transparent"],
          opacity: 0.5,
        },
      },
      xaxis: {
        categories: this.categories,
        labels: {
          style: {
            colors: "#FFFFFF",
          },
        },
      },
      yaxis: {
        labels: {
          style: {
            colors: "#FFFFFF",
          },
        },
      },
      legend: {
        labels: {
          colors: "#FFFFFF",
        },
      },
      tooltip: {
        theme: "dark", // dark, light
        style: {
          fontSize: "12px",
          fontFamily: "Barlow, sans-serif",
          colors: ["#202020"],
        },
      },
    };

    new ApexCharts(this.chartElement, options).render();
  }

  /**
   * Fetches the sales data from the server and creates the sales chart.
   *
   * @async
   * @memberof SalesChart
   * @instance
   * @method fetchAndCreateCharts
   * @public
   * @static
   */
  static async fetchAndCreateCharts() {
    try {
      const response = await fetch("/api/getSalesByMonth", {
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

      if (data.success && data.salesByMonth) {
        const months = data.salesByMonth.map((item) => item.orderMonth);
        const revenues = data.salesByMonth.map((item) =>
          parseFloat(item.monthlyRevenue)
        );

        const chartElement = document.querySelector("#sales-chart");
        if (chartElement) {
          const chart = new SalesChart(
            chartElement,
            "Monthly Sales Revenue",
            revenues,
            months
          );
          chart.initializeChart();
        } else {
          console.error("No chart element found with #salesChart");
        }
      } else {
        console.error("Invalid data structure from API:", data);
      }
    } catch (error) {
      console.error("Error:", error);
    }
  }
}
