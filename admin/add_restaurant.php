<?php

include 'admin_auth.php';
require_admin_login();

include '../config/db_connect.php';
require_once '../includes/restaurant_image.php';

$error = "";

$form = [
    'restaurant_name' => '',
    'restaurant_address' => '',
    'restaurant_opening_hours' => '',
    'restaurant_closing_hours' => '',
    'restaurant_phone_number' => '',
    'blind_box_price' => '',
    'blind_box_description' => '',
    'blind_box_food_category' => '',
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $restaurant_name      = trim($_POST['restaurant_name'] ?? '');
    $restaurant_address   = trim($_POST['restaurant_address'] ?? '');
    $opening_hours        = $_POST['restaurant_opening_hours'] ?? '';
    $closing_hours        = $_POST['restaurant_closing_hours'] ?? '';
    $phone_number         = trim($_POST['restaurant_phone_number'] ?? '');
    $blind_box_price      = $_POST['blind_box_price'] ?? '';
    $blind_box_description = trim($_POST['blind_box_description'] ?? '');
    $blind_box_category   = trim($_POST['blind_box_food_category'] ?? '');

    // Keep entered values so the form can be re-filled if something goes wrong
    $form = [
        'restaurant_name' => $restaurant_name,
        'restaurant_address' => $restaurant_address,
        'restaurant_opening_hours' => $opening_hours,
        'restaurant_closing_hours' => $closing_hours,
        'restaurant_phone_number' => $phone_number,
        'blind_box_price' => $blind_box_price,
        'blind_box_description' => $blind_box_description,
        'blind_box_food_category' => $blind_box_category,
    ];

    if (
        $restaurant_name === '' || $restaurant_address === '' ||
        $opening_hours === '' || $closing_hours === '' ||
        $phone_number === '' || $blind_box_price === '' ||
        $blind_box_description === '' || $blind_box_category === ''
    ) {

        $error = "Please fill in all fields.";

    } elseif (strlen($restaurant_name) < 2 || strlen($restaurant_name) > 100) {

        $error = "Restaurant name must be between 2 and 100 characters.";

    } elseif (strlen($restaurant_address) < 5 || strlen($restaurant_address) > 100) {

        $error = "Address must be between 5 and 100 characters.";

    } elseif (!preg_match('/^([01][0-9]|2[0-3]):[0-5][0-9]$/', $opening_hours) ||
              !preg_match('/^([01][0-9]|2[0-3]):[0-5][0-9]$/', $closing_hours)) {

        $error = "Enter valid opening and closing hours.";

    } elseif ($opening_hours === $closing_hours) {

        $error = "Opening and closing hours cannot be the same.";

    } elseif (!is_numeric($blind_box_price) ||
              (float) $blind_box_price <= 0 ||
              (float) $blind_box_price > 99999999.99) {

        $error = "Blind box price must be between RM0.01 and RM99,999,999.99.";

    } elseif (strlen($blind_box_category) < 2 || strlen($blind_box_category) > 50) {

        $error = "Food category must be between 2 and 50 characters.";

    } elseif (strlen($blind_box_description) < 10 || strlen($blind_box_description) > 1000) {

        $error = "Description must be between 10 and 1,000 characters.";

    } else {

        $check = $conn->prepare(
            "SELECT restaurant_name, restaurant_address, restaurant_phone_number
             FROM restaurants
             WHERE restaurant_name = ? OR restaurant_address = ? OR restaurant_phone_number = ?
             LIMIT 1"
        );
        $check->bind_param("sss", $restaurant_name, $restaurant_address, $phone_number);
        $check->execute();
        $existing_restaurant = $check->get_result()->fetch_assoc();
        $check->close();

        if ($existing_restaurant) {

            if (strcasecmp($existing_restaurant['restaurant_name'], $restaurant_name) === 0) {
                $error = "A restaurant with that name already exists.";
            } elseif (strcasecmp($existing_restaurant['restaurant_address'], $restaurant_address) === 0) {
                $error = "That restaurant address is already in use.";
            } else {
                $error = "That restaurant phone number is already in use.";
            }

        } else {

            $image_filename = store_restaurant_image(
                $_FILES['blind_box_image'] ?? [],
                $error
            );

            if ($image_filename !== false) {

                try {

                    $sql = "INSERT INTO restaurants
                            (restaurant_name, restaurant_address, restaurant_opening_hours,
                             restaurant_closing_hours, restaurant_phone_number, blind_box_price,
                             blind_box_description, blind_box_image, blind_box_food_category)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

                    $stmt = $conn->prepare($sql);
                    $blind_box_price_f = (float) $blind_box_price;

                    $stmt->bind_param(
                        "sssssdsss",
                        $restaurant_name,
                        $restaurant_address,
                        $opening_hours,
                        $closing_hours,
                        $phone_number,
                        $blind_box_price_f,
                        $blind_box_description,
                        $image_filename,
                        $blind_box_category
                    );

                    $stmt->execute();

                    $stmt->close();
                    $conn->close();

                    $_SESSION['admin_message'] = "Restaurant \"$restaurant_name\" added successfully.";
                    header("Location: restaurants.php");
                    exit();

                } catch (mysqli_sql_exception $e) {

                    delete_restaurant_image($image_filename);
                    $error = "Failed to add restaurant. Please try again.";

                }
            }
        }
    }
}

