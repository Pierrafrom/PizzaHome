<?php

namespace App\models;

/**
 * Enum WineColor
 *
 * Represents the color of a wine.
 *
 * Enums allow the definition of a set of named constants and are used to represent
 * distinct values that a variable can hold, in this case, the color of a wine.
 * PHP 8.1 introduced enums, offering a native way to define a list of possible values.
 *
 * @package App\models
 */
enum WineColor : string
{
    /**
     * Represents a red wine color.
     */
    case RED = 'RED';

    /**
     * Represents a white wine color.
     */
    case WHITE = 'WHITE';

    /**
     * Represents a rosé wine color.
     */
    case ROSE = 'ROSE';
}

