<?php

require 'vendor/autoload.php';

Symfony\Component\ErrorHandler\Debug::enable();

use Onetoweb\Exact\Client;
use Onetoweb\Exact\Exception\RequestException;

$bearer = 'bearer';

$client = new Client($bearer);

// returns validation errors in dutch
$client->setLanguage('nl');

try {
    
    // get product stock
    $results = $client->getProductStocks();
    $products = $results['products'];
    
    // get products
    $page = 1;
    $results = $client->getProducts($page);
    $products = $results['products'];
    
    // get product
    $sku = 'product sku';
    $results = $client->getProduct($sku);
    $product = $results['product'];
    
    // create order
    $results = $client->createOrder([
        'description' => 'test order',
        'your_ref' => 'test order',
        'remarks' => 'test order',
        'delivery_date' => '2021-01-01T12:00:00',
        'delivery_address' => [
            'address_line_1' => 'street number extension',
            'address_line_2' => null,
            'address_line_3' => null,
            'postcode' => '1000AA',
            'city' => 'city',
            'country' => 'NL'
        ],
        'order_lines' => [[
            'item_code' => 'item code',
            'quantity' => 2,
            'notes' => 'test order item 1',
        ], [
            'item_code' => 'item code',
            'quantity' => 1,
            'notes' => 'test order item 2',
        ]],
    ]);
    $order = $results['order'];
    
    // get order
    $orderNumber = 42;
    $results = $client->getOrder($orderNumber);
    $order = $results['order'];
    
} catch (RequestException $requestException) {
    
    // contains json with error messages
    $errors = json_decode($requestException->getMessage());
}