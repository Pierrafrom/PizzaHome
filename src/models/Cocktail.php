<?php

namespace App\models;

class Cocktail extends Food
{
    /**
     * @var int Static counter to automatically generate unique identifiers.
     */
    private static int $autoIncrementId = 1;

    /**
     * @var array An array of ingredients included in the cocktail item.
     */
    private array $ingredients = [];

    /**
     * @var float alcohol percentage of the cocktail.
     */
    private float $alcoholPercentage;

    // TODO: Create the class Dessert inspired by the class Pizza.
}