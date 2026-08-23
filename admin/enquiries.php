<?php

include 'admin_auth.php';
require_admin_login();

include '../config/db_connect.php';

$enquiries = $conn->query(
    "SELECT
        enquiry_id,
        customer_name,
        customer_email,
        subject,
        message,
        status,
        created_at
     FROM enquiries
     ORDER BY created_at DESC, enquiry_id DESC"
);

include '../includes/header.php';
include '../includes/adminNavigation.php';
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/admin.css">

<section class="admin-page">
    <div class="admin-card">
        <div class="admin-header">
            <h1>User Enquiries</h1>
        </div>

        <div class="admin-body">
            <div class="restaurant-page-actions">
                <a href="dashboard.php" class="admin-action-btn add-btn">Back to Dashboard</a>
            </div>

            <?php if ($enquiries === false) : ?>
                <p class="admin-error">Enquiries could not be loaded. Please confirm that the enquiries table has been created.</p>

            <?php elseif ($enquiries->num_rows === 0) : ?>
                <p class="no-restaurants">No enquiries have been submitted yet.</p>

            <?php else : ?>
                <p class="enquiry-admin-summary">
                    <?php echo $enquiries->num_rows; ?>
                    <?php echo $enquiries->num_rows === 1 ? 'enquiry' : 'enquiries'; ?> received
                </p>

                <div class="admin-enquiry-grid">
                    <?php while ($enquiry = $enquiries->fetch_assoc()) : ?>
                        <?php
                        $reply_subject = 'Re: ' . $enquiry['subject'];
                        $reply_body = "Hi " . $enquiry['customer_name'] . ",\n\n";
                        $gmail_reply_url = 'https://mail.google.com/mail/?view=cm&fs=1'
                            . '&to=' . rawurlencode($enquiry['customer_email'])
                            . '&su=' . rawurlencode($reply_subject)
                            . '&body=' . rawurlencode($reply_body);
                        $status_class = strtolower(str_replace(' ', '-', $enquiry['status']));
                        ?>

                        <article class="admin-enquiry-card">
                            <div class="admin-enquiry-card-header">
                                <h2><?php echo htmlspecialchars($enquiry['subject']); ?></h2>
                                <span class="enquiry-status status-<?php echo htmlspecialchars($status_class); ?>">
                                    <?php echo htmlspecialchars($enquiry['status']); ?>
                                </span>
                            </div>

                            <p class="enquiry-sender">
                                <strong><?php echo htmlspecialchars($enquiry['customer_name']); ?></strong><br>
                                <a href="mailto:<?php echo htmlspecialchars($enquiry['customer_email']); ?>">
                                    <?php echo htmlspecialchars($enquiry['customer_email']); ?>
                                </a>
                            </p>

                            <p class="enquiry-date">
                                Submitted <?php echo date('d M Y, g:i A', strtotime($enquiry['created_at'])); ?>
                            </p>

                            <div class="enquiry-message-body">
                                <?php echo nl2br(htmlspecialchars($enquiry['message'])); ?>
                            </div>

                            <a
                                class="gmail-reply-btn"
                                href="<?php echo htmlspecialchars($gmail_reply_url, ENT_QUOTES); ?>"
                                target="_blank"
                                rel="noopener noreferrer"
                            >Reply with Gmail</a>
                        </article>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php
$conn->close();
include '../includes/footer.php';
?>