$conn->close();
?>

<?php include '../includes/header.php'; ?>

<?php include '../includes/adminNavigation.php'; ?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/admin.css">

<section class="admin-page">

    <div class="admin-card">

        <!-- Page Header -->

        <div class="admin-header">

            <h1>🍱 Add Restaurant</h1>

        </div>


        <div class="admin-body">

            <?php if (!empty($error)) : ?>

                <p class="admin-error">
                    <?php echo htmlspecialchars($error); ?>
                </p>

            <?php endif; ?>

            <form
                method="POST"
                action="add_restaurant.php"
                class="restaurant-form"
                enctype="multipart/form-data"
            >

                <!-- Restaurant Name -->

                <div class="form-group">

                    <label for="restaurant_name">
                        Restaurant Name
                    </label>

                    <input
                        type="text"
                        id="restaurant_name"
                        name="restaurant_name"
                        minlength="2"
                        maxlength="100"
                        value="<?php echo htmlspecialchars($form['restaurant_name']); ?>"
                        required
                    >

                </div>


                <!-- Address -->

                <div class="form-group">

                    <label for="restaurant_address">
                        Address
                    </label>

                    <input
                        type="text"
                        id="restaurant_address"
                        name="restaurant_address"
                        minlength="5"
                        maxlength="100"
                        value="<?php echo htmlspecialchars($form['restaurant_address']); ?>"
                        required
                    >

                </div>


                <!-- Opening Hours -->

                <div class="form-group">

                    <label for="restaurant_opening_hours">
                        Opening Hours
                    </label>

                    <input
                        type="time"
                        id="restaurant_opening_hours"
                        name="restaurant_opening_hours"
                        value="<?php echo htmlspecialchars($form['restaurant_opening_hours']); ?>"
                        required
                    >

                </div>


                <!-- Closing Hours -->

                <div class="form-group">

                    <label for="restaurant_closing_hours">
                        Closing Hours
                    </label>

                    <input
                        type="time"
                        id="restaurant_closing_hours"
                        name="restaurant_closing_hours"
                        value="<?php echo htmlspecialchars($form['restaurant_closing_hours']); ?>"
                        required
                    >

                </div>


                <!-- Phone -->

                <div class="form-group">

                    <label for="restaurant_phone_number">
                        Phone Number
                    </label>

                    <input
                        type="text"
                        id="restaurant_phone_number"
                        name="restaurant_phone_number"
                        placeholder="e.g. 0123456789"
                        pattern="01[0-9]{8,9}"
                        maxlength="11"
                        inputmode="numeric"
                        title="Enter 10 or 11 digits starting with 01"
                        value="<?php echo htmlspecialchars($form['restaurant_phone_number']); ?>"
                        required
                    >

                </div>


                <!-- Blind Box Price -->

                <div class="form-group">

                    <label for="blind_box_price">
                        Blind Box Price (RM)
                    </label>

                    <input
                        type="number"
                        id="blind_box_price"
                        name="blind_box_price"
                        step="0.01"
                        min="0.01"
                        max="99999999.99"
                        value="<?php echo htmlspecialchars($form['blind_box_price']); ?>"
                        required
                    >

                </div>

                <!-- Food Category -->

                <div class="form-group">

                    <label for="blind_box_food_category">
                        Food Category
                    </label>

                    <input
                        type="text"
                        id="blind_box_food_category"
                        name="blind_box_food_category"
                        minlength="2"
                        maxlength="50"
                        value="<?php echo htmlspecialchars($form['blind_box_food_category']); ?>"
                        required
                    >

                </div>


                <!-- Blind Box Image -->

                <div class="form-group form-full">

                    <label for="blind_box_image">
                        Blind Box Picture
                    </label>

                    <input
                        type="file"
                        id="blind_box_image"
                        name="blind_box_image"
                        accept="image/jpeg,image/png,image/webp"
                    >

                    <p class="field-hint">
                        Optional. Upload a JPG, PNG, or WebP image smaller than 5 MB.
                    </p>

                </div>


                <!-- Description -->

                <div class="form-group form-full">

                    <label for="blind_box_description">
                        Description
                    </label>

                    <textarea
                        id="blind_box_description"
                        name="blind_box_description"
                        rows="5"
                        minlength="10"
                        maxlength="1000"
                        required
                    ><?php echo htmlspecialchars($form['blind_box_description']); ?></textarea>

                </div>


                <!-- Buttons -->

                <div class="restaurant-form-actions">

                    <button
                        type="submit"
                        class="admin-action-btn add-btn"
                    >
                        Add Restaurant
                    </button>


                    <a
                        href="restaurants.php"
                        class="admin-action-btn back-btn"
                    >
                        ← Cancel
                    </a>

                </div>

            </form>

        </div>

    </div>

</section>


<?php include '../includes/footer.php'; ?>
