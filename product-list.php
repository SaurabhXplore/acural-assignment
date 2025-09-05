<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET");

$dataFile = "all-products.json";
$products = file_exists($dataFile) ? json_decode(file_get_contents($dataFile), true) : [];

if (empty($products)) {
    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "message" => "No products available.",
        "data" => []
    ]);
    exit;
}

if (isset($_GET['id'])) {
    $productId = (int)$_GET['id'];
    $productFound = null;

    foreach ($products as $product) {
        if ($product['id'] == $productId) {
            $productFound = $product;
            break;
        }
    }

    if ($productFound) {
        http_response_code(200);
        echo json_encode([
            "status" => "success",
            "data" => $productFound
        ]);
    } else {
        http_response_code(404);
        echo json_encode([
            "status" => "error",
            "message" => "Product not found."
        ]);
    }
} else {
    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "data" => $products
    ]);
}
?>
