<?php

namespace App\controllers;

use App\models\Cocktail;
use App\models\Dessert;
use App\models\Pizza;
use App\models\Soda;
use App\models\Wine;
use Exception;

class HomeController extends Controller
{
    /**
     * HomeController constructor.
     *
     * Initializes the HomeController instance. It sets up the necessary properties for the home page view,
     * including the page title, CSS files, and JavaScript module files.
     *
     * The constructor first calls the parent constructor of the Controller class for essential setup tasks,
     * like setting the view path. It then customizes the home page by setting the page title to "Home",
     * and specifying CSS files ('banner.css', 'home.css') and JavaScript module files ('add-to-cart.js')
     * that are specifically required for the home page's functionality and layout.
     *
     * @param string $viewPath The base path for the view files specific to the HomeController.
     */
    public function __construct(string $viewPath)
    {
        parent::__construct($viewPath); // Call the parent constructor to set the view path
        self::$title = "Home"; // Set the title for the home page
        self::$cssFiles = ["banner.css", "home.css", "add-to-cart-btn.css"]; // Specify CSS files for the home page
        self::$moduleFiles = ["add-to-cart.js"]; // Specify JavaScript module files for the home page
    }

    public function loadPage(): void
    {
        try {
            $this->viewData = [
                'bestSellers' => $this->generateBestSellers()
            ];
            parent::loadPage();
        } catch (Exception $e) {
            if ($_ENV['ENVIRONMENT'] == 'development') {
                echo '<p>' . $e->getMessage() . '</p>';
            } else {
                echo '<p>An error occurred while loading the home page.</p>';
            }
        }
    }

    /**
     * Generates the HTML content for the best sellers section on the home page.
     *
     * This method aggregates the spotlight products from different categories (pizzas, wines, sodas,
     * cocktails, and desserts) and then generates a cumulative HTML string representing these products.
     * The spotlight products are fetched using their respective model classes, and the HTML content for
     * each product is generated using the `generateHTMLForProducts` method.
     *
     * If a category does not have any spotlight products, it is skipped and not included in the final
     * HTML output. The resulting HTML is a compilation of sections for each product category, showcasing
     * the best-selling or featured items.
     *
     * @return string The HTML content for the bestsellers section.
     * @throws Exception If an error occurs during the process of fetching or displaying products.
     */
    private function generateBestSellers(): string
    {
        // Collecting spotlight products from each category
        $spotlightPizzas = Pizza::getSpotlightPizzas();
        $spotlightWines = Wine::getSpotlightWines();
        $spotlightSodas = Soda::getSpotlightSodas();
        $spotlightCocktails = Cocktail::getSpotlightCocktails();
        $spotlightDesserts = Dessert::getSpotlightDesserts();

        $html = '';

        // Helper function to generate HTML for each product
        if (!empty($spotlightPizzas)) {
            $html .= $this->generateHTMLForProducts($spotlightPizzas, 'pizza');
        }
        if (!empty($spotlightWines)) {
            $html .= $this->generateHTMLForProducts($spotlightWines, 'wine');
        }
        if (!empty($spotlightSodas)) {
            $html .= $this->generateHTMLForProducts($spotlightSodas, 'soda');
        }
        if (!empty($spotlightCocktails)) {
            $html .= $this->generateHTMLForProducts($spotlightCocktails, 'cocktail');
        }
        if (!empty($spotlightDesserts)) {
            $html .= $this->generateHTMLForProducts($spotlightDesserts, 'dessert');
        }

        return $html;
    }

    /**
     * Generates HTML content for a given set of products of a specific type.
     *
     * This method takes an array of product objects and a product type (e.g., 'pizza', 'wine') as input.
     * It iterates over the product array and generates an HTML string for each product. The HTML includes
     * the product image, name, description, and an 'Add to Cart' button with product-specific data attributes.
     *
     * The image directory is determined based on the product type, and the product image is sourced in both
     * webp and png formats for browser compatibility. The product name and description are properly escaped
     * to prevent XSS attacks. This method is a utility function used within the class to modularize and
     * simplify the HTML generation process for products.
     *
     * @param array $products An array of product objects to be displayed.
     * @param string $productType The type of products (e.g., 'pizza', 'wine').
     * @return string The HTML content for the provided products.
     */
    private function generateHTMLForProducts($products, $productType): string
    {
        $html = '';
        foreach ($products as $product) {
            $imgDir = '';
            if ($productType == 'wine' || $productType == 'cocktail' || $productType == 'soda') {
                $imgDir = 'drink';
            } else {
                $imgDir = $productType;
            }

            $html .= '<article>';
            $html .= '<picture>';
            $html .= '<source srcset="/img/' . $imgDir . 's/' . $product->getImageName() . '-' . $imgDir .
                '-min.webp" type="image/webp">';
            $html .= '<img src="/img/' . $imgDir . 's/' . $product->getImageName() . '-' . $imgDir .
                '-min.png" type="image/png" alt="' . htmlspecialchars($product->name) . ' ' . $productType .
                '." decoding="async" loading="lazy">';
            $html .= '</picture>';
            $html .= '<div class="product-details">';
            $html .= '<h3>' . htmlspecialchars($product->name) . '</h3>';
            $html .= $product->getDescription();
            $html .= '<button class="add-to-cart" data-product-id="' . $product->id .
                '" data-product-type="' . $productType . '">
                <img src="/img/icons/cart-plus.svg" alt="add to cart icon" class="cart-plus show-flex">
                <img src="/img/icons/cart-check.svg" alt="add to cart icon" class="cart-check hide">
                </button>';
            $html .= '</div>';
            $html .= '</article>';
        }
        return $html;
    }
}