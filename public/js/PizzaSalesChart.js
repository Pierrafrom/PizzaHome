export class PizzaSalesChart {
  constructor(chartElement, series, categories) {
    this.chartElement = chartElement;
    this.series = series; // Les données des ventes
    this.categories = categories; // Les noms des pizzas
  }

  static createChartOptions(
    pizzaNames,
    soldToday,
    soldYesterday,
    soldLastWeekSameDay
  ) {
    return {
      series: [
        {
          name: "Sold Last Week Same Day",
          data: soldLastWeekSameDay,
        },
        {
          name: "Sold Yesterday",
          data: soldYesterday,
        },
        {
          name: "Sold Today",
          data: soldToday,
        },
      ],
      chart: {
        type: "bar",
        height: 600,
        foreColor: "#FFFFFF",
      },
      plotOptions: {
        bar: {
          horizontal: false,
          columnWidth: "55%",
          endingShape: "rounded",
        },
      },
      dataLabels: {
        enabled: false,
      },
      stroke: {
        show: true,
        width: 2,
      },
      title: {
        text: "Pizza Sales",
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
        categories: pizzaNames,
        labels: {
          style: {
            colors: "#FFFFFF",
          },
        },
      },
      yaxis: {
        title: {
          text: "Number Sold",
        },
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
        theme: "dark",
        style: {
          fontSize: "12px",
          fontFamily: "Barlow, sans-serif",
          colors: ["#202020"],
        },
      },
    };
  }

  static async fetchAndCreateCharts() {
    try {
      const url = "/api/getPizzaStats";
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

      if (data.success && data.pizzaStats) {
        const pizzaNames = data.pizzaStats.map((item) => item.pizzaName);
        const soldToday = data.pizzaStats.map((item) => item.SoldToday);
        const soldYesterday = data.pizzaStats.map((item) => item.SoldYesterday);
        const soldLastWeekSameDay = data.pizzaStats.map(
          (item) => item.SoldLastWeekSameDay
        );

        const chartElement = document.querySelector("#pizza-chart");
        if (chartElement) {
          let options = PizzaSalesChart.createChartOptions(
            pizzaNames,
            soldToday,
            soldYesterday,
            soldLastWeekSameDay
          );

          const chart = new ApexCharts(chartElement, options);
          chart.render();
        } else {
          console.error("No chart element found with #pizza-chart");
        }
      } else {
        console.error("Invalid data structure from API:", data);
      }
    } catch (error) {
      console.error("Error:", error);
    }
  }
}
