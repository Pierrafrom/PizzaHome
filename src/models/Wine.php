<?php

namespace App\models;

use App\DB_Connection;
use InvalidArgumentException;

/**
 * Represents a specific type of wine, extending the properties and functionalities of the Food class.
 * Handles various characteristics of wine, including its price, domain, grape variety, origin, alcohol percentage, year, and color.
 * Provides methods to create new instances of Wine, access and modify attributes, retrieve collections of wine instances
 * filtered by color, and format the wine information for menu presentations.
 * 
 * Properties:
 * - glassPrice: float - The price of a glass of the wine.
 * - domain: string - The domain or brand of the wine.
 * - grapeVariety: string - The type of grape used in the wine.
 * - origin: string - The geographical origin of the wine.
 * - alcoholPercentage: float - The alcohol content percentage of the wine.
 * - year: int - The vintage year of the wine.
 * - color: string - The color of the wine (e.g., RED, WHITE, ROSE).
 * - stock: int - The quantity of the wine in stock.
 * - bottleType: string - The type of wine bottle.
 *
 * Methods:
 * - __construct: Constructor to initialize a Wine instance with specified attributes.
 * - __toString: Returns a string representation of the Wine instance.
 * - __get: Magic getter to retrieve property values.
 * - __set: Magic setter to set property values with validation.
 * - getWinesByColor: Retrieves wine entries filtered by color.
 * - displayInMenu: Formats wine information for menu display.
 * - getDescription: Retrieves a brief description of the wine.
 * - getLongDescription: Retrieves a detailed description of the wine.
 * - getById: Retrieves a Wine instance by its ID.
 * - getSpotlightWines: Retrieves all spotlight wine entries.
 * - generateSpecificSection: Generates additional HTML form sections specific to the Wine class.
 */
class Wine extends Food
{

    /**
     * @var int Static counter to automatically generate unique identifiers.
     */
    private static int $autoIncrementId = 1;

    /**
     * @var float price of a glass of the wine.
     */
    private ?float $glassPrice = null;

    /**
     * @var string domain of the wine.
     */
    private ?string $domain = null;

    /**
     * @var string grape variety of the wine.
     */
    private ?string $grapeVariety = null;

    /**
     * @var string origin of the wine.
     */
    private ?string $origin = null;

    /**
     * @var float alcohol percentage of the wine.
     */
    private ?float $alcoholPercentage = null;

    /**
     * @var int year of the wine.
     */
    private ?int $year = null;

    /**
     * @var string color of the wine.
     */
    private string $color;

    /**
     * @var int stock of the wine.
     */
    private int $stock;

    /**
     * @var string bottle type of the wine.
     */
    private string $bottleType;

    /**
     * @var array An array of form fields to display when editing the wine.
     */
    public static $formFields = [
        'name' => ['type' => 'text', 'placeholder' => 'Name of the Wine', 'required' => true],
        'price' => ['type' => 'number', 'placeholder' => 'Bottle Price', 'required' => true],
        'glassPrice' => ['type' => 'number', 'placeholder' => 'Price per Glass', 'required' => false],
        'domain' => ['type' => 'text', 'placeholder' => 'Domain', 'required' => false],
        'grapeVariety' => ['type' => 'text', 'placeholder' => 'Grape Variety', 'required' => false],
        'origin' => ['type' => 'text', 'placeholder' => 'Origin', 'required' => false],
        'alcoholPercentage' => ['type' => 'number', 'placeholder' => 'Alcohol Percentage', 'required' => false],
        'year' => ['type' => 'number', 'placeholder' => 'Year', 'required' => true],
        'spotlight' => ['type' => 'checkbox', 'required' => true],
        'stock' => ['type' => 'number', 'placeholder' => 'Stock Quantity', 'required' => true]
    ];

