<?php

namespace App\controllers;

class AdminController extends Controller
{
    public function __construct(string $viewPath)
    {
        parent::__construct($viewPath); // Call the parent constructor to set the view path
        self::$title = 'Admin'; // Set the title for the cart page
        self::$cssFiles = ["banner.css", "admin.css"]; // Specify CSS files for the cart view
        self::$scriptFiles = ["chart.js","cheese-chart1.js","cheese-chart2.js","cheese-chart3.js","cheese-chart4.js"]; // Specify JS files for the cart view
        self::$scriptLinkFiles = ["https://cdn.jsdelivr.net/npm/apexcharts"]; // Specify JS link files for the cart view
    }
}