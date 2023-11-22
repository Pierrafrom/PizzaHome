<?php

namespace App\controllers;

use App\helpers\SessionHelper;

class CartController extends Controller
{
    /**
     * Adds a product to the shopping cart.
     *
     * This method adds a product, identified by its ID and type, to the shopping cart stored in the session.
     * If the cart doesn't exist in the session, it's initialized.
     *
     * @param int $productId The ID of the product to add.
     * @param string $productType The type of the product (e.g., 'pizza', 'soda').
     * @return bool Returns true if the product was successfully added, false otherwise.
     */
    public static function addProductToCart(int $productId, string $productType, int $productQuantity): bool
    {
        SessionHelper::initSession(); // Initialize session variables

        // Check if the cart exists in the session
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = []; // Initialize the cart if it doesn't exist
        }

        // Check if this type of product already exists in the cart
        if (!isset($_SESSION['cart'][$productType])) {
            $_SESSION['cart'][$productType] = []; // Initialize this type of product in the cart
        }

        // Add or update the product in the cart
        $productKey = 'product_' . $productId; // Create a unique key for each product
        if (!isset($_SESSION['cart'][$productType][$productKey])) {
            // Add the product to the cart if it doesn't exist
            $_SESSION['cart'][$productType][$productKey] = ['quantity' => $productQuantity];
        } else {
            // Update the product quantity if it already exists
            $_SESSION['cart'][$productType][$productKey]['quantity'] += $productQuantity;
        }

        return true; // Return true indicating the product was added successfully
    }
}
