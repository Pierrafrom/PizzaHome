import { ProductChart } from "./ProductChart.js";
import { SalesChart } from "./SalesChart.js";
import { PizzaSalesChart } from "./PizzaSalesChart.js";

document.addEventListener("DOMContentLoaded", function () {
  ProductChart.fetchAndCreateCharts();
  SalesChart.fetchAndCreateCharts();
  PizzaSalesChart.fetchAndCreateCharts();
});
