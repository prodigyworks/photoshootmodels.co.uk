<?php
    // 1. Validate the incoming parameter
    if (!isset($_GET['user']) || empty($_GET['user'])) {
        http_response_code(400);
        echo "Missing user parameter.";
        exit;
    }

    $userParam = urlencode($_GET['user']);

    // 2. Build the remote URL internally
    $remoteUrl = "https://www.virtualstudiobooking.com/api/view-pdf?user={$userParam}";

    // 3. Fetch the remote PDF
    $ch = curl_init($remoteUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

    curl_close($ch);

    // 4. Handle errors
    if ($httpCode !== 200 || !$response) {
        http_response_code(500);
        echo "Failed to load PDF.";
        exit;
    }

    // 5. Output the PDF to the browser
    header("Content-Type: application/pdf");
    header("Content-Length: " . strlen($response));
    header("Content-Disposition: inline; filename=\"document.pdf\"");

    echo $response;
    exit;
