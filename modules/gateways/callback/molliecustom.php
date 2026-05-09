<?php
/**
 * Mollie Callback Handler
 * 
 * Developed by: Premium Technologies Private Limited
 * Website: https://www.premiumtech.uk
 * 
 * PHP 5.6 to 9.0+ Compatible Secure Webhook
 */

require_once __DIR__ . '/../../../init.php';
require_once __DIR__ . '/../../../includes/gatewayfunctions.php';
require_once __DIR__ . '/../../../includes/invoicefunctions.php';

$gatewayModuleName = "molliecustom";
$GATEWAY = getGatewayVariables($gatewayModuleName);

if (!$GATEWAY["type"]) {
    die("Module Not Activated");
}

// Mollie sends the payment ID via POST
$paymentId = isset($_POST["id"]) ? $_POST["id"] : null;

if (!$paymentId) {
    header("HTTP/1.1 400 Bad Request");
    die("No Payment ID Provided");
}

// Secure Server-to-Server Verification
$ch = curl_init("https://api.mollie.com/v2/payments/" . $paymentId);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Authorization: Bearer " . $GATEWAY['apiKey'],
    "User-Agent: PremiumTech_WHMCS_Mollie/1.0"
));
$response = curl_exec($ch);
$payment = json_decode($response, true);
curl_close($ch);

$status = isset($payment['status']) ? $payment['status'] : '';
$invoiceId = isset($payment['metadata']['invoice_id']) ? $payment['metadata']['invoice_id'] : null;
$amount = isset($payment['amount']['value']) ? $payment['amount']['value'] : 0;
$transId = isset($payment['id']) ? $payment['id'] : '';

// Process Payment Results
if ($status === 'paid') {
    $invoiceId = checkCbInvoiceID($invoiceId, $GATEWAY["name"]);
    checkCbTransID($transId); 
    
    addInvoicePayment(
        $invoiceId,
        $transId,
        $amount,
        0, // Transaction Fee
        $gatewayModuleName
    );
    
    logTransaction($GATEWAY["name"], $payment, "Successful Transaction");
    header("HTTP/1.1 200 OK");
    echo "OK";
} elseif (in_array($status, array('expired', 'failed', 'canceled'))) {
    logTransaction($GATEWAY["name"], $payment, "Unsuccessful Status: " . $status);
    echo "Payment " . $status;
} else {
    // Other statuses (pending, open) don't require action yet
    echo "Status: " . $status;
}