<?php

namespace App\models;

class Dessert extends Food
{
    /**
     * @var int Static counter to automatically generate unique identifiers.
     */
    private static int $autoIncrementId = 1;

    /**
     * @var array An array of ingredients included in the dessert item.
     */
    private array $ingredients = [];


    // TODO: Create the class Dessert inspired by the class Pizza.
}