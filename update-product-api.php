<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: PUT");

$update_product = "all-products.json";

$products = file_exists($update_product) ? json_decode(file_get_contents($update_product), true) : [];

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" => "Product ID is required."
    ]);
    exit;
}

$productId = (int)$_GET['id'];

$pro_req = json_decode(file_get_contents("php://input"), true);

if (!$pro_req || !is_array($pro_req)) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" => "Invalid JSON input."
    ]);
    exit;
}

$productIndex = null;
foreach ($products as $index => $product) {
    if ($product['id'] == $productId) {
        $productIndex = $index;
        break;
    }
}

if ($productIndex === null) {
    http_response_code(404);
    echo json_encode([
        "status" => "error",
        "message" => "Product not found."
    ]);
    exit;
}

$allowedFields = ['name', 'description', 'price', 'quantity'];

foreach ($pro_req as $key => $value) {
    if (in_array($key, $allowedFields)) {
        
        if ($key == 'price' || $key == 'quantity') {
            if (!is_numeric($value) || $value < 0) {
                http_response_code(400);
                echo json_encode([
                    "status" => "error",
                    "message" => ucfirst($key) . " must be a positive number."
                ]);
                exit;
            }
            $products[$productIndex][$key] = (float)$value;
        } else {
            $products[$productIndex][$key] = trim($value);
        }
    }
}

file_put_contents($update_product, json_encode($products, JSON_PRETTY_PRINT));

http_response_code(200);
echo json_encode([
    "status" => "success",
    "data" => $products[$productIndex]
]);
?>
