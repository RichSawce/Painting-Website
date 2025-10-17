<?php
declare(strict_types=1);

// Return JSON for all responses
header('Content-Type: application/json; charset=UTF-8');

// --- Production error handling ---
ini_set('display_errors', '0');                // off in prod
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/mail_errors.log');

$response = ['status' => 'error', 'message' => 'Unexpected error.'];

// Basic rate limit (very simple)
session_start();
$now = time();
if (!isset($_SESSION['last_submit'])) $_SESSION['last_submit'] = 0;
if ($now - (int)$_SESSION['last_submit'] < 8) { // 1 submission every 8s
    echo json_encode(['status' => 'error', 'message' => 'Please wait a moment before sending again.']);
    exit;
}

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

// Honeypot (hidden field in form; should be empty)
$hp = $_POST['company'] ?? '';
if (!empty($hp)) {
    echo json_encode(['status' => 'success', 'message' => 'Thank you!']); // pretend success
    exit;
}

// Helper functions
function clean_text(string $s, int $max = 2000): string {
    $s = trim($s);
    // remove control chars except newline
    $s = preg_replace('/[^\P{C}\n]+/u', '', $s) ?? '';
    $s = strip_tags($s);
    $s = htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    if (mb_strlen($s) > $max) $s = mb_substr($s, 0, $max);
    return $s;
}
function no_headers(string $s): bool {
    return (bool)preg_match("/[\r\n]/", $s);
}

// Collect & validate
$firstName = clean_text($_POST['firstName'] ?? '', 80);
$lastName  = clean_text($_POST['lastName'] ?? '', 80);
$emailRaw  = trim($_POST['email'] ?? '');
$inquiry   = clean_text($_POST['inquiry'] ?? '', 4000);

if ($firstName === '' || $lastName === '' || $emailRaw === '' || $inquiry === '') {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
    exit;
}

if (no_headers($firstName) || no_headers($lastName) || no_headers($emailRaw)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input.']);
    exit;
}

$email = filter_var($emailRaw, FILTER_SANITIZE_EMAIL);
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid email format.']);
    exit;
}

// Compose email
$to       = 'paintings.by.francine@gmail.com';      // recipient (OK to be public)
$from     = 'no-reply@paintingsbyfrancine.ca';      // use your domain address
$subject  = 'New Inquiry from ' . $firstName . ' ' . $lastName;

// Guard subject
$subject  = str_replace(["\r", "\n"], ' ', $subject);

// Build body (plain text)
$bodyLines = [
    "First Name: $firstName",
    "Last Name: $lastName",
    "Email: $email",
    "",
    "Inquiry:",
    $inquiry,
    "",
    "— Sent from paintingsbyfrancine.ca contact form —"
];
$body = implode("\n", $bodyLines);

// Headers (use CRLF)
$headers  = "From: {$from}\r\n";
$headers .= "Reply-To: {$email}\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

// Send
$ok = @mail($to, $subject, $body, $headers);

if ($ok) {
    $_SESSION['last_submit'] = $now;
    echo json_encode(['status' => 'success', 'message' => 'Your message has been sent.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Message could not be sent. Please try again later.']);
}