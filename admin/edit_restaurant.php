<?php

include 'admin_auth.php';
require_admin_login();

include '../config/db_connect.php';

$error = "";

// The restaurant being edited is identified by its original name,
// since restaurant_name is the primary key and can itself be changed.
$original_name = isset($_GET['restaurant']) ? $_GET['restaurant'] : (isset($_POST['original_restaurant_name']) ? $_POST['original_restaurant_name'] : '');

if ($original_name === '') {
    header("Location: restaurants.php");
    exit();
}

// Load the current row to pre-fill the form
$stmt = $conn->prepare("SELECT * FROM restaurants WHERE restaurant_name = ?");
$stmt->bind_param("s", $original_name);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {

    $stmt->close();
    $conn->close();

    $_SESSION['admin_error'] = "Restaurant not found.";
    header("Location: restaurants.php");
    exit();

}

$form = $result->fetch_assoc();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $new_name              = trim($_POST['restaurant_name']);
    $restaurant_address    = trim($_POST['restaurant_address']);
    $opening_hours         = $_POST['restaurant_opening_hours'];
    $closing_hours         = $_POST['restaurant_closing_hours'];
    $phone_number          = trim($_POST['restaurant_phone_number']);
    $blind_box_price       = $_POST['blind_box_price'];
    $blind_box_description = trim($_POST['blind_box_description']);
    $blind_box_quantity    = $_POST['blind_box_remaining_quantity'];
    $blind_box_category    = trim($_POST['blind_box_food_category']);

    // Keep entered values so the form re-fills correctly on error
    $form = [
        'restaurant_name' => $new_name,
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
        $new_name === '' || $restaurant_address === '' ||
        $opening_hours === '' || $closing_hours === '' ||
        $phone_number === '' || $blind_box_price === '' ||
        $blind_box_description === '' || $blind_box_quantity === '' ||
        $blind_box_category === ''
    ) {

        $error = "Please fill in all fields.";

    } elseif (!is_numeric($blind_box_price) || $blind_box_price < 0) {

        $error = "Blind box price must be a valid, non-negative number.";

    } elseif (!ctype_digit((string) $blind_box_quantity) || (int) $blind_box_quantity < 0) {

        $error = "Remaining quantity must be a non-negative whole number.";

    } else {

        // If the name changed, make sure the new name isn't already taken
        if ($new_name !== $original_name) {

            $check = $conn->prepare("SELECT restaurant_name FROM restaurants WHERE restaurant_name = ?");
            $check->bind_param("s", $new_name);
            $check->execute();

            if ($check->get_result()->num_rows > 0) {
                $error = "A restaurant with that name already exists.";
            }

            $check->close();
        }

        if ($error === "") {

            $blind_box_price_f = (float) $blind_box_price;
            $blind_box_quantity_i = (int) $blind_box_quantity;
            $renaming = ($new_name !== $original_name);

            try {

                $conn->begin_transaction();

                if ($renaming) {
                    // Renaming changes the primary key. cart/wishlist only cascade
                    // on DELETE, not UPDATE, so briefly disable FK checks while we
                    // update the restaurant row and its dependent rows together.
                    $conn->query("SET FOREIGN_KEY_CHECKS = 0");
                }

                $sql = "UPDATE restaurants SET
                            restaurant_name = ?,
                            restaurant_address = ?,
                            restaurant_opening_hours = ?,
                            restaurant_closing_hours = ?,
                            restaurant_phone_number = ?,
                            blind_box_price = ?,
                            blind_box_description = ?,
                            blind_box_remaining_quantity = ?,
                            blind_box_food_category = ?
                        WHERE restaurant_name = ?";

                $update_stmt = $conn->prepare($sql);

                $update_stmt->bind_param(
                    "sssssdsiss",
                    $new_name,
                    $restaurant_address,
                    $opening_hours,
                    $closing_hours,
                    $phone_number,
                    $blind_box_price_f,
                    $blind_box_description,
                    $blind_box_quantity_i,
                    $blind_box_category,
                    $original_name
                );

                $update_stmt->execute();
                $update_stmt->close();

                if ($renaming) {

                    // Keep every table that stores restaurant_name in sync
                    foreach (['cart', 'wishlist', 'history'] as $table) {

                        $sync_stmt = $conn->prepare(
                            "UPDATE $table SET restaurant_name = ? WHERE restaurant_name = ?"
                        );
                        $sync_stmt->bind_param("ss", $new_name, $original_name);
                        $sync_stmt->execute();
                        $sync_stmt->close();

                    }

                    $conn->query("SET FOREIGN_KEY_CHECKS = 1");
                }

                $conn->commit();

                $conn->close();

                $_SESSION['admin_message'] = "Restaurant \"$new_name\" updated successfully.";
                header("Location: restaurants.php");
                exit();

            } catch (mysqli_sql_exception $e) {

                $conn->rollback();

                if ($renaming) {
                    $conn->query("SET FOREIGN_KEY_CHECKS = 1");
                }

                $error = "Failed to update restaurant: " . $e->getMessage();

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

            <h1>🍱 Edit Restaurant</h1>

        </div>


        <div class="admin-body">

            <?php if (!empty($error)) : ?>

                <p class="admin-error">
                    <?php echo htmlspecialchars($error); ?>
                </p>

            <?php endif; ?>


            <form
                method="POST"
                action="edit_restaurant.php"
                class="restaurant-form"
            >

                <!-- Keep original restaurant name -->

                <input
                    type="hidden"
                    name="original_restaurant_name"
                    value="<?php echo htmlspecialchars($original_name); ?>"
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
                        value="<?php echo htmlspecialchars(
                            substr($form['restaurant_opening_hours'], 0, 5)
                        ); ?>"
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
                        value="<?php echo htmlspecialchars(
                            substr($form['restaurant_closing_hours'], 0, 5)
                        ); ?>"
                        required
                    >

                </div>


                <!-- Phone Number -->

                <div class="form-group">

                    <label for="restaurant_phone_number">
                        Phone Number
                    </label>

                    <input
                        type="text"
                        id="restaurant_phone_number"
                        name="restaurant_phone_number"
                        value="<?php echo htmlspecialchars(
                            $form['restaurant_phone_number']
                        ); ?>"
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
                        step="0.01"
                        min="0"
                        id="blind_box_price"
                        name="blind_box_price"
                        value="<?php echo htmlspecialchars(
                            $form['blind_box_price']
                        ); ?>"
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
                        step="1"
                        min="0"
                        id="blind_box_remaining_quantity"
                        name="blind_box_remaining_quantity"
                        value="<?php echo htmlspecialchars(
                            $form['blind_box_remaining_quantity']
                        ); ?>"
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
                        value="<?php echo htmlspecialchars(
                            $form['blind_box_food_category']
                        ); ?>"
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
                    ><?php echo htmlspecialchars(
                        $form['blind_box_description']
                    ); ?></textarea>

                </div>


                <!-- Buttons -->

                <div class="restaurant-form-actions">

                    <button
                        type="submit"
                        class="admin-action-btn add-btn"
                    >
                        Save Changes
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