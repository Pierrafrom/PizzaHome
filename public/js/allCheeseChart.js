document.addEventListener('DOMContentLoaded', function () {
    const myCheeseChartPizza = new CheeseChart(
        'pizza',
        'Best-selling-pizza',
        ["pizza1", "pizza2", "pizza3", "pizza4", "pizza5"],
        44, 55, 41, 17, 15
    );

    myCheeseChartPizza.initializeChart();

    const myCheeseChartDessert = new CheeseChart(
        'dessert',
        'Best-selling-dessert',
        ["pizza1", "pizza2", "pizza3", "pizza4", "pizza5"],
        44, 55, 41, 17, 15
    );

    myCheeseChartDessert.initializeChart();

    const myCheeseChartWine = new CheeseChart(
        'wine',
        'Best-selling-wine',
        ["pizza1", "pizza2", "pizza3", "pizza4", "pizza5"],
        44, 55, 41, 17, 15
    );

    myCheeseChartWine.initializeChart();

    const myCheeseChartCocktail = new CheeseChart(
        'cocktail',
        'Best-selling-cocktail',
        ["pizza1", "pizza2", "pizza3", "pizza4", "pizza5"],
        44, 55, 41, 17, 15
    );

    myCheeseChartCocktail.initializeChart();
});

