<?php

include 'admin_auth.php';
require_admin_login();

include '../config/db_connect.php';

$sql = "SELECT * FROM restaurants ORDER BY restaurant_name";
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Restaurants - Blind Bite Admin</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>

    <div class="admin-page">

        <div class="admin-card admin-card-wide">

            <div class="admin-header">
                <h1>🍱 Manage Restaurants</h1>
            </div>

            <div class="admin-body">

                <?php if (isset($_SESSION['admin_message'])) : ?>
                    <p class="admin-success"><?php echo htmlspecialchars($_SESSION['admin_message']); unset($_SESSION['admin_message']); ?></p>
                <?php endif; ?>

                <?php if (isset($_SESSION['admin_error'])) : ?>
                    <p class="admin-error"><?php echo htmlspecialchars($_SESSION['admin_error']); unset($_SESSION['admin_error']); ?></p>
                <?php endif; ?>

                <div class="admin-actions-bar">
                    <a href="add_restaurant.php" class="manage-btn">+ Add Restaurant</a>
                    <a href="dashboard.php" class="admin-back-link">&larr; Back to Dashboard</a>
                </div>

                <!-- Search Restaurant -->
                <div class="admin-search-bar">
                    <input
                        type="text"
                        id="restaurantSearchInput"
                        placeholder="Search restaurant name..."
                        autocomplete="off"
                    >
                </div>

                <p id="restaurantNoResults" class="admin-no-results" style="display: none;">
                    No restaurants match your search.
                </p>

                <div class="admin-table-wrap">

                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Restaurant</th>
                                <th>Address</th>
                                <th>Hours</th>
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

                                    <tr class="restaurant-row"
                                        data-name="<?php echo htmlspecialchars(strtolower($row['restaurant_name'])); ?>">
                                        <td><?php echo htmlspecialchars($row['restaurant_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['restaurant_address']); ?></td>
                                        <td>
                                            <?php echo htmlspecialchars($row['restaurant_opening_hours']); ?>
                                            -
                                            <?php echo htmlspecialchars($row['restaurant_closing_hours']); ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['restaurant_phone_number']); ?></td>
                                        <td>RM <?php echo number_format($row['blind_box_price'], 2); ?></td>
                                        <td><?php echo (int) $row['blind_box_remaining_quantity']; ?></td>
                                        <td><?php echo htmlspecialchars($row['blind_box_food_category']); ?></td>
                                        <td class="admin-table-actions">
                                            <a
                                                href="edit_restaurant.php?restaurant=<?php echo urlencode($row['restaurant_name']); ?>"
                                                class="admin-edit-link"
                                            >Edit</a>

                                            <form
                                                method="POST"
                                                action="delete_restaurant.php"
                                                onsubmit="return confirm('Delete &quot;<?php echo htmlspecialchars(addslashes($row['restaurant_name'])); ?>&quot;? This cannot be undone.');"
                                            >
                                                <input type="hidden" name="restaurant_name" value="<?php echo htmlspecialchars($row['restaurant_name']); ?>">
                                                <button type="submit" class="admin-delete-btn">Delete</button>
                                            </form>
                                        </td>
                                    </tr>

                                <?php endwhile; ?>

                            <?php else : ?>

                                <tr>
                                    <td colspan="8">No restaurants yet. Click "+ Add Restaurant" to create one.</td>
                                </tr>

                            <?php endif; ?>

                        </tbody>
                    </table>

                </div>

            </div>

        </div>

    </div>

    <script>
        const searchInput = document.getElementById("restaurantSearchInput");
        const restaurantRows = document.querySelectorAll(".restaurant-row");
        const noResults = document.getElementById("restaurantNoResults");

        searchInput.addEventListener("input", function () {
            const searchValue = this.value.trim().toLowerCase();
            let visibleCount = 0;

            restaurantRows.forEach(function (row) {
                const restaurantName = row.getAttribute("data-name");

                if (restaurantName.includes(searchValue)) {
                    row.style.display = "";
                    visibleCount++;
                } else {
                    row.style.display = "none";
                }
            });

            noResults.style.display =
                visibleCount === 0 && restaurantRows.length > 0
                    ? "block"
                    : "none";
        });
    </script>

</body>
</html>
<?php $conn->close(); ?>