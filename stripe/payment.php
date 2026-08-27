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
            min-height: 25vh;
            padding: 12px 16px 12px;
            overflow: hidden;
        }

        .hero-carousel {
            position: absolute;
            inset: 0;
            display: grid;
            height: 100%;
        }

        .carousel-slide {
            grid-area: 1 / 1;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            opacity: 0;
            transition: opacity 1s ease-in-out;
        }

        .carousel-slide.active {
            opacity: 1;
        }

        .hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 32%;
            background: linear-gradient(180deg, rgba(15, 17, 25, 0.72) 0%, rgba(15, 17, 25, 0.22) 100%);
        }

        .hero-top {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: row;
            margin-top: -18vh;
            gap: 16px;
            width: 100%;
            flex-wrap: wrap;
        }

        .hero-logo {
            width: 68px;
            height: 68px;
            border-radius: 50%;
            border: 2px solid rgba(255,255,255,0.28);
            background: rgba(255,255,255,0.14);
            padding: 10px;
            box-sizing: border-box; 
            flex-shrink: 0;
        }

        .hero-brand {
            color: #ffffff;
            font-size: 12px;
            letter-spacing: .24em;
            text-transform: uppercase;
            opacity: 0.94;
        }

        .hero-headline {
            position: relative;
            z-index: 1;
            margin: 0;
            font-size: 28px;
            line-height: 1.05;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: #ffffff;
            max-width: 320px;
        }

        .hero-copy {
            position: relative;
            z-index: 1;
            margin: 12px auto 0;
            max-width: 280px;
            font-size: 13px;
            line-height: 1.75;
            color: rgba(255,255,255,0.88);
        }

        .payment-form-section {
            padding: 16px;
            margin-top: -24px;
        }

        .payment-panel {
            width: min(100%, 520px);
            max-width: 520px;
            margin: 12px auto 20px;
            border-radius: 24px;
            box-shadow: 0 18px 42px rgba(24, 20, 18, 0.12);
            padding: 20px 16px 24px;
            border: 1px solid rgba(171, 139, 109, 0.12);
        }

        .payment-panel h2 {
            margin: 0 0 10px;
            font-size: 22px;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: #1b1a18;
        }

        .payment-panel .description {
            margin: 0 0 2px;
            font-size: 14px;
            line-height: 1.7;
            color: #726152;
        }

        .mock-card {
            width: 100%;
            max-width: 340px;
            margin: 0 auto 18px;
            padding: 16px 14px 14px;
            border-radius: 24px;
            background: linear-gradient(135deg, #f18f3b 0%, #c65e13 100%);
            color: #ffffff;
            box-shadow: 0 20px 42px rgba(196, 94, 19, 0.24);
            position: relative;
            overflow: hidden;
            opacity: 0;
            transform: translateY(-20px) scale(0.98);
            animation: cardFadeIn 0.8s ease-out 0.2s forwards;
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
            margin-bottom: 18px;
        }

        .mock-chip {
            width: 46px;
            height: 36px;
            border-radius: 12px;
            background: rgba(255,255,255,0.18);
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.14);
        }

        .mock-card-number {
            font-size: 15px;
            letter-spacing: 0.26em;
            margin-bottom: 16px;
        }

        .mock-card-data {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            font-size: 11px;
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
            color: rgba(255,255,255,0.72);
            letter-spacing: 0.12em;
        }

        .field-group {
            margin-bottom: 18px;
        }

        .field-label {
            display: block;
            margin-bottom: 10px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: #4f473b;
        }

        .stripe-element,
        #postcode-input {
            border: 1px solid #e9e2d9;
            border-radius: 18px;
            background: #fff7f0;
            padding: 16px 16px;
            min-height: 52px;
            box-sizing: border-box;
        }

        .field-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        #postcode-input {
            width: 100%;
            font-size: 15px;
            color: #111111;
            outline: none;
        }

        #postcode-input::placeholder {
            color: #b1a495;
        }

        #card-number-element,
        #card-expiry-element,
        #card-cvc-element {
            width: 100%;
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

        /* Button spinner */
        .btn-text {
            vertical-align: middle;
        }

        .spinner {
            display: none;
            width: 18px;
            height: 18px;
            border: 2px solid rgba(255,255,255,0.45);
            border-top-color: #ffffff;
            border-radius: 50%;
            margin-left: 12px;
            vertical-align: middle;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        #result {
            margin-top: 18px;
            color: #444;
            font-size: 14px;
            line-height: 1.6;
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

        .success-button {
            margin-top: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 22px;
            border-radius: 16px;
            border: none;
            background: #0b74e1;
            color: #ffffff;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            cursor: pointer;
            transition: background 0.2s ease, transform 0.2s ease;
        }

        .success-button:hover {
            background: #095fbb;
            transform: translateY(-1px);
        }

        .error-box {
            padding: 18px 20px;
            border-radius: 18px;
            background: #ffe9e9;
            border: 1px solid #f5c2c2;
            color: #8a1f1f;
        }

        @media (max-width: 560px) {
            .hero-top {
                margin-top: -12vh;
            }

            .payment-hero {
                min-height: 20vh;
                padding: 14px 14px 14px;
            }

            .hero-headline {
                font-size: 22px;
                max-width: 240px;
            }

            .hero-copy {
                max-width: 240px;
                font-size: 13px;
            }

            .payment-panel {
                margin-top: -48px;
                padding: 20px 14px 24px;
            }

            .mock-card {
                width: 90%;
                max-width: 100%;
                padding: 14px 12px 12px;
                margin-bottom: 10px;
            }

            .mock-card-number {
                font-size: 14px;
            }

            .mock-card-data {
                gap: 1px;
            }

            .payment-panel h2 {
                font-size: 20px;
            }

            .field-row {
                display: grid;
                grid-template-columns: 1fr;
            }

            #payBtn {
                min-height: 50px;
            }

            .success-box {
                flex-direction: column;
                align-items: stretch;
            }
        }
        .description p {
            padding-top: 3px;
            padding-bottom: 3px;
        }
    </style>
