<?php

namespace App\controllers;

use App\controllers\Controller;
use App\helpers\URL;
use App\models\Cocktail;
use App\models\Dessert;
use App\models\Ingredient;
use App\models\Pizza;
use App\models\Soda;
use App\models\Wine;
use App\DB_Connection;
use Exception;
use PDOException;

/**
 * CreationController class for handling the creation of various product types like ingredients, wines, sodas, etc.
 * 
 * This class extends the base Controller class and includes functionalities for generating creation forms,
 * validating data, and creating objects in the database.
 * 
 * Methods included in this class:
 * 
 * - __construct(string $viewPath): Initializes the controller with specific setup for the creation page.
 * - loadPage(): Loads the creation page with appropriate view data.
 * - generateCreationForm(): Generates a form for creating a new object based on the given class type.
 * - createObject(): Processes the POST request to create a new object in the database.
 * - validateCommonProductData(array $data): Validates common data across different product types.
 * - createPizza(array $data): Handles the creation of a pizza.
 * - createWine(array $data): Handles the creation of a wine.
 * - createSoda(array $data): Handles the creation of a soda.
 * - createDessert(array $data): Handles the creation of a dessert.
 * - createCocktail(array $data): Handles the creation of a cocktail.
 * - createIngredient(array $data): Handles the creation of an ingredient.
 * - handleIngredients(array $data): Extracts and validates ingredient data from the request.
 *
 * @package App\controllers
 */
class CreationController extends Controller
{
    /**
     * Constructs the CreationController with a specific view path.
     * 
     * Initializes the controller with a title, CSS, and JavaScript files specific to the creation page.
     * Calls the parent constructor for basic setup.
     *
     * @param string $viewPath The base path for the view files.
     */
    public function __construct(string $viewPath)
    {
        parent::__construct($viewPath); // Call the parent constructor for basic setup
        self::$title = "Creation"; // Set the title specific to the registration and login pages
        self::$cssFiles = ["form.css"]; // Define CSS files for page styling
        self::$scriptFiles = ["form.js"]; // Include JavaScript files for tab functionality
    }

    /**
     * Loads the creation page.
     * 
     * Prepares and sets view data including the form and title for the creation page.
     * Calls the parent method to complete the page loading process.
     */
    public function loadPage(): void
    {
        $this->viewData = [
            'form' => $this->generateCreationForm(),
            'title' => 'New ' . ucfirst(str_replace('App\\models\\', '', $_GET['class']))
        ];
        parent::loadPage();
    }

    /**
     * Generates a creation form for a specified class.
     * 
     * Creates a form based on the class specified in the $_GET variable.
     * Builds the form fields dynamically based on the class's formFields property.
     * 
     * @return string The HTML content of the generated form.
     */
    private function generateCreationForm(): string
    {
        // get the class via the $_GET variable
        $className = $_GET['class'] ?? '';
        // Check if the class name is valid and exists
        if (!class_exists($className)) {
            return 'Classe non valide ou introuvable.';
        }

        // get the class' fields
        $formFields = $className::$formFields;

        $content = '';
        foreach ($formFields as $fieldName => $fieldAttributes) {

            $content .= '<div class="form-group">';

            $requiredStar = isset($fieldAttributes['required']) && $fieldAttributes['required'] ?
                '<span style="color: var(--secondary);">*</span>' : '';
            if (strpos(strtolower($fieldName), 'price') !== false) {
                $content .= '<label for="' . htmlspecialchars($fieldName) . '">' . htmlspecialchars(ucfirst($fieldName)) .
                    ' (€) ' . $requiredStar . '</label>';
            } else {
                $content .= '<label for="' . htmlspecialchars($fieldName) . '">' . htmlspecialchars(ucfirst($fieldName)) .
                    $requiredStar . '</label>';
            }


            if ($fieldAttributes['type'] === 'textarea') {
                $placeholder = isset($fieldAttributes['placeholder']) ? ' placeholder="' . htmlspecialchars($fieldAttributes['placeholder']) . '"' : '';
                $required = isset($fieldAttributes['required']) && $fieldAttributes['required'] ? ' required' : '';
                $content .= '<textarea id="' . htmlspecialchars($fieldName) . '" maxlength="100"' . 'name="' . htmlspecialchars($fieldName) . '"' .
                    $placeholder . $required . '></textarea>';
            } elseif ($fieldAttributes['type'] === 'checkbox') {
                $content .= '<select id="' . htmlspecialchars($fieldName) . '" name="' . htmlspecialchars($fieldName) . '">';
                $content .= '<option value=0>No</option>';
                $content .= '<option value=1>Yes</option>';
                $content .= '</select>';
            } else {
                $placeholder = isset($fieldAttributes['placeholder']) ? ' placeholder="' . htmlspecialchars($fieldAttributes['placeholder']) . '"' : '';
                $required = isset($fieldAttributes['required']) && $fieldAttributes['required'] ? ' required' : '';
                $content .= '<input type="' . htmlspecialchars($fieldAttributes['type']) .
                    '" id="' . htmlspecialchars($fieldName) .
                    '" name="' . htmlspecialchars($fieldName) . '"' .
                    $placeholder . $required . '>';
            }

            $content .= '</div>';
        }

        // Include the special section if defined in the class
        if (method_exists($className, 'generateSpecificSection')) {
            $content .= $className::generateSpecificSection();
        }

        return $content;
    }

