<?php

require_once '../classes/product_class.php';

$product = new Product();

function add_product_ctr(int $userId, array $payload): array
{
    global $product;
    return $product->add($userId, $payload);
}

function update_product_ctr(int $userId, int $productId, array $payload, ?string $imagePath = null): array
{
    global $product;
    return $product->update($userId, $productId, $payload, $imagePath);
}

function list_products_ctr(int $userId): array
{
    global $product;
    return $product->listByUser($userId);
}

function get_product_ctr(int $userId, int $productId): ?array
{
    global $product;
    return $product->getById($userId, $productId);
}

function update_product_image_ctr(int $userId, int $productId, string $imagePath): array
{
    global $product;
    return $product->updateImagePath($userId, $productId, $imagePath);
}

function delete_product_ctr(int $userId, int $productId): bool
{
    global $product;
    return $product->delete($userId, $productId);
}

?>

