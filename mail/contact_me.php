<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use League\OAuth2\Client\Provider\GenericProvider;

require '../vendor/autoload.php'; // Load Composer's autoloader

// Load environment variables from .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Validate Form Inputs
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405); 
    echo "Invalid request method.";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $message = trim($_POST['message']);

    if (empty($name) || empty($email) || empty($phone) || empty($message)) {
        echo "All fields are required.";
        exit;
    }

    // OAuth2 Provider Configuration
    $provider = new GenericProvider([
        'clientId'                => $_ENV['OAUTH_CLIENT_ID'], 
        'clientSecret'            => $_ENV['OAUTH_CLIENT_SECRET'], 
        'redirectUri'             => 'https://login.microsoftonline.com/common/oauth2/nativeclient',
        'urlAuthorize'            => 'https://login.microsoftonline.com/' . $_ENV['OAUTH_TENANT_ID'] . '/oauth2/v2.0/authorize',
        'urlAccessToken'          => 'https://login.microsoftonline.com/' . $_ENV['OAUTH_TENANT_ID'] . '/oauth2/v2.0/token',
        'urlResourceOwnerDetails' => '',
    ]);

    try {
        // Get an access token
        $accessToken = $provider->getAccessToken('client_credentials');

        // Setup PHPMailer
        $mail = new PHPMailer(true);
        $mail->isSMTP();                                 // Send using SMTP
        $mail->Host = 'smtp.office365.com';              // Set the SMTP server to send through
        $mail->SMTPAuth = true;
        $mail->AuthType = 'XOAUTH2';                     // Use OAuth2 authentication
        $mail->setOAuth(new \PHPMailer\PHPMailer\OAuth([
            'provider' => $provider,
            'clientId' => $_ENV['OAUTH_CLIENT_ID'],
            'clientSecret' => $_ENV['OAUTH_CLIENT_SECRET'],
            'refreshToken' => $accessToken->getToken(),
            'userName' => $_ENV['SMTP_USER'], 
        ]));
       
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  // Enable TLS encryption
        $mail->Port = 587;                                   // TCP port to connect to

        // Recipients
        $mail->setFrom($_ENV['SMTP_USER'], 'Politheon Founders'); 
        $mail->addAddress('founders@politheon.com');
        $mail->addReplyTo($email, $name); 

        // Content
        $mail->isHTML(true);                           
        $mail->Subject = 'New Contact Form Submission';
        $mail->Body = "
            <h2>New Contact Message</h2>
            <strong>Name:</strong> {$name} <br>
            <strong>Email:</strong> {$email} <br>
            <strong>Phone:</strong> {$phone} <br>
            <strong>Message:</strong> {$message}
        ";
        $mail->AltBody = "Name: {$name}\nEmail: {$email}\nPhone: {$phone}\nMessage: {$message}"; 

        // Send the email
        $mail->send();
        echo trim("success");
        exit;
    } catch (Exception $e) {
        echo "PHP Mailer Error: " . $mail->ErrorInfo;
    }
} else {
    echo "Invalid request method.";
}
?>