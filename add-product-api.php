<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");

$store_product = "all-products.json";

if (file_exists($store_product)) {
    $products = json_decode(file_get_contents($store_product), true);
} else {
    $products = [];
}


$pro_req = json_decode(file_get_contents("php://input"), true);

$errors = [];

if (!isset($pro_req['name']) || !is_string($pro_req['name']) || strlen($pro_req['name']) > 255) {
    $errors[] = "Invalid or missing 'name'.";
}
if (!isset($pro_req['price']) || !is_numeric($pro_req['price']) || $pro_req['price'] <= 0) {
    $errors[] = "Invalid or missing 'price'.";
}
if (!isset($pro_req['quantity']) || !is_int($pro_req['quantity']) || $pro_req['quantity'] < 0) {
    $errors[] = "Invalid or missing 'quantity'.";
}
if (isset($pro_req['description']) && !is_string($pro_req['description'])) {
    $errors[] = "'description' must be a string.";
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(["errors" => $errors]);
    exit;
}

$newProduct = [
     "id" => count($products) > 0 ? end($products)['id'] + 1 : 1,
    "name" => $pro_req['name'],
    "description" => $pro_req['description'] ?? "",
    "price" => (float) $pro_req['price'],
    "quantity" => (int) $pro_req['quantity']
];


$products[] = $newProduct;

file_put_contents($store_product, json_encode($products, JSON_PRETTY_PRINT));

http_response_code(201);
echo json_encode([
    "message" => "Product created successfully!",
    "product" => $newProduct
]);
