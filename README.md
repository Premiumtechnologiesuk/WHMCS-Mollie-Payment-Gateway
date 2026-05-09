# Mollie Payment Gateway for WHMCS
### Developed by [Premium Technologies Private Limited](https://www.premiumtech.uk)

![ISO Certified](https://img.shields.io/badge/Certification-ISO%20Certified-blue)

Professional Mollie Payment Gateway integration for WHMCS. Secure, ISO-standard quality, and ready for global scale.

## 🚀 Features
- **Secure Integration:** HMAC-SHA512 signature verification.
- **Auto-Payment Tracking:** Automated invoice marking via Webhooks.
- **UK Business Ready:** Optimized for international standards.

## 📂 Installation
1. Download the latest release.
2. Upload the `modules` folder to your WHMCS root directory.
3. In WHMCS Admin, navigate to **System Settings > Payment Gateways**.
4. Activate **Mollie (Premium Tech)**.
5. Enter your **Mollie API Key** and Save.

## ⚠️ Important Note 
Troubleshoot Error " "URL scheme not allowed"
If you encounter this error, it is a security rejection from Mollie's API. Please verify:
1. **Domain Mismatch:** You must use an API key that belongs to the specific website profile approved by Mollie. If you use an API Key from "Site A" on "Site B", Mollie will reject the request.
2. **HTTPS Requirement:** Your WHMCS System URL must be `https://`. 
3. **Approved Status:** Live transactions only work on domains approved in your Mollie Dashboard. Use **Test API Keys** for unapproved or staging domains.

## 🏢 About Us
---
**Premium Technologies Private Limited**  
Web | Software | IT | AI Solutions  
📧 info@premiumtech.uk | 🌐 [www.premiumtech.uk](https://www.premiumtech.uk)
