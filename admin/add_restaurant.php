<?php

include 'admin_auth.php';
require_admin_login();

include '../config/db_connect.php';

$error = "";

$form = [
    'restaurant_name' => '',
    'restaurant_address' => '',
    'restaurant_opening_hours' => '',
    'restaurant_closing_hours' => '',
    'restaurant_phone_number' => '',
    'blind_box_price' => '',
    'blind_box_description' => '',
    'blind_box_remaining_quantity' => '',
    'blind_box_food_category' => '',
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $restaurant_name      = trim($_POST['restaurant_name']);
    $restaurant_address   = trim($_POST['restaurant_address']);
    $opening_hours        = $_POST['restaurant_opening_hours'];
    $closing_hours        = $_POST['restaurant_closing_hours'];
    $phone_number         = trim($_POST['restaurant_phone_number'] ?? '');
    $blind_box_price      = $_POST['blind_box_price'];
    $blind_box_description = trim($_POST['blind_box_description']);
    $blind_box_quantity   = $_POST['blind_box_remaining_quantity'];
    $blind_box_category   = trim($_POST['blind_box_food_category']);

    // Keep entered values so the form can be re-filled if something goes wrong
    $form = [
        'restaurant_name' => $restaurant_name,
        'restaurant_address' => $restaurant_address,
        'restaurant_opening_hours' => $opening_hours,
        'restaurant_closing_hours' => $closing_hours,
        'restaurant_phone_number' => $phone_number,
        'blind_box_price' => $blind_box_price,
        'blind_box_description' => $blind_box_description,
        'blind_box_remaining_quantity' => $blind_box_quantity,
        'blind_box_food_category' => $blind_box_category,
    ];

    if (
        $restaurant_name === '' || $restaurant_address === '' ||
        $opening_hours === '' || $closing_hours === '' ||
        $phone_number === '' || $blind_box_price === '' ||
        $blind_box_description === '' || $blind_box_quantity === '' ||
        $blind_box_category === ''
    ) {

        $error = "Please fill in all fields.";

    } elseif (!preg_match('/^01[0-9]{8,9}$/', $phone_number)){
        $error = "Please enter a valid phone number (e.g., 0123456789).";
    }
    elseif (!is_numeric($blind_box_price) || $blind_box_price < 0) {

        $error = "Blind box price must be a valid, non-negative number.";

    } elseif (!ctype_digit((string) $blind_box_quantity) || (int) $blind_box_quantity < 0) {

        $error = "Remaining quantity must be a non-negative whole number.";

    } else {

        // Restaurant name is the primary key, so it must be unique
        $check = $conn->prepare("SELECT restaurant_name FROM restaurants WHERE restaurant_name = ?");
        $check->bind_param("s", $restaurant_name);
        $check->execute();

        if ($check->get_result()->num_rows > 0) {

            $error = "A restaurant with that name already exists.";
            $check->close();

        } else {

            $check->close();

            $sql = "INSERT INTO restaurants
                    (restaurant_name, restaurant_address, restaurant_opening_hours,
                     restaurant_closing_hours, restaurant_phone_number, blind_box_price,
                     blind_box_description, blind_box_remaining_quantity, blind_box_food_category)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);

            if (!$stmt) {
                die("Database error: " . $conn->error);
            }

            $blind_box_price_f = (float) $blind_box_price;
            $blind_box_quantity_i = (int) $blind_box_quantity;

            $stmt->bind_param(
                "sssssdsis",
                $restaurant_name,
                $restaurant_address,
                $opening_hours,
                $closing_hours,
                $phone_number,
                $blind_box_price_f,
                $blind_box_description,
                $blind_box_quantity_i,
                $blind_box_category
            );

            if ($stmt->execute()) {

                $stmt->close();
                $conn->close();

                $_SESSION['admin_message'] = "Restaurant \"$restaurant_name\" added successfully.";
                header("Location: restaurants.php");
                exit();

            } else {

                $error = "Failed to add restaurant: " . $stmt->error;
                $stmt->close();

            }
        }
    }
}

$conn->close();
?>

<?php include '../includes/header.php'; ?>

<?php include '../includes/adminNavigation.php'; ?>


<section class="admin-page">

    <div class="admin-card">

        <!-- Page Header -->

        <div class="admin-header">

            <h1>🍱 Add Restaurant</h1>

        </div>


        <div class="admin-body">

            <form
                method="POST"
                action="add_restaurant.php"
                class="restaurant-form"
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
                        required
                    >

                </div>


                <!-- Phone -->

                <div class="form-group">

                    <label for="restaurant_phone_number">
                        Phone Number
                    </label>

                    <input type="text" id="restaurant_phone_number" name="restaurant_phone_number" placeholder="e.g. 0123456789" pattern="01[0-9]{8,9}" maxlength="11" required>

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
                        min="0"
                        required
                    >

                </div>


                <!-- Remaining Quantity -->

                <div class="form-group">

                    <label for="blind_box_remaining_quantity">
                        Remaining Quantity
                    </label>

                    <input
                        type="number"
                        id="blind_box_remaining_quantity"
                        name="blind_box_remaining_quantity"
                        min="0"
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
                        required
                    >

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
                        required
                    ></textarea>

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