</head>
<body>
    <header class="payment-hero">
        <div class="hero-carousel">
            <div class="carousel-slide active" style="background-image: url('../images/slide-1.jpg');"></div>
            <div class="carousel-slide" style="background-image: url('../images/slide-2.jpg');"></div>
            <div class="carousel-slide" style="background-image: url('../images/slide-3.jpg');"></div>
        </div>
        <div class="hero-overlay"></div>
        <div class="hero-top">
            <img class="hero-logo" src="../images/logomini.png" alt="Photo Shoot Models logo">
            <div>
                <div class="hero-brand">Photo Shoot Models</div>
            </div>
        </div>
    </header>

    <section class="payment-form-section">
        <div class="payment-panel">
            <div class="description">
                <h2>SANDHURST DIGITAL STUDIOS LTD</h2>
                <h4>£50 Deposit - Terms & Conditions</h4>
                <p>Company No. 11664348</p>
                <p>By paying the £50 deposit to Sandhurst Digital Studios Ltd (Company No. 11664348), you agree to the following: </p>
                <p>The £50 deposit is pre-authorised only and secures your appointment, design work and production time. </p>
                <p>The £50 deposit will be allocated back to your account automatically within 14 days.  </p>
                <p>Your booking is confirmed once the deposit has been received.  </p>
                <p>If you cancel your booking with less than 24 hours notice, or do not attend your appointment, the £50 deposit will be retained to cover administration, and reserved production capacity.  </p>
                <p>You will be given the opportunity to rebook automatically on 2 occasions before the £50 deposit is forfeited.  </p>
                <p>Your statutory rights are not affected.  </p>
                <p><b>By paying the £50 deposit, you confirm that you have read and agree to these terms and conditions.</b></p>
            </div>
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
            <div class="field-group">
                <label class="field-label" for="card-number-element">Card number</label>
                <div id="card-number-element" class="stripe-element"></div>
            </div>
            <div class="field-row">
                <div class="field-group">
                    <label class="field-label" for="card-expiry-element">MM / YY</label>
                    <div id="card-expiry-element" class="stripe-element"></div>
                </div>
                <div class="field-group">
                    <label class="field-label" for="card-cvc-element">CVC</label>
                    <div id="card-cvc-element" class="stripe-element"></div>
                </div>
            </div>
            <div class="field-group">
                <label class="field-label" for="postcode-input">Postcode</label>
                <input id="postcode-input" type="text" placeholder="e.g. SW1A 1AA">
            </div>
            <button id="payBtn" type="button"><span class="btn-text">Place Payment On Hold</span><span id="paySpinner" class="spinner" aria-hidden="true"></span></button>
            <div id="result"></div>
        </div>
    </section>

    <script>
        const stripe = Stripe("<?php echo STRIPE_PUBLIC_KEY; ?>", { locale: 'en-GB' });
        const elements = stripe.elements();
        const cardNumber = elements.create("cardNumber", {
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
        const cardExpiry = elements.create("cardExpiry", {
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
        const cardCvc = elements.create("cardCvc", {
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
        cardNumber.mount("#card-number-element");
        cardExpiry.mount("#card-expiry-element");
        cardCvc.mount("#card-cvc-element");

        const payButton = document.getElementById('payBtn');
        const paySpinner = document.getElementById('paySpinner');
        const resultContainer = document.getElementById('result');
        const postcodeInput = document.getElementById('postcode-input');

        const carouselSlides = Array.from(document.querySelectorAll('.carousel-slide'));
        let activeSlide = 0;

        setInterval(() => {
            carouselSlides[activeSlide].classList.remove('active');
            activeSlide = (activeSlide + 1) % carouselSlides.length;
            carouselSlides[activeSlide].classList.add('active');
        }, 4500);

        document.getElementById("payBtn").onclick = async () => {
            resultContainer.innerHTML = '';
            // show spinner and disable button while verifying
            payButton.disabled = true;
            if (paySpinner) paySpinner.style.display = 'inline-block';
            const btnText = payButton.querySelector('.btn-text');
            if (btnText) btnText.textContent = 'Verifying...';

            const { paymentMethod, error } = await stripe.createPaymentMethod({
                type: 'card',
                card: cardNumber,
                billing_details: {
                    address: {
                        postal_code: postcodeInput.value.trim()
                    }
                }
            });

            if (error) {
                if (paySpinner) paySpinner.style.display = 'none';
                if (btnText) btnText.textContent = 'Place Payment On Hold';
                payButton.disabled = false;
                resultContainer.innerHTML = '<div class="error-box">' +
                    '<strong>Payment failed:</strong> ' + error.message +
                    '</div>';
                return;
            }

            // proceed to server hold
            if (btnText) btnText.textContent = 'Processing...';

            const response = await fetch('create_hold.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    payment_method: paymentMethod.id,
                    totalDeposit: 50.00,
                    postcode: postcodeInput.value.trim(),
                    firstName: "<?php echo $_POST['firstName'] ?? ''; ?>",
                    lastName: "<?php echo $_POST['lastName'] ?? ''; ?>",
                    email: "<?php echo $_POST['email'] ?? ''; ?>",
                    phone: "<?php echo $_POST['phone'] ?? ''; ?>",
                    date: "<?php echo $_POST['date'] ?? ''; ?>",
                    time: "<?php echo $_POST['time'] ?? ''; ?>",
                    address: "<?php echo $_POST['address'] ?? ''; ?>",
                    notes: "<?php echo (str_replace(["\r\n", "\r", "\n"], "\\n", $_POST['notes'] ?? '')); ?>",
                    age: "<?php echo $_POST['age'] ?? ''; ?>",
                    gender: "<?php echo $_POST['gender'] ?? ''; ?>",
                    parentName: "<?php echo $_POST['parentName'] ?? ''; ?>"
                })
            });

            if (!response.ok) {
                if (paySpinner) paySpinner.style.display = 'none';
                if (btnText) btnText.textContent = 'Place Payment On Hold';
                payButton.disabled = false;
                resultContainer.innerHTML = '<div class="error-box">' +
                    '<strong>Server error:</strong> Please try again later.' +
                    '</div>';
                return;
            }

            const data = await response.json();
            resultContainer.innerHTML =
                '<div class="success-box" id="success-message">' +
                    '<div class="success-icon">✓</div>' +
                    '<div class="success-copy">' +
                        '<h3>Thank you for your payment</h3>' +
                        '<p>Your deposit is now secured. You will receive a confirmation email shortly with the booking details and next steps.</p>' +
                        '<button type="button" class="success-button" id="return-to-booking">Return to booking</button>' +
                    '</div>' +
                '</div>';

            if (paySpinner) paySpinner.style.display = 'none';
            const returnButton = document.getElementById('return-to-booking');
            if (returnButton) {
                returnButton.addEventListener('click', () => {
                    window.parent.postMessage('payment_success', data.payment_intent);
                    window.close();
                });
            }
            // hide the button after success
            payButton.style.display = 'none';
            document.getElementById('success-message').scrollIntoView({ behavior: 'smooth', block: 'center' });
        };
    </script>
</body>
</html>
