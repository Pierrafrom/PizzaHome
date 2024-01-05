<?php

namespace App\controllers;

use App\models\Cocktail;
use App\models\Dessert;
use App\models\Food;
use App\models\Pizza;
use App\models\Wine;
use App\models\Soda;
use Exception;
use InvalidArgumentException;

/**
 * ProductController Class
 *
 * Manages product details, rendering of product details based on product type and id. 
 * It generates HTML content for displaying product images and details, 
 * manages quantity controls, and provides functionality for adding items to the cart.
 *
 * Methods:
 * - __construct(string $viewPath): Sets up page title, CSS, and JavaScript files.
 * - __get(string $property): Magic getter for accessing protected properties.
 * - loadPage(): Loads the product details page based on product type and id.
 * - generatePictureTag(string $productType): Generates HTML <picture> tag for product images.
 * - generateDetails(string $productType): Generates HTML content for product details.
 *
 * @package App\controllers
 */
class ProductController extends Controller
{
    /**
     * @var Food $product The product to be displayed on the page.
     */
    private Food $product;

    /**
     * Constructor for ProductController.
     * Sets up the page title, CSS, and JavaScript files for the product details page.
     *
     * @param string $viewPath The base path for the view files.
     */
    public function __construct(string $viewPath)
    {
        self::$title = "Details"; // Set the title specific to the menu page
        self::$cssFiles = ["details.css", "add-to-cart-btn.css"]; // Define CSS files for the menu page styling
        self::$scriptFiles = ["quantity-controls.js"]; // Include JavaScript files for interactive elements
        self::$moduleFiles = ["add-to-cart.js"]; // Include JavaScript module for adding items to the cart
        parent::__construct($viewPath);
    }

    /**
     * Magic getter to access protected properties of the class.
     * Specifically, it allows accessing the 'product' property.
     *
     * @param string $property The property name to access.
     * @return mixed The value of the property.
     * @throws InvalidArgumentException If the property does not exist.
     */
    public function __get(string $property)
    {
        if ($property == 'product') {
            return $this->product;
        }
        return parent::__get($property);
    }

    /**
     * Loads the product details page.
     * Retrieves product details based on the provided product type and id,
     * generates HTML content for the product image and details, and then renders the page.
     *
     * @throws Exception If the product id or product type is not set or if the product is not found.
     */
    public function loadPage(): void
    {
        // Get the product id and product type from the url.
        $productId = $_GET['id'];
        $productType = $_GET['type'];

        // check if the product id and product type are set.
        if (!isset($productId) || !isset($productType)) {
            throw new Exception('Product id or product type is not set.');
        }

        // Get the product from the database.
        switch ($productType) {
            case 'pizza':
                $this->product = Pizza::getById($productId);
                break;
            case 'dessert':
                $this->product = Dessert::getById($productId);
                break;
            case 'wine':
                $this->product = Wine::getById($productId);
                break;
            case 'soda':
                $this->product = Soda::getById($productId);
                break;
            case 'cocktail':
                $this->product = Cocktail::getById($productId);
                break;
            default:
                throw new InvalidArgumentException('Invalid product type.');
                break;
        }

        $this->viewData = [
            'product' => $this->product,
            'image' => $this->generatePictureTag($productType),
            'details' => $this->generateDetails($productType)
        ];

        parent::loadPage();
    }

    /**
     * Generates the HTML <picture> tag for the product image.
     * The image source is based on the product type and the specific image name associated with the product.
     *
     * @param string $productType The type of the product (e.g., wine, soda, pizza).
     * @return string The HTML <picture> tag with the appropriate image sources.
     */
    private function generatePictureTag(string $productType): string
    {
        $imgDir = '/img/';
        $endFile = '';
        if ($productType == 'wine' || $productType == 'cocktail' || $productType == 'soda') {
            $imgDir .= 'drinks';
            $endFile = '-drink-min.';
        } else {
            $imgDir .= $productType . 's';
            $endFile = '-' . $productType . '-min.';
        }
        $imgDir = $imgDir . '/' . $this->product->getImageName() . $endFile;
        $imageAlt = $this->product->name;

        return "<picture>
                    <source srcset='$imgDir" . "webp' type='image/webp'>
                    <img src='$imgDir" . "png' alt='$imageAlt' decoding=\"async\" loading=\"lazy\">
                </picture>";
    }

    /**
     * Generates HTML content for displaying product details.
     * The content varies based on the product type and may include ingredient lists, descriptions, and quantity controls.
     *
     * @param string $productType The type of the product (e.g., wine, soda, pizza).
     * @return string The HTML content for the product details.
     */
    private function generateDetails(string $productType): string
    {
        $html = '<div class="quantity-area">';
        $html .= '<div class="quantity-control">';
        $html .= '<button class="btn-quantity btn-minus">-</button>';
        $html .= '<span class="product-quantity" data-product-id="'
            . $this->product->id . '" data-product-type="'
            . $productType . '">0</span>';
        $html .= '<button class="btn-quantity btn-plus">+</button>';
        $html .= '</div>';
        $html .= '<button class="add-to-cart" 
                            data-product-id="' . $this->product->id . '"
                            data-product-type="' . $productType . '">
                            <img src="/img/icons/cart-plus.svg" alt="add to cart icon" class="cart-plus show-flex">
                            <img src="/img/icons/cart-check.svg" alt="add to cart icon" class="cart-check hide">
                            </button>';
        $html .= '</div>';

        switch ($productType) {
            case 'wine':
                $html .= '<p>' . $this->product->getLongDescription() . '</p>';
                break;
            case 'soda':
                break;
            case 'pizza':
                $html .= '<ul class="ingredient-list">';
                $html .= '<h3>Ingredients</h3>';
                foreach ($this->product->ingredients as $ingredient) {
                    $html .= '<li class="ingredient">
                                    <input type="checkbox" id="cross-checkbox-' . $ingredient->id . '" class="hide">
                                    <label for="cross-checkbox-' . $ingredient->id . '">
                                        <span class="cross">&#10005;</span>
                                    </label>
                                    <span class="ingredient-name">' . $ingredient->name . '</span>
                                </li>';
                }
                $html .= '<p>(3 removed ingredients allowed)</p>';
                $html .= '</ul>';
                $html .= '<div class="supplements">
                                <h3>Supplements</h3>
                                <label><input type="checkbox" name="supplement" value="7">Pepperoni</label>
                                <label><input type="checkbox" name="supplement" value="17">Black Olives</label>
                                <label><input type="checkbox" name="supplement" value="2">Garlic</label>
                                <p>(1€50 each)</p>
                            </div>';

                break;
            default:
                $html .= '<ul class="ingredient-list">';
                foreach ($this->product->ingredients as $ingredient) {
                    $html .= '<li class="ingredient">' . $ingredient->name . '</li>';
                }
                $html .= '</ul>';
                break;
        }

        return $html;
    }
}
