<?php

namespace App\controllers;

use App\models\Ingredient;
use App\models\Soda;
use App\models\Wine;
use Exception;
use InvalidArgumentException;

class AdminController extends Controller
{
    public function __construct(string $viewPath)
    {
        parent::__construct($viewPath);
        self::$title = 'Admin';
        self::$cssFiles = ["banner.css", "admin.css","table.css"];
        self::$scriptFiles = ["tabs.js", "chart.js", "cheese-chart1.js", "cheese-chart2.js", "cheese-chart3.js", "cheese-chart4.js"];
        self::$scriptLinkFiles = ["https://cdn.jsdelivr.net/npm/apexcharts"];
        self::$moduleFiles = ["admin.js"];
    }

    public function loadPage(): void
    {
        try {
            $this->viewData = [
                'ingredientSection' => $this->loadSection(Ingredient::getAllIngredients(), 'Ingredient'),
                'winesSection' => $this->loadSection(Wine::getAllWine(), 'Wine'),
                'sodasSection' => $this->loadSection(Soda::getAllSodas(), 'Soda')
            ];

            parent::loadPage();
        } catch (Exception $e) {
            if ($_ENV['ENVIRONMENT'] == 'development') {
                echo '<p>' . $e->getMessage() . '</p>';
            } else {
                echo '<p>An error occurred while loading the admin page.</p>';
            }
        }
    }

    private function loadSection(array $items, string $itemName): string
    {
        $stockProperty = 'stock';
        try {
            $output = '<table><thead><tr>';
            $output .= '<th>' . $itemName . '</th>';
            $output .= '<th>Stock</th>';
            $output .= '<th>Edit</th>';
            $output .= '</tr></thead>';
            $output .= '<tbody>';
            foreach ($items as $item) {
                $output .= '<tr>';
                $output .= '<td>' . $item->name . '</td>';
                try {
                    $output .= '<td>' . $item->$stockProperty . ' ' . $item->unit . '</td>';
                } catch (InvalidArgumentException) {
                    $output .= '<td>' . $item->$stockProperty . ' ' . $item->bottleType . '</td>';
                }
                $output .= '<td style="min-width: 150px;">';
                $output .= '<button type="button" class="btn-error decrement-button">-</button>';
                $output .= '<input type="number" class="' . 'stock-quantity' . '" min="0"
                                data-stock-id="' . $item->id . '"
                                data-stock-type="' . $itemName . '"
                                value="' . $item->$stockProperty . '">';
                $output .= '<button type="button" class="btn-primary increment-button">+</button>';
                $output .= '<button class="btn-primary confirm-quantity hide">Confirm</button>';
                $output .= '</td>';
                $output .= '</tr>';
            }
            $output .= '</tbody></table>';
            return $output;
        } catch (Exception $e) {
            if ($_ENV['ENVIRONMENT'] == 'development') {
                return '<p>' . $e->getMessage() . '</p>';
            } else {
                return '<p>An error occurred while loading the ' . $itemName . ' section.</p>';
            }
        }
    }
}
