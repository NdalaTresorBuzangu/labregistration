<?php

require_once __DIR__ . '/../classes/cart_class.php';

/**
 * Cart Controller
 * Wraps cart_class methods for use by action scripts
 */
function add_to_cart_ctr(int $productId, int $quantity = 1): array
{
    $cart = new Cart();
    return $cart->addToCart($productId, $quantity);
}

function update_cart_item_ctr(int $productId, int $quantity): array
{
    $cart = new Cart();
    return $cart->updateQuantity($productId, $quantity);
}

function remove_from_cart_ctr(int $productId): array
{
    $cart = new Cart();
    return $cart->removeFromCart($productId);
}

function get_user_cart_ctr(): array
{
    $cart = new Cart();
    return $cart->getUserCart();
}

function get_cart_total_ctr(): float
{
    $cart = new Cart();
    return $cart->getCartTotal();
}

function get_cart_item_count_ctr(): int
{
    $cart = new Cart();
    return $cart->getCartItemCount();
}

function empty_cart_ctr(): array
{
    $cart = new Cart();
    return $cart->emptyCart();
}

function transfer_guest_cart_ctr(int $customerId, string $ipAddress): bool
{
    $cart = new Cart();
    return $cart->transferGuestCartToUser($customerId, $ipAddress);
}

?>

