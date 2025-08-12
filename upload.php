<?php
// upload.php - simple handler to receive files and send email with attachments.
// WARNING: This script is intended to run on a PHP-enabled webserver. Make sure file uploads are enabled and mail() works.
// Destination email:
$to = 'sturox528@gmail.com';

// Gather POST fields
$f_name = isset($_POST['f_name']) ? $_POST['f_name'] : 'N/A';
$f_roll = isset($_POST['f_roll']) ? $_POST['f_roll'] : 'N/A';
$f_phone = isset($_POST['f_phone']) ? $_POST['f_phone'] : 'N/A';
$f_email = isset($_POST['f_email']) ? $_POST['f_email'] : 'N/A';
$bw_pages = isset($_POST['bw_pages']) ? intval($_POST['bw_pages']) : 0;
$color_pages = isset($_POST['color_pages']) ? intval($_POST['color_pages']) : 0;
$order_total = isset($_POST['order_total']) ? $_POST['order_total'] : ($bw_pages*1 + $color_pages*3);

$pay_method = isset($_POST['final_pay_method']) ? $_POST['final_pay_method'] : 'N/A';

$message = "New STUROX order received.\n\nName: $f_name\nRoll/ID: $f_roll\nPhone: $f_phone\nEmail: $f_email\nB&W pages: $bw_pages\nColour pages: $color_pages\nOrder total (Rs.): $order_total\nPayment method: $pay_method\n\nPlease find attachments (uploaded files and payment screenshot if provided).";

// Create upload directory
$upload_dir = __DIR__ . '/uploads';
if(!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

// Collect attachments
$attachments = array();

// handle uploaded files array 'uploaded_files[]'
if(isset($_FILES['uploaded_files'])){
    $files = $_FILES['uploaded_files'];
    for($i=0; $i<count($files['name']); $i++){
        if($files['error'][$i] === UPLOAD_ERR_OK){
            $tmp = $files['tmp_name'][$i];
            $name = basename($files['name'][$i]);
            $dest = $upload_dir . '/' . time() . '_' . $name;
            if(move_uploaded_file($tmp, $dest)){
                $attachments[] = $dest;
            }
        }
    }
}

// payment screenshot
if(isset($_FILES['payment_screenshot']) && $_FILES['payment_screenshot']['error'] === UPLOAD_ERR_OK){
    $tmp = $_FILES['payment_screenshot']['tmp_name'];
    $name = basename($_FILES['payment_screenshot']['name']);
    $dest = $upload_dir . '/' . time() . '_pay_' . $name;
    if(move_uploaded_file($tmp, $dest)){
        $attachments[] = $dest;
    }
}

// Now send email with attachments using multipart/mixed
$subject = "STUROX Order: $f_name - $f_roll";
$boundary = md5(time());

$headers = "From: noreply@sturox.local\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: multipart/mixed; boundary=\"{$boundary}\"\r\n\r\n";

$body = "--{$boundary}\r\n";
$body .= "Content-Type: text/plain; charset=\"UTF-8\"\r\n";
$body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
$body .= $message . "\r\n\r\n";

// Attach files
foreach($attachments as $file){
    if(is_file($file)){
        $filedata = chunk_split(base64_encode(file_get_contents($file)));
        $filename = basename($file);
        $body .= "--{$boundary}\r\n";
        $body .= "Content-Type: application/octet-stream; name=\"{$filename}\"\r\n";
        $body .= "Content-Transfer-Encoding: base64\r\n";
        $body .= "Content-Disposition: attachment; filename=\"{$filename}\"\r\n\r\n";
        $body .= $filedata . "\r\n\r\n";
    }
}

$body .= "--{$boundary}--";

// Attempt to send email
$mail_sent = mail($to, $subject, $body, $headers);

if($mail_sent){
    echo "<h2>Order submitted successfully.</h2>";
    echo "<p>Thank you, $f_name. You will receive a confirmation email soon (if your server can send mail).</p>";
    echo "<p><a href='index.html'>Return to Home</a></p>";
} else {
    echo "<h2>Order submitted but email could not be sent by server.</h2>";
    echo "<p>The files were uploaded. Please check the 'uploads' folder on the server or configure mail settings.</p>";
    echo "<p><a href='index.html'>Return to Home</a></p>";
}
?>