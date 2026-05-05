<?php
    // Allow CORS for your Flutter Web app
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: Content-Type");
    header("Access-Control-Allow-Methods: POST");
    header("Content-Type: application/json");

    // Load Stripe
    require __DIR__ . '/vendor/autoload.php';
    require __DIR__ . '/prodigyworks.php';

    \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY); // <-- Replace with your real key

    // Read JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) {
        echo json_encode(['error' => 'Invalid JSON']);
        exit;
    }

    // Extract fields
    $firstName = $input['firstName'] ?? '';
    $lastName  = $input['lastName'] ?? '';
    $email     = $input['email'] ?? '';
    $phone     = $input['phone'] ?? '';
    $address   = $input['address'] ?? '';
    $notes     = $input['notes'] ?? '';
    $date      = $input['date'] ?? '';
    $time      = $input['time'] ?? '';

    // Create Stripe Checkout Session
    try {
        $session = \Stripe\Checkout\Session::create([
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'gbp',
                    'unit_amount' => 5000, // £50 deposit
                    'product_data' => [
                        'name' => 'Photo Shoot Deposit',
                        'description' => "$date @ $time",
                    ],
                ],
                'quantity' => 1,
            ]],
            'customer_email' => $email,
            'metadata' => [
                'firstName' => $firstName,
                'lastName' => $lastName,
                'email' => $email,
                'phone' => $phone,
                'address' => $address,
                'notes' => $notes,
                'booking_date' => $date,
                'booking_time' => $time,
            ],
            'success_url' => 'https://www.photoshootmodels.co.uk/stripe/success.php',
            'cancel_url' => 'https://www.photoshootmodels.co.uk/stripe/cancel.php',
        ]);

        echo json_encode(['url' => $session->url]);

    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }