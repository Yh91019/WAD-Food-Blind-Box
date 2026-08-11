<?php

include 'admin_auth.php';
require_admin_login();

include '../config/db_connect.php';


$result = $conn->query(
    "SELECT * FROM restaurants ORDER BY restaurant_name"
);

?>


<?php include '../includes/header.php'; ?>

<?php include '../includes/adminNavigation.php'; ?>


<section class="admin-page">

    <div class="admin-card">

        <!-- Page Header -->

        <div class="admin-header">

            <h1>🍱Manage Restaurants</h1>

        </div>


        <div class="admin-body">


            <!-- Messages -->

            <?php if (isset($_SESSION['admin_message'])) : ?>

                <p class="admin-success">

                    <?php
                    echo htmlspecialchars(
                        $_SESSION['admin_message']
                    );

                    unset($_SESSION['admin_message']);
                    ?>

                </p>

            <?php endif; ?>


            <?php if (isset($_SESSION['admin_error'])) : ?>

                <p class="admin-error">

                    <?php
                    echo htmlspecialchars(
                        $_SESSION['admin_error']
                    );

                    unset($_SESSION['admin_error']);
                    ?>

                </p>

            <?php endif; ?>


            <!-- Top Buttons -->

            <div class="restaurant-page-actions">
                <a href="dashboard.php" class="admin-action-btn back-btn">
                    ← Back to Dashboard
                </a>

                <a href="add_restaurant.php" class="admin-action-btn add-btn">
                    + Add Restaurant
                </a>

            </div>


            <!-- Restaurant Table -->

            <div class="restaurant-table-container">

                <table class="restaurant-table">

                    <thead>

                        <tr>

                            <th>Restaurant</th>

                            <th>Address</th>

                            <th>Opening</th>

                            <th>Closing</th>

                            <th>Phone</th>

                            <th>Box Price</th>

                            <th>Qty</th>

                            <th>Category</th>

                            <th>Actions</th>

                        </tr>

                    </thead>


                    <tbody>

                        <?php if ($result && $result->num_rows > 0) : ?>

                            <?php while ($row = $result->fetch_assoc()) : ?>

                                <tr>

                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $row['restaurant_name']
                                        );
                                        ?>
                                    </td>


                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $row['restaurant_address']
                                        );
                                        ?>
                                    </td>


                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $row['restaurant_opening_hours']
                                        );
                                        ?>
                                    </td>


                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $row['restaurant_closing_hours']
                                        );
                                        ?>
                                    </td>


                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $row['restaurant_phone_number']
                                        );
                                        ?>
                                    </td>


                                    <td class="price-cell">
                                        RM
                                        <?php
                                        echo number_format(
                                            $row['blind_box_price'],
                                            2
                                        );
                                        ?>
                                    </td>


                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $row['blind_box_remaining_quantity']
                                        );
                                        ?>
                                    </td>


                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $row['blind_box_food_category']
                                        );
                                        ?>
                                    </td>


                                    <td>

                                        <div class="table-actions">

                                            <a
                                                href="edit_restaurant.php?restaurant=<?php
                                                    echo urlencode(
                                                        $row['restaurant_name']
                                                    );
                                                ?>"
                                                class="edit-btn"
                                            >
                                                Edit
                                            </a>


                                            <form
                                                method="POST"
                                                action="delete_restaurant.php"
                                                onsubmit="return confirm(
                                                    'Are you sure you want to delete this restaurant?'
                                                );"
                                            >

                                                <input
                                                    type="hidden"
                                                    name="restaurant_name"
                                                    value="<?php
                                                        echo htmlspecialchars(
                                                            $row['restaurant_name']
                                                        );
                                                    ?>"
                                                >


                                                <button
                                                    type="submit"
                                                    class="delete-btn"
                                                >
                                                    Delete
                                                </button>

                                            </form>

                                        </div>

                                    </td>

                                </tr>

                            <?php endwhile; ?>

                        <?php else : ?>

                            <tr>

                                <td
                                    colspan="9"
                                    class="no-restaurants"
                                >
                                    No restaurants available.
                                </td>

                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</section>


<?php

$conn->close();

include '../includes/footer.php';

?>