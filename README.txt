
STUROX Website Package - Instructions
-------------------------------------

Contents:
- index.html
- upload.html
- delivery.html
- order.html
- payment.html
- confirm.html
- assets/styles.css
- assets/app.js
- upload.php
- uploads/  (created on server runtime)
- README.txt

What this package does:
- Provides a static website that walks customers through uploading files, entering delivery details,
  placing an order, and making payment.
- The final form posts to upload.php which saves uploaded files into the 'uploads' directory
  and attempts to send an email to sturox528@gmail.com with the uploaded files attached.

Important notes & hosting requirements:
1) The upload.php script requires a PHP-enabled webserver with file uploads enabled.
   - Put the whole package into your webroot or a subfolder on a server that supports PHP (>=5.6 recommended).
   - Ensure 'uploads' directory is writable by the web server user (script will create it if not present).

2) Email sending:
   - upload.php uses PHP's mail() to send the email with attachments. Many hosts require proper SMTP setup.
   - If mail() is disabled or not configured, consider using an SMTP library (PHPMailer) and configure SMTP credentials.
   - If you need, I can adapt upload.php to use PHPMailer with SMTP (you'll need to provide SMTP host, username, password).

3) Security considerations:
   - This code is a starting point. For production, validate & sanitize inputs, limit allowed file types and sizes,
     and implement authentication or spam prevention (captcha).
   - Do not run on an open public server without further hardening.

4) UPI / Payment:
   - The site shows the UPI ID 8972548589@ibl and a link which uses 'upi://pay' scheme to open compatible payment apps.
   - The page supports uploading a QR image to show to users, and users must upload a payment screenshot if they choose UPI.
   - Cash on delivery option is supported.

If you'd like:
- I can modify the PHP to send via SMTP using PHPMailer (you'll need to provide SMTP credentials), and/or
- Add server-side price calculations and stronger validation, or change the destination email address.

To download the ZIP, use the link provided below.
