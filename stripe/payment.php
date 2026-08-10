<?php
    // Allow CORS for your Flutter Web app
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: Content-Type");
    header("Access-Control-Allow-Methods: POST");

    // Load Stripe
    require __DIR__ . '/vendor/autoload.php';
    require __DIR__ . '/prodigyworks.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Booking Deposit | Photo Shoot Models</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://js.stripe.com/v3"></script>
    <style>
        body {
            background: #f5f5f5;
            color: #2c2c2c;
            font-family: 'Open Sans', Arial, sans-serif;
            margin: 0;
        }

        .payment-hero {
            position: relative;
            min-height: 520px;
            background: linear-gradient(180deg, rgba(34,34,34,0.52) 0%, rgba(34,34,34,0.52) 100%), url('../images/slide-1.jpg') center / cover no-repeat;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 80px 20px;
        }

        .payment-hero .hero-copy {
            max-width: 860px;
            width: 100%;
            color: #fff;
        }

        .payment-hero .hero-label {
            display: inline-block;
            margin-bottom: 24px;
            padding: 11px 28px;
            border: 1px solid rgba(255,255,255,0.8);
            letter-spacing: .24em;
            font-size: 12px;
            text-transform: uppercase;
            color: #fff;
        }

        .payment-hero h1 {
            font-family: 'Dorsa', serif;
            font-size: 58px;
            line-height: 1.02;
            letter-spacing: .24em;
            margin: 0 0 22px;
            text-transform: uppercase;
        }

        .payment-hero p {
            margin: 0 auto;
            max-width: 640px;
            font-size: 18px;
            line-height: 1.8;
            color: rgba(255,255,255,0.88);
        }

        .payment-form-section {
            padding: 100px 20px 140px;
        }

        .payment-panel {
            max-width: 760px;
            margin: -90px auto 0;
            background: #fff;
            border-radius: 28px;
            box-shadow: 0 28px 80px rgba(33, 33, 33, 0.12);
            padding: 46px 50px;
            border: 1px solid rgba(0, 0, 0, 0.04);
        }

        .payment-panel h2 {
            margin: 0 0 14px;
            font-size: 32px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #111111;
        }

        .payment-panel p.description {
            margin: 0 0 24px;
            font-size: 16px;
            line-height: 1.8;
            color: #6c6c6c;
        }

        .mock-card {
            width: 100%;
            max-width: 420px;
            margin: 0 auto 28px;
            padding: 24px 24px 20px;
            border-radius: 28px;
            background: linear-gradient(135deg, #1a2a48 0%, #0b1a34 100%);
            color: #fff;
            box-shadow: 0 24px 60px rgba(15, 31, 71, 0.24);
            position: relative;
            overflow: hidden;
            opacity: 0;
            transform: translateY(-24px) scale(0.98);
            animation: cardFadeIn 0.75s ease-out 0.25s forwards;
        } 

        .mock-card::before {
            content: '';
            position: absolute; 
            top: -20px;
            right: -30px;
            width: 120px;
            height: 120px;
            background: rgba(255,255,255,0.08);
            border-radius: 50%;
        }

        @keyframes cardFadeIn {
            0% {
                opacity: 0;
                transform: translateY(-24px) scale(0.98);
            }
            60% {
                opacity: 1;
                transform: translateY(8px) scale(1.005);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .mock-card-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 26px;
        }

        .mock-chip {
            width: 52px;
            height: 40px;
            border-radius: 10px;
            background: rgba(255,255,255,0.24);
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.15);
        }

        .mock-card-number {
            font-size: 18px;
            letter-spacing: 0.3em;
            margin-bottom: 18px;
        }

        .mock-card-data {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            font-size: 13px;
            text-transform: uppercase;
            opacity: 0.88;
        }

        .mock-card-data span {
            display: block;
        }

        .mock-card-data strong {
            display: block;
            margin-bottom: 4px;
            font-size: 10px;
            color: rgba(255,255,255,0.72);
            letter-spacing: 0.16em;
        }

        #card-element {
            border: 1px solid #e5e5e5;
            border-radius: 18px;
            padding: 18px 20px;
            background: #fafafa;
            margin-bottom: 28px;
        }

        #payBtn {
            width: 100%;
            min-height: 56px;
            border: none;
            border-radius: 14px;
            background: #111111;
            color: #ffffff;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        #payBtn:hover {
            background: #333333;
        }

        #result {
            margin-top: 24px;
            color: #444;
            font-size: 14px;
            white-space: pre-wrap;
            word-break: break-word;
            min-height: 24px;
        }

        .success-box {
            display: flex;
            gap: 16px;
            align-items: flex-start;
            padding: 28px;
            border-radius: 22px;
            background: #f7fbff;
            border: 1px solid #cfe7ff;
            box-shadow: 0 16px 35px rgba(30, 45, 75, 0.08);
        }

        .success-icon {
            flex-shrink: 0;
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: #0b74e1;
            color: #ffffff;
            display: grid;
            place-items: center;
            font-size: 24px;
            font-weight: 700;
        }

        .success-copy h3 {
            margin: 0 0 10px;
            font-size: 22px;
            color: #111111;
        }

        .success-copy p {
            margin: 0;
            font-size: 15px;
            line-height: 1.8;
            color: #4f4f4f;
        }

        .error-box {
            padding: 18px 20px;
            border-radius: 18px;
            background: #ffe9e9;
            border: 1px solid #f5c2c2;
            color: #8a1f1f;
        }

        @media (max-width: 840px) {
            .payment-panel {
                margin: -70px 0 0;
                padding: 36px 28px;
            }

            .payment-hero {
                min-height: 420px;
                padding: 60px 18px;
            }

            .payment-hero h1 {
                font-size: 44px;
            }
        }

        @media (max-width: 560px) {
            .payment-hero {
                min-height: 340px;
                padding: 42px 18px;
            }

            .payment-hero h1 {
                font-size: 32px;
            }

            .payment-panel {
                margin: -60px 0 0;
                padding: 28px 20px;
            }

            .mock-card {
                padding: 20px 18px 18px;
            }

            .mock-card-number {
                font-size: 16px;
            }

            .mock-card-data {
                gap: 12px;
                font-size: 12px;
            }

            .success-box {
                flex-direction: column;
                align-items: stretch;
            }
        }
    </style>
