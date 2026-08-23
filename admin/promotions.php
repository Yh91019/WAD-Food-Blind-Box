<?php

include 'admin_auth.php';
require_admin_login();

include '../config/db_connect.php';

if (empty($_SESSION['admin_promotion_csrf'])) {
    $_SESSION['admin_promotion_csrf'] = bin2hex(random_bytes(32));
}

function promotion_datetime_value(?string $value): string
{
    return $value ? date('Y-m-d\TH:i', strtotime($value)) : '';
}

function redirect_to_promotions(): void
{
    header('Location: promotions.php');
    exit();
}

$error = '';
$editingPromotion = null;
$form = [
    'promotion_id' => 0,
    'code' => '',
    'title' => '',
    'description' => '',
    'discount_type' => 'Percentage',
    'discount_value' => '',
    'minimum_spend' => '0.00',
    'starts_at' => date('Y-m-d\TH:i'),
    'ends_at' => date('Y-m-d\TH:i', strtotime('+30 days')),
    'is_active' => 1,
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    $action = $_POST['action'] ?? '';

    if (!hash_equals($_SESSION['admin_promotion_csrf'], $token)) {
        $error = 'Your session expired. Please reload the page and try again.';
    } elseif ($action === 'delete') {
        $promotionId = (int) ($_POST['promotion_id'] ?? 0);
        $stmt = $conn->prepare('DELETE FROM promotions WHERE promotion_id = ?');
        $stmt->bind_param('i', $promotionId);
        $stmt->execute();
        $deleted = $stmt->affected_rows === 1;
        $stmt->close();

        $_SESSION[$deleted ? 'admin_message' : 'admin_error'] = $deleted
            ? 'Promotion deleted successfully.'
            : 'Promotion could not be found.';
        redirect_to_promotions();
    } elseif ($action === 'toggle') {
        $promotionId = (int) ($_POST['promotion_id'] ?? 0);
        $stmt = $conn->prepare(
            'UPDATE promotions SET is_active = IF(is_active = 1, 0, 1) WHERE promotion_id = ?'
        );
        $stmt->bind_param('i', $promotionId);
        $stmt->execute();
        $changed = $stmt->affected_rows === 1;
        $stmt->close();

        $_SESSION[$changed ? 'admin_message' : 'admin_error'] = $changed
            ? 'Promotion availability updated.'
            : 'Promotion could not be found.';
        redirect_to_promotions();
    } elseif ($action === 'create' || $action === 'update') {
        $promotionId = (int) ($_POST['promotion_id'] ?? 0);
        $code = strtoupper(trim($_POST['code'] ?? ''));
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $discountType = $_POST['discount_type'] ?? '';
        $discountValue = trim($_POST['discount_value'] ?? '');
        $minimumSpend = trim($_POST['minimum_spend'] ?? '');
        $startsAtInput = trim($_POST['starts_at'] ?? '');
        $endsAtInput = trim($_POST['ends_at'] ?? '');
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        $form = [
            'promotion_id' => $promotionId,
            'code' => $code,
            'title' => $title,
            'description' => $description,
            'discount_type' => $discountType,
            'discount_value' => $discountValue,
            'minimum_spend' => $minimumSpend,
            'starts_at' => $startsAtInput,
            'ends_at' => $endsAtInput,
            'is_active' => $isActive,
        ];

        $startsTimestamp = strtotime($startsAtInput);
        $endsTimestamp = strtotime($endsAtInput);

        if (!preg_match('/^[A-Z0-9_-]{3,40}$/', $code)) {
            $error = 'Voucher code must contain 3–40 letters, numbers, dashes, or underscores.';
        } elseif (strlen($title) < 3 || strlen($title) > 120) {
            $error = 'Title must contain between 3 and 120 characters.';
        } elseif (strlen($description) < 5 || strlen($description) > 255) {
            $error = 'Description must contain between 5 and 255 characters.';
        } elseif (!in_array($discountType, ['Percentage', 'Fixed'], true)) {
            $error = 'Choose a valid discount type.';
        } elseif (!is_numeric($discountValue) || (float) $discountValue <= 0) {
            $error = 'Discount value must be greater than zero.';
        } elseif ($discountType === 'Percentage' && (float) $discountValue > 100) {
            $error = 'Percentage discounts cannot exceed 100%.';
        } elseif (!is_numeric($minimumSpend) || (float) $minimumSpend < 0) {
            $error = 'Minimum spend cannot be negative.';
        } elseif ($startsTimestamp === false || $endsTimestamp === false) {
            $error = 'Enter valid promotion start and end dates.';
        } elseif ($endsTimestamp <= $startsTimestamp) {
            $error = 'The end date must be later than the start date.';
        } elseif ($action === 'update' && $promotionId <= 0) {
            $error = 'The promotion to edit could not be found.';
        } else {
            $duplicateSql = 'SELECT promotion_id FROM promotions WHERE code = ?';
            if ($action === 'update') {
                $duplicateSql .= ' AND promotion_id <> ?';
            }
            $duplicateSql .= ' LIMIT 1';
            $duplicate = $conn->prepare($duplicateSql);
            if ($action === 'update') {
                $duplicate->bind_param('si', $code, $promotionId);
            } else {
                $duplicate->bind_param('s', $code);
            }
            $duplicate->execute();
            $codeExists = $duplicate->get_result()->num_rows > 0;
            $duplicate->close();

            if ($codeExists) {
                $error = 'That voucher code already exists.';
            } else {
                $discountValueNumber = (float) $discountValue;
                $minimumSpendNumber = (float) $minimumSpend;
                $startsAt = date('Y-m-d H:i:s', $startsTimestamp);
                $endsAt = date('Y-m-d H:i:s', $endsTimestamp);

                if ($action === 'create') {
                    $stmt = $conn->prepare(
                        'INSERT INTO promotions
                         (code, title, description, discount_type, discount_value,
                          minimum_spend, starts_at, ends_at, is_active)
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
                    );
                    $stmt->bind_param(
                        'ssssddssi',
                        $code,
                        $title,
                        $description,
                        $discountType,
                        $discountValueNumber,
                        $minimumSpendNumber,
                        $startsAt,
                        $endsAt,
                        $isActive
                    );
                } else {
                    $stmt = $conn->prepare(
                        'UPDATE promotions SET code = ?, title = ?, description = ?,
                         discount_type = ?, discount_value = ?, minimum_spend = ?,
                         starts_at = ?, ends_at = ?, is_active = ?
                         WHERE promotion_id = ?'
                    );
                    $stmt->bind_param(
                        'ssssddssii',
                        $code,
                        $title,
                        $description,
                        $discountType,
                        $discountValueNumber,
                        $minimumSpendNumber,
                        $startsAt,
                        $endsAt,
                        $isActive,
                        $promotionId
                    );
                }

                $stmt->execute();
                $stmt->close();
                $_SESSION['admin_message'] = $action === 'create'
                    ? "Promotion \"$title\" created successfully."
                    : "Promotion \"$title\" updated successfully.";
                redirect_to_promotions();
            }
        }
    } else {
        $error = 'Invalid promotion action.';
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && isset($_GET['edit'])) {
    $promotionId = (int) $_GET['edit'];
    $stmt = $conn->prepare('SELECT * FROM promotions WHERE promotion_id = ?');
    $stmt->bind_param('i', $promotionId);
    $stmt->execute();
    $editingPromotion = $stmt->get_result()->fetch_assoc() ?: null;
    $stmt->close();

    if ($editingPromotion) {
        $form = $editingPromotion;
        $form['starts_at'] = promotion_datetime_value($editingPromotion['starts_at']);
        $form['ends_at'] = promotion_datetime_value($editingPromotion['ends_at']);
    } else {
        $_SESSION['admin_error'] = 'Promotion could not be found.';
        redirect_to_promotions();
    }
}

$promotionResult = $conn->query(
    'SELECT promotions.*, COUNT(user_vouchers.user_voucher_id) AS claim_count
     FROM promotions
     LEFT JOIN user_vouchers ON user_vouchers.promotion_id = promotions.promotion_id
     GROUP BY promotions.promotion_id
     ORDER BY promotions.created_at DESC, promotions.promotion_id DESC'
);

$activePromotionRows = [];
$pastPromotionRows = [];
$livePromotionCount = 0;
$now = time();

while ($promotionResult && $promotion = $promotionResult->fetch_assoc()) {
    $startsTimestamp = strtotime($promotion['starts_at']);
    $endsTimestamp = strtotime($promotion['ends_at']);
    $isEnabled = (int) $promotion['is_active'] === 1;
    $isLive = $isEnabled && $now >= $startsTimestamp && $now <= $endsTimestamp;
    $isCurrent = $isEnabled && $now <= $endsTimestamp;

    if ($isLive) {
        $promotion['display_status'] = 'Live';
        $livePromotionCount++;
    } elseif ($isCurrent && $now < $startsTimestamp) {
        $promotion['display_status'] = 'Scheduled';
    } elseif (!$isEnabled) {
        $promotion['display_status'] = 'Inactive';
    } else {
        $promotion['display_status'] = 'Ended';
    }

    $promotion['is_live'] = $isLive;

    if ($isCurrent) {
        $activePromotionRows[] = $promotion;
    } else {
        $pastPromotionRows[] = $promotion;
    }
}

$totalPromotions = count($activePromotionRows) + count($pastPromotionRows);
$activePromotions = $livePromotionCount;
$promotionGroups = [
    'active' => $activePromotionRows,
    'past' => $pastPromotionRows,
];

include '../includes/header.php';
include '../includes/adminNavigation.php';
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/admin.css?v=<?php echo filemtime(__DIR__ . '/../css/admin.css'); ?>">

<section class="admin-page promotion-admin-page">
    <div class="admin-card">
        <div class="admin-header">
            <h1>Manage Promotions</h1>
        </div>

        <div class="admin-body">
            <div class="restaurant-page-actions promotion-page-actions">
                <a href="dashboard.php" class="admin-action-btn add-btn">← Back to Dashboard</a>
            </div>

            <div class="admin-stats promotion-summary">
                <div class="stat-card">
                    <span class="stat-label">Total Promotions</span>
                    <span class="stat-number"><?php echo $totalPromotions; ?></span>
                </div>
                <div class="stat-card">
                    <span class="stat-label">Live on Homepage</span>
                    <span class="stat-number"><?php echo $activePromotions; ?></span>
                </div>
            </div>

            <?php if (isset($_SESSION['admin_message'])) : ?>
                <p class="admin-success">
                    <?php echo htmlspecialchars($_SESSION['admin_message']); unset($_SESSION['admin_message']); ?>
                </p>
            <?php endif; ?>

            <?php if (isset($_SESSION['admin_error'])) : ?>
                <p class="admin-error">
                    <?php echo htmlspecialchars($_SESSION['admin_error']); unset($_SESSION['admin_error']); ?>
                </p>
            <?php endif; ?>

            <?php if ($error !== '') : ?>
                <p class="admin-error"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>

            <div class="promotion-admin-layout">
                <section class="promotion-editor" aria-labelledby="promotionFormTitle">
                    <div class="promotion-section-heading">
                        <span><?php echo $form['promotion_id'] ? 'Update voucher' : 'New voucher'; ?></span>
                        <h2 id="promotionFormTitle">
                            <?php echo $form['promotion_id'] ? 'Edit Promotion' : 'Create Promotion'; ?>
                        </h2>
                    </div>

                    <form method="POST" action="promotions.php" class="promotion-form">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['admin_promotion_csrf']); ?>">
                        <input type="hidden" name="action" value="<?php echo $form['promotion_id'] ? 'update' : 'create'; ?>">
                        <input type="hidden" name="promotion_id" value="<?php echo (int) $form['promotion_id']; ?>">

                        <div class="form-group">
                            <label for="code">Voucher Code</label>
                            <input id="code" name="code" type="text" maxlength="40" pattern="[A-Za-z0-9_-]{3,40}" value="<?php echo htmlspecialchars($form['code']); ?>" placeholder="E.g. WEEKEND10" required>
                        </div>

                        <div class="form-group">
                            <label for="title">Promotion Title</label>
                            <input id="title" name="title" type="text" minlength="3" maxlength="120" value="<?php echo htmlspecialchars($form['title']); ?>" placeholder="E.g. Weekend Surprise" required>
                        </div>

                        <div class="form-group form-full">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" minlength="5" maxlength="255" required><?php echo htmlspecialchars($form['description']); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="discount_type">Discount Type</label>
                            <select id="discount_type" name="discount_type" required>
                                <option value="Percentage" <?php echo $form['discount_type'] === 'Percentage' ? 'selected' : ''; ?>>Percentage (%)</option>
                                <option value="Fixed" <?php echo $form['discount_type'] === 'Fixed' ? 'selected' : ''; ?>>Fixed amount (RM)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="discount_value">Discount Value</label>
                            <input id="discount_value" name="discount_value" type="number" min="0.01" max="99999999.99" step="0.01" value="<?php echo htmlspecialchars((string) $form['discount_value']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="minimum_spend">Minimum Spend (RM)</label>
                            <input id="minimum_spend" name="minimum_spend" type="number" min="0" max="99999999.99" step="0.01" value="<?php echo htmlspecialchars((string) $form['minimum_spend']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="starts_at">Starts At</label>
                            <input id="starts_at" name="starts_at" type="datetime-local" value="<?php echo htmlspecialchars($form['starts_at']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="ends_at">Ends At</label>
                            <input id="ends_at" name="ends_at" type="datetime-local" value="<?php echo htmlspecialchars($form['ends_at']); ?>" required>
                        </div>

                        <label class="promotion-active-check">
                            <input type="checkbox" name="is_active" value="1" <?php echo $form['is_active'] ? 'checked' : ''; ?>>
                            Display this promotion when its dates are active
                        </label>

                        <div class="restaurant-form-actions form-full">
                            <?php if ($form['promotion_id']) : ?>
                                <a href="promotions.php" class="admin-action-btn back-btn">Cancel</a>
                            <?php endif; ?>
                            <button type="submit" class="admin-action-btn add-btn">
                                <?php echo $form['promotion_id'] ? 'Save Changes' : 'Create Promotion'; ?>
                            </button>
                        </div>
                    </form>
                </section>

                <section class="promotion-list-section" aria-labelledby="promotionListTitle">
                    <div class="promotion-section-heading">
                        <span>Homepage vouchers</span>
                        <h2 id="promotionListTitle">Existing Promotions</h2>
                    </div>

                    <div class="admin-promotion-tabs" role="tablist" aria-label="Promotion categories">
                        <button type="button" class="admin-promotion-tab is-active" id="activePromotionTab" role="tab" aria-selected="true" aria-controls="activePromotionPanel">
                            Active Promotions
                            <span><?php echo count($activePromotionRows); ?></span>
                        </button>
                        <button type="button" class="admin-promotion-tab" id="pastPromotionTab" role="tab" aria-selected="false" aria-controls="pastPromotionPanel">
                            Past Promotions
                            <span><?php echo count($pastPromotionRows); ?></span>
                        </button>
                    </div>

                    <div class="admin-promotion-panels">
                        <?php foreach ($promotionGroups as $groupName => $promotions) : ?>
                            <?php $isActiveGroup = $groupName === 'active'; ?>
                            <div
                                class="admin-promotion-panel"
                                id="<?php echo $isActiveGroup ? 'activePromotionPanel' : 'pastPromotionPanel'; ?>"
                                role="tabpanel"
                                aria-labelledby="<?php echo $isActiveGroup ? 'activePromotionTab' : 'pastPromotionTab'; ?>"
                                <?php echo $isActiveGroup ? '' : 'hidden'; ?>>
                                <div class="admin-promotion-list">
                                    <?php if (empty($promotions)) : ?>
                                        <div class="promotion-list-empty">
                                            <span aria-hidden="true"><?php echo $isActiveGroup ? '🎁' : '🕘'; ?></span>
                                            <p><?php echo $isActiveGroup ? 'No active promotions.' : 'No past promotions.'; ?></p>
                                        </div>
                                    <?php else : ?>
                                        <?php foreach ($promotions as $promotion) : ?>
                                            <article class="admin-promotion-item <?php echo $promotion['is_live'] ? 'is-live' : ''; ?>">
                                                <div class="admin-promotion-topline">
                                                    <strong><?php echo htmlspecialchars($promotion['code']); ?></strong>
                                                    <span><?php echo htmlspecialchars($promotion['display_status']); ?></span>
                                                </div>
                                                <h3><?php echo htmlspecialchars($promotion['title']); ?></h3>
                                                <p><?php echo htmlspecialchars($promotion['description']); ?></p>
                                                <dl class="promotion-details">
                                                    <div><dt>Discount</dt><dd><?php echo $promotion['discount_type'] === 'Percentage' ? number_format((float) $promotion['discount_value'], 0) . '%' : 'RM ' . number_format((float) $promotion['discount_value'], 2); ?></dd></div>
                                                    <div><dt>Minimum</dt><dd>RM <?php echo number_format((float) $promotion['minimum_spend'], 2); ?></dd></div>
                                                    <div><dt>Claims</dt><dd><?php echo (int) $promotion['claim_count']; ?></dd></div>
                                                </dl>
                                                <small><?php echo date('d M Y, g:i A', strtotime($promotion['starts_at'])); ?> – <?php echo date('d M Y, g:i A', strtotime($promotion['ends_at'])); ?></small>

                                                <div class="promotion-item-actions">
                                                    <a href="promotions.php?edit=<?php echo (int) $promotion['promotion_id']; ?>" class="edit-btn">Edit</a>
                                                    <form method="POST" action="promotions.php" class="delete-form">
                                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['admin_promotion_csrf']); ?>">
                                                        <input type="hidden" name="action" value="toggle">
                                                        <input type="hidden" name="promotion_id" value="<?php echo (int) $promotion['promotion_id']; ?>">
                                                        <button type="submit" class="promotion-toggle-btn"><?php echo (int) $promotion['is_active'] === 1 ? 'Deactivate' : 'Activate'; ?></button>
                                                    </form>
                                                    <form method="POST" action="promotions.php" class="delete-form promotion-delete-form">
                                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['admin_promotion_csrf']); ?>">
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="promotion_id" value="<?php echo (int) $promotion['promotion_id']; ?>">
                                                        <button type="submit" class="delete-btn">Delete</button>
                                                    </form>
                                                </div>
                                            </article>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            </div>
        </div>
    </div>
</section>

<script src="<?php echo BASE_URL; ?>/js/admin-promotions.js?v=<?php echo filemtime(__DIR__ . '/../js/admin-promotions.js'); ?>"></script>

<?php
$conn->close();
include '../includes/footer.php';
?>