    /**
     * Processes the creation of an object based on POST request data.
     * 
     * Determines the type of object to be created based on the class name in the POST data.
     * Delegates the creation process to the respective method based on the object type.
     */
    public function createObject()
    {
        // si la methode est post
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        $className = str_replace('App\\models\\', '', $_POST['class']);

        switch ($className) {
            case 'Ingredient':
                $this->createIngredient($_POST);
                break;
            case 'Wine':
                $this->createWine($_POST);
                break;
            case 'Soda':
                $this->createSoda($_POST);
                break;
            case 'Pizza':
                $this->createPizza($_POST);
                break;
            case 'Dessert':
                $this->createDessert($_POST);
                break;
            case 'Cocktail':
                $this->createCocktail($_POST);
                break;
            default:
                throw new Exception('Invalid class');
        }

        URL::redirect($_POST['redirect'] ?? '/');
    }

    /**
     * Validates common data across different product types.
     * 
     * Checks for required fields like name, price, and spotlight status in the provided data.
     * Throws an exception if validation fails.
     *
     * @param array $data The data to validate.
     * @throws Exception If validation fails.
     */
    private static function validateCommonProductData($data)
    {
        // Name validation
        if (empty($data['name'])) {
            throw new Exception('Name is required.');
        } elseif (strlen($data['name']) > 50) {
            throw new Exception('Name must not exceed 50 characters.');
        }

        // Price validation
        if (empty($data['price'])) {
            throw new Exception('Price is required.');
        } elseif (!is_numeric($data['price']) || $data['price'] < 0) {
            throw new Exception('Price must be a positive number.');
        }

        // Spotlight status validation
        if (!isset($data['spotlight']) || (($data['spotlight']) != 0 && ($data['spotlight']) != 1)) {
            throw new Exception('Spotlight value must be boolean.');
        }
    }

    /**
     * Handles the creation of a pizza.
     * 
     * Validates provided data, processes ingredients, and calls the database procedure to create a pizza.
     * Throws an exception if the creation fails.
     *
     * @param array $data The data from the request.
     * @throws Exception If pizza creation fails.
     */
    private function createPizza($data)
    {
        self::validateCommonProductData($data);
        $ingredients = $this->handleIngredients($data);


        $params = [
            'p_pizzaName' => $data['name'],
            'p_pizzaPrice' => (float)$data['price'],
            'p_spotlight' => (int)(bool)$data['spotlight']
        ];

        for ($i = 1; $i <= 8; $i++) {
            $params["p_ingredientId$i"] = $ingredients[$i - 1]['id'] ?? null;
            $params["p_quantity$i"] = $ingredients[$i - 1]['quantity'] ?? null;
        }

        $outParams = ['p_newId' => null];

        try {
            DB_Connection::callProcedure('CreatePizzaWithIngredients', $params, $outParams);

            if ($outParams['p_newId'] == -1) {
                throw new Exception("Failed to create the pizza.");
            }
        } catch (PDOException $e) {
            throw new Exception("Failed to create the pizza: " . $e->getMessage());
        }
    }