</head>
<body>
    <header class="payment-hero">
        <div class="hero-copy">
            <span class="hero-label">London Studio Visit</span>
            <h1>Photo Shoot Models</h1>
            <p>Expert photographers and stylists dedicated to capturing your potential.</p>
        </div>
    </header>

    <section class="payment-form-section">
        <div class="payment-panel">
            <h2>Booking Deposit</h2>
            <p class="description">Secure your studio booking with a £50 deposit. Enter your card details below and place the payment on hold safely through Stripe.</p>

            <div class="mock-card">
                <div class="mock-card-row">
                    <div>
                        <div style="font-size:12px; letter-spacing:.16em; text-transform:uppercase; opacity:.88;">Photo Shoot Models</div>
                    </div>
                    <div class="mock-chip"></div>
                </div>
                <div class="mock-card-number">5210 4500 1234 0000</div>
                <div class="mock-card-data">
                    <span><strong>Card holder</strong>Studio Visitor</span>
                    <span><strong>Expires</strong>12/28</span>
                    <span><strong>Postcode</strong>SW1A 1AA</span>
                </div>
            </div>

            <div id="card-element"></div>
            <button id="payBtn" type="button">Place Payment On Hold</button>
            <div id="result"></div>
        </div>
    </section>

    <script>
        const stripe = Stripe("<?php echo STRIPE_PUBLIC_KEY; ?>", { locale: 'en-GB' });
        const elements = stripe.elements();
        const card = elements.create("card", {
            hidePostalCode: false,
            style: {
                base: {
                    color: '#111111',
                    fontSize: '16px',
                    fontFamily: 'Arial, sans-serif',
                    '::placeholder': { color: '#a8a8a8' },
                },
                invalid: { color: '#e12a50' }
            }
        });
        card.mount("#card-element");

        const resultContainer = document.getElementById('result');

        document.getElementById("payBtn").onclick = async () => {
            resultContainer.innerHTML = '';
            const { paymentMethod, error } = await stripe.createPaymentMethod({
                type: 'card',
                card: card
            });

            if (error) {
                resultContainer.innerHTML = '<div class="error-box">' +
                    '<strong>Payment failed:</strong> ' + error.message +
                    '</div>';
                return;
            }

            const response = await fetch('create_hold.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    payment_method: paymentMethod.id,
                    totalDeposit: 50.00,
                    firstName: "<?php echo $_POST['firstName'] ?? ''; ?>",
                    lastName: "<?php echo $_POST['lastName'] ?? ''; ?>",
                    email: "<?php echo $_POST['email'] ?? ''; ?>",
                    phone: "<?php echo $_POST['phone'] ?? ''; ?>",
                    date: "<?php echo $_POST['date'] ?? ''; ?>",
                    time: "<?php echo $_POST['time'] ?? ''; ?>"
                })
            });

            if (!response.ok) {
                resultContainer.innerHTML = '<div class="error-box">' +
                    '<strong>Server error:</strong> Please try again later.</n' +
                    '</div>';
                return;
            }

            const data = await response.json();
            resultContainer.innerHTML =
                '<div class="success-box">' +
                    '<div class="success-icon">✓</div>' +
                    '<div class="success-copy">' +
                        '<h3>Thank you for your payment</h3>' +
                        '<p>Your deposit is now secured. You will receive a confirmation email shortly with the booking details and next steps.</p>' +
                    '</div>' +
                '</div>';
        };
    </script>
</body>
</html>
