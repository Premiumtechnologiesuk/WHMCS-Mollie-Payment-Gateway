<?php
/**
 * Mollie Payment Gateway for WHMCS
 * 
 * Developed by: Premium Technologies Private Limited
 * Website: https://www.premiumtech.uk
 * Email: info@premiumtech.uk
 * 
 * Professional Mollie integration. Secure, ISO-standard quality, and ready for global scale.
 * 
 * @package    WHMCS
 * @author     Premium Technologies Private Limited
 * @copyright  Copyright (c) 2026 Premium Technologies Private Limited
 * @license    MIT License
 */

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

function molliecustom_config() {
    return array(
        "FriendlyName" => array("Type" => "System", "Value" => "Mollie (Premium Tech)"),
        "apiKey" => array(
            "FriendlyName" => "Mollie API Key", 
            "Type" => "text", 
            "Size" => "40", 
            "Description" => "Enter your Live or Test API Key from your Mollie Dashboard."
        ),
    );
}

function molliecustom_link($params) {
    // API Details
    $apiKey = $params['apiKey'];
    $invoiceId = $params['invoiceid'];
    $amount = number_format($params['amount'], 2, '.', '');
    $currencyCode = $params['currency'];
    
    /**
     * Premium Tech Smart URL Handling
     * Mollie requires HTTPS for all endpoints. 
     */
    $systemUrl = rtrim($params['systemurl'], '/');
    if (strpos($systemUrl, 'https://') === false) {
        $systemUrl = str_replace('http://', 'https://', $systemUrl);
    }
    
    $callbackUrl = $systemUrl . '/modules/gateways/callback/molliecustom.php';
    $returnUrl = $params['returnurl'];

    // API Payload Construction
    $payload = array(
        "amount" => array(
            "currency" => (string)$currencyCode, 
            "value" => (string)$amount
        ),
        "description" => $params['companyname'] . " - Invoice #" . $invoiceId,
        "redirectUrl" => $returnUrl,
        "webhookUrl"  => $callbackUrl,
        "metadata"    => array(
            "invoice_id" => $invoiceId,
            "integrator" => "Premium Technologies Private Limited"
        )
    );

    // Secure API Request
    $ch = curl_init("https://api.mollie.com/v2/payments");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Authorization: Bearer " . $apiKey,
        "Content-Type: application/json",
        "User-Agent: PremiumTech_WHMCS_Mollie/1.0"
    ));
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    
    $result = curl_exec($ch);
    $response = json_decode($result, true);
    curl_close($ch);

    $checkoutUrl = isset($response['_links']['checkout']['href']) ? $response['_links']['checkout']['href'] : null;

    if ($checkoutUrl) {
        return '<form action="' . $checkoutUrl . '" method="get">
                <input type="submit" value="' . $params['langpaynow'] . '" class="btn btn-primary" style="font-weight:bold; padding: 10px 20px;" />
                </form>';
    } else {
        $errorDetail = isset($response['detail']) ? $response['detail'] : 'Connection failed or Invalid API Key';
        return '<div class="alert alert-danger">Payment Error: ' . htmlspecialchars($errorDetail) . '</div>';
    }
}