    /**
     * Constructor for the Wine class. Initializes a new Wine object with specified attributes.
     * If an ID is provided and valid, the object is populated with the given parameters; otherwise, default values are used.
     * This method allows for flexible instantiation of a Wine object, with all or partial attributes specified.
     *
     * Parameters:
     * - id (int|null): The unique identifier for the wine. If null, a new ID is automatically generated.
     * - name (string|null): The name of the wine. Defaults to 'My Wine' if not specified.
     * - price (float|null): The price of the wine. Defaults to 12.0 if not specified.
     * - spotlight (bool|null): A boolean indicating if the wine is a spotlight item. Defaults to false if not specified.
     * - glassPrice (float|null): The price of a glass of this wine. Defaults to 4.0 if not specified.
     * - domain (string|null): The domain or brand of the wine. Defaults to 'My Domain' if not specified.
     * - grapeVariety (string|null): The grape variety used in the wine. Defaults to 'My Grape Variety' if not specified.
     * - origin (string|null): The origin of the wine. Defaults to 'My Origin' if not specified.
     * - alcoholPercentage (float|null): The alcohol percentage of the wine. Defaults to 12.0 if not specified.
     * - year (int|null): The vintage year of the wine. Defaults to 2020 if not specified.
     * - color (string|null): The color of the wine, which should be one of the predefined WineColor constants ('RED', 'WHITE', 'ROSE'). Throws InvalidArgumentException if an invalid color is provided.
     * Example Usage:
     * ```
     * $wine = new Wine(name: "Cabernet Sauvignon", price: 18.0, glassPrice: 5.0, domain: "Bordeaux", grapeVariety: "Cabernet Sauvignon", origin: "France", alcoholPercentage: 14.0, year: 2015, color: "RED");
     * ```
     *
     * Note: This constructor throws InvalidArgumentException if an invalid wine color is provided.
     */
    public function __construct(
        ?int    $id = null,
        ?string $name = null,
        ?float  $price = null,
        ?bool   $spotlight = null,
        ?float  $glassPrice = null,
        ?string $domain = null,
        ?string $grapeVariety = null,
        ?string $origin = null,
        ?float  $alcoholPercentage = null,
        ?int    $year = null,
        ?string $color = null,
        ?int    $stock = null,
        ?string $bottleType = null
    ) {
        if (!is_null($id)) {
            if (!is_null($color) && !in_array($color, WineColor::cases())) {
                throw new InvalidArgumentException("Color should be 'RED', 'WHITE' or 'ROSE'.");
            }
            $this->id = self::$autoIncrementId++;
            $this->name = $name ?? 'My Wine';
            $this->price = $price ?? 12.0;
            $this->spotlight = $spotlight ?? false;
            $this->glassPrice = $glassPrice ?? 4.0;
            $this->domain = $domain ?? 'My Domain';
            $this->grapeVariety = $grapeVariety ?? 'My Grape Variety';
            $this->origin = $origin ?? 'My Origin';
            $this->alcoholPercentage = $alcoholPercentage ?? 12.0;
            $this->year = $year ?? 2020;
            $this->color = $color ?? WineColor::RED;
            $this->stock = $stock ?? 0;
            $this->bottleType = $bottleType ?? 'BOTTLE';
        }
    }

    /**
     * Magic method to provide a string representation of the Wine object.
     * This method returns a string that concisely describes the wine, including its name and price.
     * It is useful for quickly understanding the basic details of the wine instance,
     * especially in contexts where a simple text representation is needed.
     *
     * @return string A string in the format "Wine: [name] ([price] €)".
     *
     * Example Usage:
     * ```
     * $wine = new Wine(name: "Merlot", price: 15.0);
     * echo $wine; // Outputs: Wine: Merlot (15.0 €)
     * ```
     */
    public function __toString(): string
    {
        return "Wine: $this->name ($this->price €)";
    }

    /**
     * Magic getter method for accessing the properties of the Wine object.
     * This method allows for the retrieval of property values in a controlled manner.
     * If the specified property exists within the Wine class, its value is returned.
     * Otherwise, the method attempts to retrieve the property from the parent class if applicable.
     *
     * @param string $property The name of the property to access.
     * @return mixed The value of the specified property if it exists, otherwise the value from the parent class.
     *
     * Example Usage:
     * ```
     * $wine = new Wine(name: "Pinot Noir");
     * echo $wine->__get('name'); // Outputs: Pinot Noir
     * ```
     */
    public function __get(string $property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
        return parent::__get($property);
    }

