<?php

namespace App\controllers;

use App\models\Cocktail;
use App\models\Dessert;
use App\models\Pizza;
use App\models\Soda;
use App\models\Wine;
use App\models\WineColor;
use Exception;

/**
 * Class MenuController
 *
 * Extends the base Controller class to handle the rendering of the menu page.
 * This controller is responsible for fetching and organizing menu items like pizzas, sodas, and wines,
 * and then passing this data to the view for rendering.
 */
class MenuController extends Controller
{
    /**
     * MenuController constructor.
     *
     * Initializes the MenuController instance. This constructor is responsible for setting up
     * the basic configurations required for rendering the menu page. It sets the page title to "Menu",
     * specifies the CSS files required for the menu page's styling, and also defines the JavaScript
     * files needed for interactive elements on the menu page, such as tabs and quantity controls.
     *
     * The constructor extends the functionality of the parent Controller class constructor. After
     * calling the parent constructor to handle common initializations (like setting the view path),
     * it customizes the setup for the menu page specifically by defining the relevant CSS and
     * JavaScript files. This ensures that the menu page has all the resources it needs for proper
     * rendering and functionality.
     *
     * @param string $viewPath The base path for the view files specific to the MenuController.
     */
    public function __construct(string $viewPath)
    {
        parent::__construct($viewPath); // Call the parent constructor for basic setup
        self::$title = "Menu"; // Set the title specific to the menu page
        self::$cssFiles = ["banner.css", "menu.css", "add-to-cart-btn.css"]; // Define CSS files for the menu page styling
        self::$scriptFiles = ["tabs.js", "quantity-controls.js"]; // Include JavaScript files for interactive elements
        self::$moduleFiles = ["add-to-cart.js"]; // Include JavaScript module for adding items to the cart
    }

    /**
     * Load and render the menu page.
     *
     * This method overrides the loadPage method of the parent Controller class.
     * It fetches data for various menu sections (pizzas, sodas, wines, etc.),
     * processes them into HTML sections, and then calls the parent loadPage method
     * to render these sections within the menu view.
     *
     * @return void
     * @throws Exception If an error occurs during the fetching or rendering process.
     */
    public function loadPage(): void
    {
        try {
            $pizzas = Pizza::getAllPizzas();
            $sodas = Soda::getAllSodas();
            $whiteWines = Wine::getWinesByColor(WineColor::WHITE);
            $redWines = Wine::getWinesByColor(WineColor::RED);
            $cocktails = Cocktail::getAllCocktails();
            $desserts = Dessert::getAllDesserts();

            $this->viewData = [
                'pizzaSection' => $this->getMenuSection($pizzas, 'pizza'),
                'sodaSection' => $this->getMenuSection($sodas, 'soda'),
                'whiteWineSection' => $this->getMenuSection($whiteWines, 'wine'),
                'redWineSection' => $this->getMenuSection($redWines, 'wine'),
                'cocktailSection' => $this->getMenuSection($cocktails, 'cocktail'),
                'dessertSection' => $this->getMenuSection($desserts, 'dessert')
            ];

            parent::loadPage();
        } catch (Exception $e) {
            if ($_ENV['ENVIRONMENT'] == 'development') {
                echo '<p>' . $e->getMessage() . '</p>';
            } else {
                echo '<p>An error occurred while loading the menu page.</p>';
            }
        }
    }

    /**
     * Generate HTML for a menu section.
     *
     * Given an array of food items (like pizzas or sodas) and a product type,
     * this method generates a string of HTML representing these items in a menu section.
     * Each item is rendered with controls for quantity adjustment and adding to cart.
     *
     * @param array $foods Array of food items to be displayed in the menu section.
     * @param string $productType The type of products in the section (e.g., 'pizza', 'soda').
     * @return string The HTML content for the menu section.
     * @throws Exception If an error occurs during the HTML generation process.
     */
    public function getMenuSection(array $foods, string $productType): string
    {
        $output = '';
        $detailsText = $productType == 'pizza' ? 'See Details or Customize' : 'See Details';
        try {
            foreach ($foods as $food) {
                $output .= '<div class="tab-item">';
                $output .= '<article>';
                $output .= $food->displayInMenu();
                $output .= '<a href="/details?id=' . $food->id . '&type=' . $productType . '" class="details-link">' . $detailsText .
                    '<span> 
                                    <img src="/img/icons/link-icon.svg" alt="arrow link icon">
                                </span>
                            </a>';
                $output .= '</article>';
                $output .= '<div class="menu-btns">';
                $output .= '<div class="quantity-control">';
                $output .= '<button class="btn-quantity btn-minus">-</button>';
                $output .= '<span class="product-quantity" data-product-id="' 
                            . $food->id . '" data-product-type="' 
                            . $productType . '">0</span>';
                $output .= '<button class="btn-quantity btn-plus">+</button>';
                $output .= '</div>';
                $output .= '<button class="add-to-cart" 
                            data-product-id="' . $food->id . '"
                            data-product-type="' . $productType . '">
                            <img src="/img/icons/cart-plus.svg" alt="add to cart icon" class="cart-plus show-flex">
                            <img src="/img/icons/cart-check.svg" alt="add to cart icon" class="cart-check hide">
                            </button>';
                $output .= '</div>';
                $output .= '</div>';
            }
        } catch (Exception $e) {
            $output = '<p>An error occurred while retrieving ' . $productType . '.</p>';
            if ($_ENV['ENVIRONMENT'] == 'development') {
                $output .= '<p>' . $e->getMessage() . '</p>';
            }
        }
        return $output;
    }
}
