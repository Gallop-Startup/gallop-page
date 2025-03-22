<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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

    // Setup PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();                                 // Send using SMTP
        $mail->Host = 'smtp.gmail.com';                  // Set the SMTP server to send through
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['SMTP_USER']; // SMTP username from .env
        $mail->Password = $_ENV['SMTP_PASS']; // SMTP password from .env
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption
        $mail->Port = 587;                             // TCP port to connect to

        // Recipients
        $mail->setFrom($_ENV['SMTP_USER'], 'Gallop'); // Sender's email and name
        $mail->addAddress('htuple3@gmail.com');
        $mail->addReplyTo($email, $name); // Add a reply-to address

        // Content
        $mail->isHTML(true);                           // Set email format to HTML
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