    /**
     * Magic setter method for setting the properties of the Wine object.
     * This method enables setting values for specific properties with validation to ensure data integrity.
     * It throws an InvalidArgumentException if an invalid type is provided for certain properties.
     *
     * Supported Properties:
     * - glassPrice (float): The price of a glass of wine. Throws InvalidArgumentException if not a float.
     * - alcoholPercentage (float): The alcohol content percentage. Throws InvalidArgumentException if not a float.
     * - domain (string): The domain or brand of the wine. Throws InvalidArgumentException if not a string.
     * - grapeVariety (string): The grape variety. Throws InvalidArgumentException if not a string.
     * - origin (string): The geographical origin of the wine. Throws InvalidArgumentException if not a string.
     * - year (int): The vintage year. Throws InvalidArgumentException if not an integer.
     *
     * @param string $property The name of the property to set.
     * @param mixed $value The value to assign to the property.
     * @throws InvalidArgumentException if an invalid type is provided for the property.
     *
     * Example Usage:
     * ```
     * $wine = new Wine();
     * $wine->__set('domain', 'Bordeaux');
     * $wine->__set('year', 2016);
     * ```
     */
    public function __set(string $property, mixed $value)
    {
        switch ($property) {
            case 'glassPrice':
            case 'alcoholPercentage':
                if (!is_float($value)) {
                    throw new InvalidArgumentException("glassPrice must be a float.");
                }
                $this->$property = $value;
                break;
            case 'domain':
            case 'grapeVariety':
            case 'origin':
                if (!is_string($value)) {
                    throw new InvalidArgumentException("domain must be a string.");
                }
                $this->$property = $value;
                break;
            case 'year':
                if (!is_int($value)) {
                    throw new InvalidArgumentException("year must be an integer.");
                }
                $this->$property = $value;
                break;
            default:
                parent::__set($property, $value);
        }
    }

    /**
     * Retrieves wine entries from the database based on the specified color.
     * If no color is specified, it fetches all wine records.
     * 
     * @param WineColor|null $color The color of the wine to filter by, or null to fetch all wines.
     * @return object|array An array of Wine objects, or a single Wine object if only one record is found.
     *
     * Example Usage:
     * ```
     * $redWines = Wine::getWinesByColor(WineColor::RED);
     * $allWines = Wine::getWinesByColor(null); // Gets all wines
     * ```
     */
    public static function getWinesByColor(?WineColor $color = null): object|array
    {
        $sql = "SELECT id,
                       name, 
                       price,
                       spotlight,
                       glassPrice, 
                       domain, 
                       grapeVariety,
                       origin, 
                       alcoholPercentage, 
                       year,
                       color,
                       stock,
                       bottleType
                FROM VIEW_WINE";

        // Add a WHERE clause if a specific wine color is requested
        if ($color !== null) {
            $sql .= " WHERE color = '{$color->value}'";
        }

        $wines = DB_Connection::query($sql, [], self::class);

        // Return an array with a single Wine object if only one record is found
        if (is_object($wines)) {
            return [$wines];
        }

        return $wines;
    }

    /**
     * Formats and displays the wine's information in a structured layout suitable for a menu presentation.
     * This method creates a string that includes the wine's name, domain, grape variety, origin, alcohol percentage, year,
     * and price. It is designed to be easily readable and presentable in a menu context.
     *
     * The resulting string is structured with HTML tags, enabling it to be directly inserted into a web page.
     * The wine's name is displayed as a header, followed by its characteristics in an italicized format,
     * and concluding with the price prominently displayed.
     *
     * @return string A formatted string containing the wine's information ready for display in a menu.
     *
     * Usage Example:
     * ```
     * $wine = new Wine(name: "Chardonnay", price: 20.0, glassPrice: 5.0, domain: "Napa Valley", grapeVariety: "Chardonnay", origin: "California", alcoholPercentage: 13.5, year: 2018, color: "WHITE");
     * echo $wine->displayInMenu();
     * ```
     *
     * Output Example:
     * ```
     * <h4>Chardonnay</h4>
     * <i><p>Napa Valley, Chardonnay, California, 13.5%, 2018</p></i><br>
     * <p><strong>Price: 20.0 €</strong></p>
     * ```
     *
     * Note: This method assumes that the Wine object has been properly initialized with all necessary properties.
     */
    public function displayInMenu(): string
    {
        $specs = "<p>$this->domain, $this->grapeVariety, $this->origin, " .
            "$this->alcoholPercentage%, $this->year</p>";

        return "<h4>$this->name</h4>" .
            "<i>$specs</i><br>" .
            "<p><strong>$this->price €</strong></p>";
    }

