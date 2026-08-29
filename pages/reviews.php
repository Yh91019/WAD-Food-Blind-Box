<?php

session_start();

include '../config/db_connect.php';

include '../includes/reviewer_guard.php';



$username = $_SESSION['username'];

$message = "";
$error = "";



if ($_SERVER["REQUEST_METHOD"] === "POST") {


   
    $history_id =
        isset($_POST['history_id'])
        ? (int) $_POST['history_id']
        : 0;

    $rating =
        isset($_POST['rating'])
        ? (int) $_POST['rating']
        : 0;

    $review =
        trim($_POST['review'] ?? "");



    if ($history_id <= 0) {

        $error =
            "Invalid review item.";

    }

    elseif ($rating < 1 || $rating > 5) {

        $error =
            "Please select a rating from 1 to 5.";

    }

    elseif ($review === "") {

        $error =
            "Please write a review.";

    }

    elseif (strlen($review) > 1000) {

        $error =
            "Your review must be 1,000 characters or fewer.";

    }

    else {


     
        $history_sql = "

            SELECT
                history_id,
                restaurant_name,
                status

            FROM history

            WHERE history_id = ?

            AND username = ?

            AND status = 'Completed'

            LIMIT 1

        ";


        $history_stmt =
            $conn->prepare($history_sql);


        if (!$history_stmt) {

            $error =
                "Database error.";

        }

        else {


            $history_stmt->bind_param(
                "is",
                $history_id,
                $username
            );


            $history_stmt->execute();


            $history_result =
                $history_stmt->get_result();


            $history =
                $history_result->fetch_assoc();


            $history_stmt->close();


       
            if (!$history) {

                $error =
                    "This review item could not be found.";

            }

            else {


            
                $review_sql = "

                    INSERT INTO reviews
                    (
                        history_id,
                        username,
                        restaurant_name,
                        rating,
                        review
                    )

                    VALUES
                    (
                        ?,
                        ?,
                        ?,
                        ?,
                        ?
                    )

                    ON DUPLICATE KEY UPDATE

                        rating = VALUES(rating),

                        review = VALUES(review),

                        updated_at =
                            CURRENT_TIMESTAMP

                ";


                $review_stmt =
                    $conn->prepare($review_sql);


                if (!$review_stmt) {

                    $error =
                        "Unable to prepare review.";

                }

                else {


                    $review_stmt->bind_param(
                        "issis",
                        $history_id,
                        $username,
                        $history['restaurant_name'],
                        $rating,
                        $review
                    );


                    if (
                        $review_stmt->execute()
                    ) {

                        $message =
                            "Your rating and review have been saved successfully.";

                    }

                    else {

                        $error =
                            "Unable to save your review.";

                    }


                    $review_stmt->close();

                }

            }

        }

    }

}



$items = [];


$item_sql = "

    SELECT

        h.history_id,

        h.restaurant_name,

        h.blind_box_price,

        h.quantity,

        h.order_date,

        rv.review_id,

        rv.rating,

        rv.review

    FROM history h

    LEFT JOIN reviews rv

        ON h.history_id = rv.history_id

    WHERE h.username = ?

    AND h.status = 'Completed'

    ORDER BY h.order_date DESC

";


$item_stmt =
    $conn->prepare($item_sql);


if ($item_stmt) {


    $item_stmt->bind_param(
        "s",
        $username
    );


    $item_stmt->execute();


    $item_result =
        $item_stmt->get_result();


    while (
        $row =
        $item_result->fetch_assoc()
    ) {

        $items[] = $row;

    }


    $item_stmt->close();

}


$conn->close();

?>


<?php include '../includes/header.php'; ?>

<?php include '../includes/navigation.php'; ?>

<link rel="stylesheet" href="../css/orderhistory.css?v=<?php echo filemtime(__DIR__ . '/../css/orderhistory.css'); ?>">

