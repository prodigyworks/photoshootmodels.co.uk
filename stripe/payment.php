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
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Booking Deposit | Photo Shoot Models</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://js.stripe.com/v3"></script>
    <style>
        body {
            margin: 0;
            background: #f9f3e9;
            color: #2c2c2c;
            font-family: 'Open Sans', Arial, sans-serif;
            min-height: 100vh;
        }

        .payment-hero {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            text-align: center;
            padding: 30px 16px 30px;
            background: linear-gradient(180deg, rgba(15, 17, 25, 0.70) 0%, rgba(15, 17, 25, 0.18) 100%), url('../images/slide-1.jpg') center / cover no-repeat;
            border-bottom-left-radius: 28px;
            border-bottom-right-radius: 28px;
            overflow: hidden;
        }

        .hero-top {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 12px;
            width: 100%;
        }

        .hero-logo {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            border: 2px solid rgba(255,255,255,0.32);
            background: rgba(255,255,255,0.12);
            padding: 10px;
            box-sizing: border-box;
        }

        .hero-brand {
            color: #ffffff;
            font-size: 12px;
            letter-spacing: .24em;
            text-transform: uppercase;
            opacity: 0.95;
        }

        .hero-headline {
            margin: 14px 0 10px;
            font-size: 30px;
            line-height: 1.05;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: #ffffff;
            max-width: 260px;
        }

        .hero-copy {
            margin: 0 auto;
            max-width: 290px;
            font-size: 13px;
            line-height: 1.7;
            color: rgba(255,255,255,0.86);
        }

        .payment-form-section {
            padding: 16px;
            margin-top: -26px;
        }

        .payment-panel {
            max-width: 420px;
            margin: 0 auto 32px;
            background: #ffffff;
            border-radius: 28px;
            box-shadow: 0 24px 55px rgba(24, 20, 18, 0.12);
            padding: 24px 20px 28px;
            border: 1px solid rgba(171, 139, 109, 0.12);
        }

        .payment-panel h2 {
            margin: 0 0 10px;
            font-size: 22px;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: #1b1a18;
        }

        .payment-panel p.description {
            margin: 0 0 22px;
            font-size: 14px;
            line-height: 1.7;
            color: #726152;
        }

        .mock-card {
            width: 100%;
            margin: 0 auto 22px;
            padding: 20px 18px 18px;
            border-radius: 26px;
            background: linear-gradient(135deg, #2e4b74 0%, #15253f 100%);
            color: #ffffff;
            box-shadow: 0 22px 50px rgba(14, 28, 63, 0.24);
            position: relative;
            overflow: hidden;
            opacity: 0;
            transform: translateY(-20px) scale(0.98);
            animation: cardFadeIn 0.75s ease-out 0.2s forwards;
        }

        .mock-card::before {
            content: '';
            position: absolute;
            top: -12px;
            right: -22px;
            width: 88px;
            height: 88px;
            background: rgba(255,255,255,0.08);
            border-radius: 50%;
        }

        @keyframes cardFadeIn {
            0% { opacity: 0; transform: translateY(-20px) scale(0.98); }
            60% { opacity: 1; transform: translateY(8px) scale(1.005); }
            100% { opacity: 1; transform: translateY(0) scale(1); }
        }

        .mock-card-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .mock-chip {
            width: 48px;
            height: 38px;
            border-radius: 12px;
            background: rgba(255,255,255,0.18);
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.14);
        }

        .mock-card-number {
            font-size: 16px;
            letter-spacing: 0.28em;
            margin-bottom: 16px;
        }

        .mock-card-data {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            font-size: 12px;
            text-transform: uppercase;
            opacity: 0.88;
        }

        .mock-card-data span {
            display: block;
        }

        .mock-card-data strong {
            display: block;
            margin-bottom: 4px;
            font-size: 9px;
            color: rgba(255,255,255,0.68);
            letter-spacing: 0.14em;
        }

        #card-element {
            border: 1px solid #e9e2d9;
            border-radius: 20px;
            padding: 18px 18px;
            background: #fff7f0;
            margin-bottom: 22px;
        }

        #payBtn {
            width: 100%;
            min-height: 52px;
            border: none;
            border-radius: 18px;
            background: #e66023;
            color: #ffffff;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: .14em;
            text-transform: uppercase;
            cursor: pointer;
            transition: background 0.2s ease;
            box-shadow: 0 8px 18px rgba(230, 96, 35, 0.22);
        }

        #payBtn:hover {
            background: #d64d14;
        }

        #result {
            margin-top: 18px;
            color: #444;
            font-size: 14px;
            white-space: pre-wrap;
            word-break: break-word;
            min-height: 24px;
        }

        .success-box {
            display: flex;
            gap: 14px;
            align-items: flex-start;
            padding: 22px;
            border-radius: 22px;
            background: #f7fbff;
            border: 1px solid #cfe7ff;
            box-shadow: 0 16px 32px rgba(30, 45, 75, 0.08);
        }

        .success-icon {
            flex-shrink: 0;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #0b74e1;
            color: #ffffff;
            display: grid;
            place-items: center;
            font-size: 22px;
            font-weight: 700;
        }

        .success-copy h3 {
            margin: 0 0 8px;
            font-size: 18px;
            color: #111111;
        }

        .success-copy p {
            margin: 0;
            font-size: 14px;
            line-height: 1.7;
            color: #4f4f4f;
        }

        .error-box {
            padding: 18px 20px;
            border-radius: 18px;
            background: #ffe9e9;
            border: 1px solid #f5c2c2;
            color: #8a1f1f;
        }

        @media (max-width: 560px) {
            .payment-hero {
                min-height: 260px;
                padding: 24px 14px 24px;
            }

            .hero-headline {
                font-size: 24px;
                max-width: 240px;
            }

            .hero-copy {
                max-width: 260px;
                font-size: 13px;
            }

            .payment-panel {
                margin-top: -50px;
                padding: 22px 16px 24px;
            }

            .mock-card {
                padding: 18px 16px 16px;
            }

            .mock-card-number {
                font-size: 15px;
            }

            .mock-card-data {
                gap: 10px;
                font-size: 11px;
            }

            .payment-panel h2 {
                font-size: 20px;
            }

            #payBtn {
                min-height: 50px;
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
        <div class="hero-top">
            <img class="hero-logo" src="../images/logomini.png" alt="Photoshoot Models logo">
            <div class="hero-brand">Photoshoot Models</div>
        </div>
        <h1 class="hero-headline">Book your studio shoot</h1>
        <p class="hero-copy">Confirm a deposit and secure your chosen date with a fast, mobile-friendly payment experience.</p>
    </header>

    <section class="payment-form-section">
        <div class="payment-panel">
            <h2>Booking Deposit</h2>
            <p class="description">Secure your studio booking with a £50 deposit. Enter your card details below and place the payment on hold safely through Stripe.</p>
            <div class="mock-card">
                <div class="mock-card-row">
                    <div>
                        <div style="font-size:12px; letter-spacing:.18em; text-transform:uppercase; opacity:.86;">Photo Shoot Models</div>
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
                    '<strong>Server error:</strong> Please try again later.' +
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
