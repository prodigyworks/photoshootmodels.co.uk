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

    $input = json_decode(file_get_contents("php://input"), true);

    $paymentMethod = $input['payment_method'];
    $deposit = floatval($input['totalDeposit'] ?? 50); // Default to £50 if not provided
    $amount = intval($deposit * 100);

    try {
        $intent = \Stripe\PaymentIntent::create([
            'amount' => $amount,
            'currency' => 'gbp',
            'capture_method' => 'manual',
            'automatic_payment_methods' => [
                'enabled' => true,
                'allow_redirects' => 'never'
            ],
            'payment_method' => $paymentMethod,
            'confirm' => true,
            'metadata' => [
                'firstName' => $input['firstName'],
                'lastName' => $input['lastName'],
                'parentName' => $input['parentName'],
                'email' => $input['email'],
                'age' =>  $input['age'],
                'postcode' => $input['postcode'],
                'gender' => $input['gender'],
                'phone' => $input['phone'],
                'date' => $input['date'],
                'time' => $input['time'],
                'address' => $input['address'],
                'notes' => $input['notes'],
                'deposit' => $deposit
            ]
        ]);

        echo json_encode([
            'success' => true,
            'payment_intent' => $intent->id,
            'status' => $intent->status
        ]);

    } catch (\Stripe\Exception\ApiErrorException $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
