<?php
include '../config/db_connect.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['enquiry_csrf'])) {
    $_SESSION['enquiry_csrf'] = bin2hex(random_bytes(32));
}

$form = [
    'name' => '',
    'email' => '',
    'subject' => '',
    'message' => '',
];
$enquiry_error = '';
$enquiry_success = $_SESSION['enquiry_success'] ?? '';
unset($_SESSION['enquiry_success']);

if (isset($_SESSION['username'])) {
    $profile_stmt = $conn->prepare(
        'SELECT username, email FROM users WHERE username = ? LIMIT 1'
    );
    $profile_stmt->bind_param('s', $_SESSION['username']);
    $profile_stmt->execute();
    $profile = $profile_stmt->get_result()->fetch_assoc();
    $profile_stmt->close();

    if ($profile) {
        $form['name'] = $profile['username'];
        $form['email'] = $profile['email'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_enquiry'])) {
    $form['name'] = trim($_POST['name'] ?? '');
    $form['email'] = trim($_POST['email'] ?? '');
    $form['subject'] = trim($_POST['subject'] ?? '');
    $form['message'] = trim($_POST['message'] ?? '');
    $submitted_token = $_POST['csrf_token'] ?? '';

    if (!hash_equals($_SESSION['enquiry_csrf'], $submitted_token)) {
        $enquiry_error = 'Your session expired. Please refresh and try again.';
    } elseif (
        $form['name'] === ''
        || $form['email'] === ''
        || $form['subject'] === ''
        || $form['message'] === ''
    ) {
        $enquiry_error = 'Please complete every field.';
    } elseif (!filter_var($form['email'], FILTER_VALIDATE_EMAIL)) {
        $enquiry_error = 'Please enter a valid email address.';
    } elseif (
        strlen($form['name']) > 100
        || strlen($form['email']) > 150
        || strlen($form['subject']) > 150
        || strlen($form['message']) > 2000
    ) {
        $enquiry_error = 'One or more fields are too long.';
    } else {
        $username = $_SESSION['username'] ?? null;
        $enquiry_stmt = $conn->prepare(
            'INSERT INTO enquiries
                (username, customer_name, customer_email, subject, message)
             VALUES (?, ?, ?, ?, ?)'
        );
        $enquiry_stmt->bind_param(
            'sssss',
            $username,
            $form['name'],
            $form['email'],
            $form['subject'],
            $form['message']
        );
        $enquiry_stmt->execute();
        $enquiry_stmt->close();

        $_SESSION['enquiry_success'] = 'Thanks! Your enquiry has been received.';
        $_SESSION['enquiry_csrf'] = bin2hex(random_bytes(32));
        $conn->close();
        header('Location: contact.php');
        exit();
    }
}

include '../includes/header.php';
include '../includes/navigation.php';
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/contact.css">

<main class="contact-page">
    <section class="contact-intro">
        <span class="contact-kicker">We’re here to help</span>
        <h1>Contact Us</h1>
        <p>Questions about an order, restaurant, or Blind Box? Send us an enquiry and our team will get back to you.</p>
    </section>

    <section class="contact-layout">
        <aside class="contact-details">
            <div>
                <span class="contact-detail-icon" aria-hidden="true">💬</span>
                <h2>Let’s talk</h2>
                <p>Our support team is available Monday–Friday, 9 AM–6 PM.</p>
            </div>

            <div class="contact-detail-list">
                <p><strong>Email</strong><a href="mailto:hello@blindbite.my">hello@blindbite.my</a></p>
                <p><strong>Phone</strong><a href="tel:+60198680387">+60 19-868 0387</a></p>
                <p><strong>Location</strong><span>Kuala Lumpur, Malaysia</span></p>
            </div>
        </aside>

        <div class="enquiry-card">
            <div class="enquiry-heading">
                <span>Enquiry form</span>
                <h2>How can we help?</h2>
            </div>

            <?php if ($enquiry_success !== '') : ?>
                <p class="enquiry-message enquiry-success"><?php echo htmlspecialchars($enquiry_success); ?></p>
            <?php endif; ?>

            <?php if ($enquiry_error !== '') : ?>
                <p class="enquiry-message enquiry-error"><?php echo htmlspecialchars($enquiry_error); ?></p>
            <?php endif; ?>

            <form method="POST" action="contact.php" class="enquiry-form">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['enquiry_csrf']); ?>">

                <div class="enquiry-row">
                    <label>
                        <span>Name</span>
                        <input type="text" name="name" maxlength="100" value="<?php echo htmlspecialchars($form['name']); ?>" placeholder="Your name" required>
                    </label>
                    <label>
                        <span>Email</span>
                        <input type="email" name="email" maxlength="150" value="<?php echo htmlspecialchars($form['email']); ?>" placeholder="you@example.com" required>
                    </label>
                </div>

                <label>
                    <span>Subject</span>
                    <select name="subject" required>
                        <option value="">Choose an enquiry type</option>
                        <?php foreach (['Order support', 'Restaurant enquiry', 'Payment question', 'Feedback', 'Other'] as $subject_option) : ?>
                            <option value="<?php echo htmlspecialchars($subject_option); ?>" <?php echo $form['subject'] === $subject_option ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($subject_option); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label>
                    <span>Message</span>
                    <textarea name="message" maxlength="2000" rows="6" placeholder="Tell us what happened or how we can help..." required><?php echo htmlspecialchars($form['message']); ?></textarea>
                    <small>Maximum 2,000 characters</small>
                </label>

                <button type="submit" name="submit_enquiry">Send Enquiry <span aria-hidden="true">→</span></button>
            </form>
        </div>
    </section>
</main>

<?php
$conn->close();
include '../includes/footer.php';
?>