    /**
     * Retrieves a description of the wine.
     *
     * This method returns a string that describes the wine, including its color, origin, grape variety,
     * domain, alcohol percentage, and year. It is useful for providing a detailed description of the wine
     * in a context where a more verbose description is needed.
     *
     * @return string A description of the wine.
     */
    public function getDescription(): string
    {
        return '<p>' . ucfirst(strtolower($this->color)) . ' wine from ' . $this->domain . '(' . $this->origin . ')</p>';
    }

    public function getLongDescription(): string
    {
        return "This {$this->color} wine, named '{$this->domain}', is a fine example of {$this->grapeVariety} from {$this->origin}. "
            . "Produced in the year {$this->year}, it boasts an alcohol content of {$this->alcoholPercentage}%. "
            . "It's a delightful choice with a stock of {$this->stock} bottles currently available.";
    }

    /**
     * Retrieves a Wine instance from the database by its ID.
     *
     * @param int $id
     * @return object|array
     * @throws InvalidArgumentException If the provided ID does not match any wine in the database.
     */
    public static function getById(int $id): object|array
    {
        $sql = "SELECT id,
                       name, 
                       price,
                       spotlight,
                       glassPrice, 
                       domain, 
                       grapeVariety,
                       origin, 
                       alcoholPercentage, 
                       year,
                       color,
                       stock,
                       bottleType
                    FROM VIEW_WINE
                    WHERE id = :id";

        $results = DB_Connection::query($sql, ['id' => $id], self::class);

        if (count($results) === 1) {
            return $results[0];
        } else {
            return $results;
        }
    }

    /**
     * Retrieves all spotlight wine entries from the database.
     * @return array An array of Wine objects or a single Wine object if only one record is found.
     */
    public static function getSpotlightWines(): array
    {
        $sql = "SELECT id,
                       name, 
                       price,
                       spotlight,
                       glassPrice, 
                       domain, 
                       grapeVariety,
                       origin, 
                       alcoholPercentage, 
                       year,
                       color 
                    FROM VIEW_WINE
                    WHERE spotlight = 1";

        $wines = DB_Connection::query($sql, [], self::class);

        if (is_object($wines)) {
            return [$wines];
        }

        return $wines;
    }

    /**
     * Generates a string of HTML code to display a form for editing a Wine instance.
     * @return string The HTML code to display the form.
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public static function generateSpecificSection()
    {
        $bottleTypes = ['BOTTLE', 'PICCOLO', 'MAGNUM', 'JEROBOAM', 'REHOBOAM', 'MATHUSALEM'];
        $color = ['RED', 'WHITE', 'ROSE'];

        $html = '<div class="form-group">';
        $html .= '<label for="color">Color</label>';
        $html .= '<select id="color" name="color">';
        foreach ($color as $c) {
            $html .= '<option value="' . htmlspecialchars($c) . '">' . htmlspecialchars(ucfirst(strtolower($c))) . '</option>';
        }
        $html .= '</select>';
        $html .= '</div>';

        $html .= '<div class="form-group">';
        $html .= '<label for="bottleType">Type de Bouteille</label>';
        $html .= '<select id="bottleType" name="bottleType">';

        foreach ($bottleTypes as $type) {
            $html .= '<option value="' . htmlspecialchars($type) . '">' . htmlspecialchars(ucfirst(strtolower($type))) . '</option>';
        }

        $html .= '</select>';
        $html .= '</div>';

        return $html;
    }
}