    /**
     * Handles the creation of a wine.
     * 
     * Validates the provided data and calls the database procedure to create a wine.
     * Manages specific validation for wine attributes such as glass price, domain, grape variety,
     * origin, alcohol percentage, year, color, stock, and bottle type.
     *
     * @param array $data The data from the request.
     * @throws Exception If wine creation fails or validation errors occur.
     */
    private function createWine($data)
    {
        // Validate common data
        self::validateCommonProductData($data);

        if ((!is_numeric($data['glassPrice']) || $data['glassPrice'] < 0) && !empty($data['glassPrice'])) {
            throw new Exception('Glass price must be a positive number.');
        }
        if (empty($data['glassPrice']) && empty($data['price'])) {
            throw new Exception('Price is required.');
        }

        if (strlen($data['domain'] > 50 && !empty($data['domain']))) {
            throw new Exception('Domain must not exceed 50 characters.');
        } else if (empty($data['domain'])) {
            $data['domain'] = null;
        }

        if (strlen($data['grapeVariety'] && !empty($data['grapeVariety'])) > 50) {
            throw new Exception('Grape variety must not exceed 50 characters.');
        } else if (empty($data['grapeVariety'])) {
            $data['grapeVariety'] = null;
        }

        if (strlen($data['origin']) > 50 && !empty($data['origin'])) {
            throw new Exception('Origin must not exceed 50 characters.');
        } else if (empty($data['origin'])) {
            $data['origin'] = null;
        }

        if ((!is_numeric($data['alcoholPercentage']) || $data['alcoholPercentage'] < 0) && !empty($data['alcoholPercentage'])) {
            throw new Exception('Alcohol percentage must be a positive number.');
        } elseif (empty($data['alcoholPercentage'])) {
            $data['alcoholPercentage'] = null;
        }

        $currentYear = date("Y");
        if (!is_numeric($data['year']) || $data['year'] < 0 || $data['year'] > $currentYear) {
            throw new Exception('Year must be a positive number and not exceed the current year.');
        } elseif (empty($data['year'])) {
            $data['year'] = null;
        }

        if (empty($data['color'])) {
            throw new Exception('Color is required.');
        } elseif (strlen($data['color']) > 50) {
            throw new Exception('Color must not exceed 50 characters.');
        } elseif ($data['color'] != 'RED' && $data['color'] != 'WHITE' && $data['color'] != 'ROSE') {
            throw new Exception('Invalid color.');
        }

        if (empty($data['stock']) || !is_numeric($data['stock']) || $data['stock'] < 0) {
            throw new Exception('Stock quantity must be a positive number.');
        }

        if (empty($data['bottleType'])) {
            throw new Exception('Bottle type is required.');
        } elseif (strlen($data['bottleType']) > 50) {
            throw new Exception('Bottle type must not exceed 50 characters.');
        } elseif (
            $data['bottleType'] != 'BOTTLE'
            && $data['bottleType'] != 'PICCOLO'
            && $data['bottleType'] != 'MAGNUM'
            && $data['bottleType'] != 'JEROBOAM'
            && $data['bottleType'] != 'REHOBOAM'
            && $data['bottleType'] != 'MATHUSALEM'
        ) {
            throw new Exception('Invalid bottle type.');
        }

        $params = [
            'p_wineName' => $data['name'],
            'p_glassPrice' => (float)$data['glassPrice'],
            'p_bottlePrice' => (float)$data['price'],
            'p_domain' => $data['domain'],
            'p_grapeVariety' => $data['grapeVariety'],
            'p_origin' => $data['origin'],
            'p_alcoholPercentage' => (float)$data['alcoholPercentage'],
            'p_year' => $data['year'],
            'p_color' => $data['color'],
            'p_spotlight' => $data['spotlight'],
            'p_stock' => (int)$data['stock'],
            'p_bottleType' => $data['bottleType']
        ];

        $outParams = ['p_newId' => null];

        try {
            DB_Connection::callProcedure('CreateWine', $params, $outParams);

            if ($outParams['p_newId'] == -1) {
                throw new Exception("Failed to create the wine.");
            }
        } catch (PDOException $e) {
            throw new Exception("Failed to create the wine: " . $e->getMessage());
        }
    }


