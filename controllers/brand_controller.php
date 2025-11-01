<?php

require_once '../classes/brand_class.php';

$brand = new Brand();

function add_brand_ctr(int $userId, int $categoryId, string $name): array
{
    global $brand;
    return $brand->add($userId, $categoryId, $name);
}

function update_brand_ctr(int $userId, int $brandId, string $name): array
{
    global $brand;
    return $brand->update($userId, $brandId, $name);
}

function delete_brand_ctr(int $userId, int $brandId): array
{
    global $brand;
    return $brand->delete($userId, $brandId);
}

function fetch_brands_grouped_ctr(int $userId): array
{
    global $brand;
    return $brand->getGroupedByCategory($userId);
}

function get_brand_by_id_ctr(int $userId, int $brandId): ?array
{
    global $brand;
    return $brand->getById($brandId, $userId);
}

?>

