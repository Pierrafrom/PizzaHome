import { ProductChart } from "./ProductChart.js";
import { SalesChart } from "./SalesChart.js";
import { PizzaSalesChart } from "./PizzaSalesChart.js";

// Wait for the DOM to be loaded
document.addEventListener("DOMContentLoaded", function () {
  ProductChart.fetchAndCreateCharts(); // Fetch and create the product charts
  SalesChart.fetchAndCreateCharts(); // Fetch and create the sales charts
  PizzaSalesChart.fetchAndCreateCharts(); // Fetch and create the pizza sales charts
});
