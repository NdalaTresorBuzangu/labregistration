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

function list_products_public_ctr(array $filters = [], ?int $limit = null, int $offset = 0): array
{
    global $product;
    return $product->viewAllProducts($limit, $offset, $filters);
}

function search_products_public_ctr(string $query, array $filters = [], ?int $limit = null, int $offset = 0): array
{
    global $product;
    return $product->searchProducts($query, $limit, $offset, $filters);
}

function filter_products_by_category_ctr(int $categoryId, array $filters = [], ?int $limit = null, int $offset = 0): array
{
    global $product;
    return $product->filterProductsByCategory($categoryId, $limit, $offset, $filters);
}

function filter_products_by_brand_ctr(int $brandId, array $filters = [], ?int $limit = null, int $offset = 0): array
{
    global $product;
    return $product->filterProductsByBrand($brandId, $limit, $offset, $filters);
}

function view_single_product_public_ctr(int $productId): ?array
{
    global $product;
    return $product->viewSingleProduct($productId);
}

function count_products_public_ctr(array $filters = []): int
{
    global $product;
    return $product->countProducts($filters);
}

function list_all_brands_ctr(): array
{
    global $product;
    return $product->listAllBrands();
}

function add_product_gallery_image_ctr(int $userId, int $productId, string $imagePath): array
{
    global $product;
    return $product->addGalleryImage($userId, $productId, $imagePath);
}

function get_product_gallery_images_ctr(int $userId, array $productIds): array
{
    global $product;
    return $product->getGalleryImagesForProducts($userId, $productIds);
}

function delete_product_gallery_image_ctr(int $userId, int $imageId): bool
{
    global $product;
    return $product->deleteGalleryImage($userId, $imageId);
}

?>

