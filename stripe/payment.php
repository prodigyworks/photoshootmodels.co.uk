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
<html>
<head>
    <title>On Hold Payment</title>
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body>

<h2>Booking Deposit (On Hold Payment)</h2>

<div id="card-element"></div>
<button id="payBtn">Place Payment On Hold</button>

<div id="result"></div>

<script>
const stripe = Stripe("<?php echo STRIPE_PUBLIC_KEY; ?>"); // <-- your Stripe public key
const elements = stripe.elements();
const card = elements.create("card");
card.mount("#card-element");

document.getElementById("payBtn").onclick = async () => {
    const {paymentMethod, error} = await stripe.createPaymentMethod({
        type: "card",
        card: card
    });

    if (error) {
        document.getElementById("result").innerHTML = error.message;
        return;
    }

    // Send paymentMethod.id to your PHP backend
    const response = await fetch("create_hold.php", {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify({
            payment_method: paymentMethod.id,
            totalDeposit: 50.00,   // example deposit
            firstName: "<?php echo $_POST['firstName'] ?? ''; ?>",
            lastName: "<?php echo $_POST['lastName'] ?? ''; ?>",
            email: "<?php echo $_POST['email'] ?? ''; ?>",
            phone: "<?php echo $_POST['phone'] ?? ''; ?>",
            date: "<?php echo $_POST['date'] ?? ''; ?>",
            time: "<?php echo $_POST['time'] ?? ''; ?>"
        })
    });

    const data = await response.json();
    document.getElementById("result").innerHTML = JSON.stringify(data, null, 2);
};
</script>

</body>
</html>