    /**
     * Handles the creation of a soda.
     * 
     * Validates the common product data along with specific soda attributes like stock and bottle type.
     * Calls the database procedure to create a soda.
     *
     * @param array $data The data from the request.
     * @throws Exception If soda creation fails or validation errors occur.
     */
    private function createSoda($data)
    {
        // Validate common data
        self::validateCommonProductData($data);

        // Validate specific soda data
        if (empty($data['stock']) || !is_numeric($data['stock']) || $data['stock'] < 0) {
            throw new Exception('Stock quantity must be a positive number.');
        }

        if (!empty($data['bottleType'])) {
            $data['bottleType'] = strtoupper($data['bottleType']);
        }

        if (!in_array($data['bottleType'], ['BOTTLE', 'CAN'])) {
            throw new Exception('Invalid bottle type.');
        }

        $params = [
            'p_sodaName' => $data['name'],
            'p_sodaPrice' => $data['price'],
            'p_stock' => $data['stock'],
            'p_bottleType' => $data['bottleType'],
            'p_spotlight' => $data['spotlight']
        ];

        $outParams = ['p_newId' => null];

        try {
            DB_Connection::callProcedure('CreateSoda', $params, $outParams);

            if ($outParams['p_newId'] == -1) {
                throw new Exception("Failed to create the soda.");
            }
        } catch (PDOException $e) {
            throw new Exception("Failed to create the soda: " . $e->getMessage());
        }
    }

    /**
     * Handles the creation of a dessert.
     * 
     * Validates the common product data and processes the ingredients.
     * Calls the database procedure to create a dessert with its ingredients.
     *
     * @param array $data The data from the request.
     * @throws Exception If dessert creation fails or validation errors occur.
     */
    private function createDessert($data)
    {
        // Validate common data
        self::validateCommonProductData($data);
        $ingredients = $this->handleIngredients($data);

        $params = [
            'p_dessertName' => $data['name'],
            'p_dessertPrice' => $data['price'],
            'p_spotlight' => $data['spotlight']
        ];

        for ($i = 1; $i <= 8; $i++) {
            $params["p_ingredientId$i"] = $ingredients[$i - 1]['id'] ?? null;
            $params["p_quantity$i"] = $ingredients[$i - 1]['quantity'] ?? null;
        }

        $outParams = ['p_newId' => null];

        try {
            DB_Connection::callProcedure('CreateDessertWithIngredients', $params, $outParams);

            if ($outParams['p_newId'] == -1) {
                throw new Exception("Failed to create the dessert.");
            }
        } catch (PDOException $e) {
            throw new Exception("Failed to create the dessert: " . $e->getMessage());
        }
    }

    /**
     * Handles the creation of a cocktail.
     * 
     * Validates the common product data along with specific cocktail attributes like alcohol percentage.
     * Processes the ingredients and calls the database procedure to create a cocktail.
     *
     * @param array $data The data from the request.
     * @throws Exception If cocktail creation fails or validation errors occur.
     */
    private function createCocktail($data)
    {
        // Validate common data
        self::validateCommonProductData($data);
        $ingredients = $this->handleIngredients($data);
        $alcoholPercentage = (float)$data['alcoholPercentage'] ?? -1;

        // Validate specific cocktail data
        if ($alcoholPercentage < 0 || $alcoholPercentage > 100) {
            throw new Exception('Alcohol percentage must be between 0 and 100.');
        }

        $params = [
            'p_cocktailName' => $data['name'],
            'p_cocktailPrice' => $data['price'],
            'p_spotlight' => $data['spotlight'],
            'p_alcoholPercentage' => $alcoholPercentage
        ];

        for ($i = 1; $i <= 8; $i++) {
            $params["p_ingredientId$i"] = $ingredients[$i - 1]['id'] ?? null;
            $params["p_quantity$i"] = $ingredients[$i - 1]['quantity'] ?? null;
        }

        $outParams = ['p_newId' => null];

        try {
            DB_Connection::callProcedure('CreateCocktailWithIngredients', $params, $outParams);

            if ($outParams['p_newId'] == -1) {
                throw new Exception("Failed to create the cocktail.");
            }
        } catch (PDOException $e) {
            throw new Exception("Failed to create the cocktail: " . $e->getMessage());
        }
    }