<main id="bb-review-page">



    <section id="bb-review-header">


        <div>

            <p class="bb-review-kicker">

                BLIND BITE

            </p>


            <h1>

                Restaurant Reviews

            </h1>


            <p class="bb-review-welcome">

                Welcome,

                <strong>

                    <?php
                    echo htmlspecialchars(
                        $username
                    );
                    ?>

                </strong>

            </p>

        </div>


        <a
            href="../authentication/logout.php"
            id="bb-review-logout"
        >

            Logout

        </a>


    </section>


    <?php if (!empty($message)) : ?>

        <div
            id="bb-review-message"
            class="bb-review-success"
        >

            <?php
            echo htmlspecialchars(
                $message
            );
            ?>

        </div>

    <?php endif; ?>



    <?php if (!empty($error)) : ?>

        <div
            id="bb-review-message"
            class="bb-review-error"
        >

            <?php
            echo htmlspecialchars(
                $error
            );
            ?>

        </div>

    <?php endif; ?>


  
    <section id="bb-review-intro">


        <h2>

            Rate Your Blind Bite

        </h2>


        <p>

            Share your experience by giving
            a rating from 1 to 5 stars and
            writing a review.

        </p>


    </section>


 
    <section id="bb-review-list">


        <?php if (empty($items)) : ?>


           
            <div id="bb-review-empty">


                <div class="bb-review-empty-icon">

                    ★

                </div>


                <h2>

                    No completed orders yet

                </h2>


                <p>

                    There are currently no completed
                    Blind Bite items available for review.

                </p>


            </div>


        <?php else : ?>


            <?php foreach ($items as $item) : ?>


             
                <article class="bb-review-card">


                 
                    <div class="bb-review-card-header">


                        <div>


                            <p class="bb-review-restaurant-label">

                                RESTAURANT

                            </p>


                            <h2>

                                <?php

                                echo htmlspecialchars(
                                    $item['restaurant_name']
                                );

                                ?>

                            </h2>


                        </div>


                        <div class="bb-review-price">

                            RM

                            <?php

                            echo number_format(
                                (float)
                                $item['blind_box_price'],
                                2
                            );

                            ?>

                        </div>


                    </div>


               
                    <div class="bb-review-order-info">


                        <span>

                            Quantity:

                            <?php

                            echo (int)
                                $item['quantity'];

                            ?>

                        </span>


                        <span>

                            Order:

                            <?php

                            echo htmlspecialchars(
                                $item['order_date']
                            );

                            ?>

                        </span>


                    </div>


                
                    <form
                        method="POST"
                        action="reviews.php"
                        class="bb-review-form"
                    >


                    
                        <input
                            type="hidden"
                            name="history_id"
                            value="<?php
                                echo (int)
                                    $item['history_id'];
                            ?>"
                        >


                    
                        <div
                            class="bb-review-rating-section"
                        >


                            <label
                                class="bb-review-rating-label"
                            >

                                Your Rating

                            </label>


                            <div
                                id="bb-review-star-rating-<?php
                                    echo (int)
                                        $item['history_id'];
                                ?>"
                                class="bb-review-star-rating"
                            >


                                <?php
                                for (
                                    $i = 5;
                                    $i >= 1;
                                    $i--
                                ) :
                                ?>


                                    <input
                                        type="radio"
                                        id="bb-review-star-<?php
                                            echo (int)
                                                $item['history_id'];
                                        ?>-<?php
                                            echo $i;
                                        ?>"
                                        name="rating"
                                        value="<?php
                                            echo $i;
                                        ?>"
                                        <?php

                                        if (
                                            (int)
                                            $item['rating']
                                            === $i
                                        ) {

                                            echo 'checked';

                                        }

                                        ?>
                                        required
                                    >


                                    <label
                                        for="bb-review-star-<?php
                                            echo (int)
                                                $item['history_id'];
                                        ?>-<?php
                                            echo $i;
                                        ?>"
                                    >

                                        ★

                                    </label>


                                <?php endfor; ?>


                            </div>


                            <p
                                class="bb-review-rating-help"
                            >

                                Select from 1 to 5 stars

                            </p>


                        </div>


                     
                        <div
                            class="bb-review-input-section"
                        >


                            <label
                                for="bb-review-textarea-<?php
                                    echo (int)
                                        $item['history_id'];
                                ?>"
                            >

                                Your Review

                            </label>


                            <textarea
                                id="bb-review-textarea-<?php
                                    echo (int)
                                        $item['history_id'];
                                ?>"
                                name="review"
                                maxlength="1000"
                                placeholder="Tell us about your experience..."
                                required
                            ><?php

                                echo htmlspecialchars(
                                    $item['review'] ?? ''
                                );

                            ?></textarea>


                            <div
                                class="bb-review-character-limit"
                            >

                                Maximum 1,000 characters

                            </div>


                        </div>


                        <!-- ======================================
                             SUBMIT BUTTON
                             ====================================== -->

                        <button
                            type="submit"
                            id="bb-review-submit-<?php
                                echo (int)
                                    $item['history_id'];
                            ?>"
                            class="bb-review-submit"
                        >

                            <?php

                            if (
                                !empty(
                                    $item['review_id']
                                )
                            ) {

                                echo "Update Review";

                            }

                            else {

                                echo "Submit Review";

                            }

                            ?>

                        </button>


                    </form>


                    <!-- ==========================================
                         EXISTING REVIEW MESSAGE
                         ========================================== -->

                    <?php
                    if (
                        !empty(
                            $item['review_id']
                        )
                    ) :
                    ?>


                        <div
                            id="bb-review-existing-<?php
                                echo (int)
                                    $item['history_id'];
                            ?>"
                            class="bb-review-existing"
                        >


                            <span
                                class="bb-review-check"
                            >

                                ✓

                            </span>


                            <span>

                                You have already reviewed
                                this order. You can update
                                your rating or review above.

                            </span>


                        </div>


                    <?php endif; ?>


                </article>


            <?php endforeach; ?>


        <?php endif; ?>


    </section>


    <section id="bb-review-footer">


        <p>

            ★ Your feedback helps Blind Bite
            improve the dining experience.

        </p>


    </section>


</main>


<?php include '../includes/footer.php'; ?>