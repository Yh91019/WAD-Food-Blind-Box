# Blind Bite Food Blind Box

Blind Bite is a PHP and MySQL website where customers can discover surprise
food boxes from local restaurants. Customers can create an account, save
restaurants, manage a cart, place an order, and view their order history.
Administrators can manage the restaurant list.

## Shared behaviour

- Pages use `config/db_connect.php` when they need data from MySQL.
- `includes/header.php`, `includes/navigation.php`, and `includes/footer.php`
  provide the common page layout.
- Admin pages call `require_admin_login()` to prevent customer access.
- Forms are checked on the server and use prepared SQL statements for user
  input. JavaScript is used only for browser interactions such as menus,
  searches, quantity buttons, and pop-up windows.

## Main file

| File | Purpose |
| --- | --- |
| `index.php` | Displays the home page and a list of available restaurants. |

## Admin files

| File | Purpose |
| --- | --- |
| `admin/admin_auth.php` | Provides the login check used by admin-only pages. |
| `admin/dashboard.php` | Shows the admin home page with restaurant and order totals. |
| `admin/restaurants.php` | Lists restaurants and provides search, edit, delete, and add controls. |
| `admin/add_restaurant.php` | Checks the form and adds a new restaurant to the database. |
| `admin/edit_restaurant.php` | Loads a restaurant and saves changes made by the admin. |
| `admin/delete_restaurant.php` | Deletes the restaurant selected by the admin. |

## Authentication files

| File | Purpose |
| --- | --- |
| `authentication/login.php` | Logs in a customer or administrator and starts their session. |
| `authentication/signup.php` | Creates a customer account after checking the entered details. |
| `authentication/profile.php` | Displays and updates customer details and saved card details. |
| `authentication/logout.php` | Ends the current session and returns to the login page. |

## Customer page files

| File | Purpose |
| --- | --- |
| `pages/menu.php` | Displays all restaurants and the menu search box. |
| `pages/details.php` | Shows one restaurant and lets a customer add it to the cart or wishlist. |
| `pages/cart.php` | Manages quantities, order type, payment choice, and order placement. |
| `pages/wishlist.php` | Lists saved restaurants and lets customers remove or move an item to the cart. |
| `pages/orderhistory.php` | Displays the signed-in customer's previous orders and their status. |
| `pages/order_complete.php` | Confirms that an order was placed successfully. |
| `pages/aboutus.php` | Explains the Blind Bite idea, story, and mission. |

## Shared include and configuration files

| File | Purpose |
| --- | --- |
| `config/db_connect.php` | Opens the shared connection to the `blindbite` MySQL database. |
| `includes/header.php` | Starts the session, works out the project URL, and creates the page header. |
| `includes/navigation.php` | Displays the customer side menu and its open/close behaviour. |
| `includes/adminNavigation.php` | Displays the admin side menu and its open/close behaviour. |
| `includes/footer.php` | Closes the page and displays the site footer. |
| `includes/restaurant_image.php` | Validates restaurant image uploads and provides the default image when needed. |

## JavaScript files

| File | Purpose |
| --- | --- |
| `js/script.js` | Opens the customer menu and closes it after a click outside. |
| `js/aboutus.js` | Opens or closes the menu on the About Us page. |
| `js/menu-search.js` | Filters restaurant cards using the text entered in the menu search box. |
| `js/quantity.js` | Controls the plus and minus buttons on the item quantity selector. |
| `js/payment.js` | Opens the payment window, records the chosen method, and submits the order. |
| `js/profile.js` | Switches the profile page between view and edit mode. |
| `js/status.js` | Updates restaurant open or closed labels using the current time. |
| `js/wishlist.js` | Provides wishlist suggestions, filtering, and action messages. |
| `js/cart.js` | Calculates cart totals and supports quantity, removal, and delivery controls. |

## Style files

| File | Purpose |
| --- | --- |
| `css/style.css` | Contains the main shared styles for the header, menu, home, details, cart, and profile pages. |
| `css/admin.css` | Styles the admin dashboard, restaurant table, and admin forms. |
| `css/aboutus.css` | Styles the About Us sections and responsive layout. |
| `css/login.css` | Styles the login and sign-up forms. |
| `css/orderhistory.css` | Styles order cards and their status labels. |
| `css/wishlist.css` | Styles the wishlist grid, cards, and buttons. |
| `css/status.css` | Sets the colours of restaurant open and closed labels. |
| `css/body{.css` | Contains an older basic cart and table layout. |

## Database files

| File | Purpose |
| --- | --- |
| `database/create_table.sql` | Creates the database and its restaurant, user, payment, cart, wishlist, history, and admin tables. |
| `database/insert_data.sql` | Adds sample restaurants and the starting admin account. |

## Image files

| File | Purpose |
| --- | --- |
| `images/BBlogo.png` | Blind Bite logo used in the page header. |
| `images/BBbox.png` | Blind box picture used on restaurant cards. |
| `images/restaurants/` | Stores blind box pictures uploaded by administrators. |
| `images/bg.jpg` | Large banner image used on the home page. |
| `images/image1.png` | Illustration used on the About Us page. |
| `images/whatsappimage.jpg` | Additional project image asset. |

## Editor files

| File | Purpose |
| --- | --- |
| `.vscode/launch.json` | Stores the local Chrome launch setup for Visual Studio Code. |
| `.vscode/settings.json` | Stores local SQLTools database connection settings. |