    /**
     * Handles the creation of an ingredient.
     * 
     * Validates specific ingredient data such as name, stock quantity, description, allergen status, and unit.
     * Calls the database procedure to create an ingredient.
     *
     * @param array $data The data from the request.
     * @throws Exception If ingredient creation fails or validation errors occur.
     */
    private function createIngredient($data)
    {
        // Name validation
        if (empty($data['name'])) {
            throw new Exception('Name is required.');
        } elseif (strlen($data['name']) > 50) {
            throw new Exception('Name must not exceed 50 characters.');
        }

        // Validate specific ingredient data
        if (empty($data['stock']) || !is_numeric($data['stock']) || $data['stock'] < 0) {
            throw new Exception('Stock quantity must be a positive number.');
        }

        if (strlen($data['description']) > 100) {
            throw new Exception('Description must not exceed 100 characters.');
        } elseif (empty($data['description'])) {
            $data['description'] = null;
        }

        // validate isAllergen
        if (isset($data['isAllergen']) && ($data['isAllergen']) != 0 && ($data['isAllergen']) != 1) {
            throw new Exception('isAllergen value must be boolean.');
        }

        if (empty($data['unit'])) {
            throw new Exception('Unit is required.');
        } elseif (
            $data['unit'] != 'G'
            && $data['unit'] != 'KG'
            && $data['unit'] != 'ML'
            && $data['unit'] != 'L'
            && $data['unit'] != 'CL'
            && $data['unit'] != 'MG'
        ) {
            throw new Exception('Unit must not exceed 50 characters.');
        }

        $params = [
            'p_ingredientName' => $data['name'],
            'p_ingredientDescription' => $data['description'],
            'p_ingredientQuantity' => $data['stock'],
            'p_isAllergen' => $data['isAllergen'],
            'p_unit' => $data['unit']
        ];

        $outParams = ['p_newId' => null];

        try {
            DB_Connection::callProcedure('CreateIngredient', $params, $outParams);

            if ($outParams['p_newId'] == -1) {
                throw new Exception("Failed to create the ingredient.");
            }
        } catch (PDOException $e) {
            throw new Exception("Failed to create the ingredient: " . $e->getMessage());
        }
    }

    /**
     * Handles ingredient data extraction and validation from the provided data array.
     * 
     * Extracts ingredient IDs and quantities, validates them, and prepares an array of ingredients.
     * Ensures the number of valid ingredients is between 1 and 8.
     *
     * @param array $data The data array containing ingredient information.
     * @return array An array of validated ingredients with their IDs and quantities.
     * @throws Exception If the number of ingredients is invalid.
     */
    private function handleIngredients($data)
    {
        $ingredients = [];
        $validIngredientCount = 0;

        // Extract and validate ingredient data from $data
        for ($i = 1; $i <= 8; $i++) {
            if (!empty($data["ingredient-$i"]) && !empty($data["quantity-$i"])) {
                $ingredientId = (int)$data["ingredient-$i"];
                $quantity = (float)$data["quantity-$i"];

                // Check if ingredient ID is valid and not the 'empty' ingredient (40)
                if ($ingredientId >= 1 && $ingredientId != 40) {
                    $ingredients[] = [
                        'id' => $ingredientId,
                        'quantity' => $quantity
                    ];
                    $validIngredientCount++;
                }
            }
        }

        // Check if the number of valid ingredients is between 1 and 8
        if ($validIngredientCount < 1 || $validIngredientCount > 8) {
            throw new Exception('Invalid number of ingredients. Must be between 1 and 8.');
        }

        return $ingredients;
    }
}
