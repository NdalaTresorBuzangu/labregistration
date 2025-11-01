<?php

session_start();

header('Content-Type: application/json');

require_once __DIR__ . '/../controllers/product_controller.php';
require_once __DIR__ . '/../controllers/category_controller.php';

$action = $_GET['action'] ?? $_POST['action'] ?? 'list';

try {
    switch ($action) {
        case 'meta':
            echo json_encode([
                'success' => true,
                'data' => getMetaPayload(),
            ]);
            break;

        case 'single':
            $productId = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['id']) ? (int)$_POST['id'] : 0);
            if ($productId <= 0) {
                throw new RuntimeException('Invalid product identifier supplied.');
            }

            $product = view_single_product_public_ctr($productId);
            echo json_encode([
                'success' => (bool)$product,
                'data' => $product,
                'message' => $product ? 'Product retrieved successfully' : 'Product not found',
            ]);
            break;

        case 'search':
            $query = trim($_GET['q'] ?? $_POST['q'] ?? '');
            if ($query === '') {
                throw new RuntimeException('Search query is required.');
            }

            $filters = collectFilters();
            $pagination = collectPagination();

            $products = search_products_public_ctr($query, $filters, $pagination['limit'], $pagination['offset']);
            $total = count_products_public_ctr(array_merge($filters, ['query' => $query]));

            echo json_encode([
                'success' => true,
                'data' => $products,
                'meta' => array_merge(buildPaginationMeta($total, $pagination['limit'], $pagination['page']), [
                    'total' => $total,
                    'filters' => $filters,
                    'query' => $query,
                    'resources' => getMetaPayload(),
                ]),
            ]);
            break;

        case 'list':
        default:
            $filters = collectFilters();
            $pagination = collectPagination();

            $products = list_products_public_ctr($filters, $pagination['limit'], $pagination['offset']);
            $total = count_products_public_ctr($filters);

            echo json_encode([
                'success' => true,
                'data' => $products,
                'meta' => array_merge(buildPaginationMeta($total, $pagination['limit'], $pagination['page']), [
                    'total' => $total,
                    'filters' => $filters,
                    'resources' => getMetaPayload(),
                ]),
            ]);
            break;
    }
} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
    ]);
}

function collectFilters(): array
{
    $filters = [];

    if (isset($_GET['category_id']) || isset($_POST['category_id'])) {
        $filters['category_id'] = (int)($_GET['category_id'] ?? $_POST['category_id']);
    }

    if (isset($_GET['brand_id']) || isset($_POST['brand_id'])) {
        $filters['brand_id'] = (int)($_GET['brand_id'] ?? $_POST['brand_id']);
    }

    if (isset($_GET['price_min']) || isset($_POST['price_min'])) {
        $filters['price_min'] = (float)($_GET['price_min'] ?? $_POST['price_min']);
    }

    if (isset($_GET['price_max']) || isset($_POST['price_max'])) {
        $filters['price_max'] = (float)($_GET['price_max'] ?? $_POST['price_max']);
    }

    if (isset($_GET['keyword']) || isset($_POST['keyword'])) {
        $filters['keywords'] = trim($_GET['keyword'] ?? $_POST['keyword']);
    }

    if (isset($_GET['keywords']) || isset($_POST['keywords'])) {
        $filters['keywords'] = trim($_GET['keywords'] ?? $_POST['keywords']);
    }

    return array_filter($filters, static function ($value) {
        if (is_numeric($value)) {
            return true;
        }
        return $value !== '' && $value !== null;
    });
}

function collectPagination(): array
{
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : (isset($_POST['limit']) ? (int)$_POST['limit'] : 12);
    $page = isset($_GET['page']) ? (int)$_GET['page'] : (isset($_POST['page']) ? (int)$_POST['page'] : 1);

    $limit = max(1, min($limit, 60));
    $page = max(1, $page);
    $offset = ($page - 1) * $limit;

    return [
        'limit' => $limit,
        'page' => $page,
        'offset' => $offset,
    ];
}

function buildPaginationMeta(int $total, int $limit, int $page): array
{
    $pages = (int)ceil($total / $limit);

    return [
        'limit' => $limit,
        'page' => $page,
        'pages' => max(1, $pages),
        'has_more' => $page < max(1, $pages),
    ];
}

function getMetaPayload(): array
{
    $categories = fetch_category_ctr();
    $brands = list_all_brands_ctr();

    return [
        'categories' => is_array($categories) ? $categories : [],
        'brands' => is_array($brands) ? $brands : [],
    ];
}

?>

