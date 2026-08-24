# Blind Bite

Blind Bite is a PHP and MySQL food blind-box ordering website. Customers can browse restaurants, claim vouchers, add blind boxes to their cart, place pickup or delivery orders, save favourites, and submit ratings and reviews. Administrators can manage restaurants, promotions, vouchers, and customer enquiries.

## Main features

### Customer

- Register, log in, log out, and update profile details
- Browse and search restaurants
- Sort restaurants by rating
- View restaurant details, ratings, and reviews
- Add blind boxes to the cart or wishlist
- Claim and apply promotional vouchers
- Choose pickup or delivery (delivery adds RM5.00)
- Pay using Cash, Card, or Touch 'n Go (TNG)
- View order history, order again, and leave a review
- Send an enquiry through the Contact Us form

### Administrator

- View restaurant and order totals on the dashboard
- Add, edit, search, and delete restaurants
- Create, edit, activate, deactivate, and delete promotions
- View active and past promotions
- View customer enquiries and open Gmail to reply

## Requirements

- Windows with [WampServer](https://www.wampserver.com/en/) or another PHP/MySQL environment such as XAMPP
- PHP 8.0 or newer with the `mysqli` extension enabled
- MySQL 5.7+ or MariaDB 10.4+
- A modern web browser
- Internet access for the Touch 'n Go redirect and Gmail reply link

No Composer or Node.js installation is required.

## Installation

### 1. Copy the project

Place the project folder inside the web server's document root.

For WampServer, the recommended location is:

```text
C:\wamp64\www\WAD-Food-Blind-Box
```

For XAMPP, place it inside `C:\xampp\htdocs\` instead.

### 2. Start the services

Open WampServer and start:

- Apache
- MySQL

Wait until the WampServer icon is green before continuing.

### 3. Create and populate the database

Using phpMyAdmin:

1. Open `http://localhost/phpmyadmin/`.
2. Select the **Import** tab.
3. Import `database/create_table.sql` first. This creates the `blindbite` database and all required tables.
4. Import `database/insert_data.sql` second. This adds sample restaurants, promotions, and the default administrator account.

The required import order is:

```text
1. database/create_table.sql
2. database/insert_data.sql
```

`database/promotions and vouchers.sql` is an optional migration for an older copy of the database. Do not import it during a normal fresh installation.

You can also import the files from Windows Command Prompt while it is open in the project folder:

```bat
mysql -u root -P 3308 < database/create_table.sql
mysql -u root -P 3308 blindbite < database/insert_data.sql
```

If the MySQL root account has a password, add `-p` and enter the password when prompted.

## Database configuration

The connection settings are stored in `config/db_connect.php`:

```php
$dbhost = "localhost";
$dbUser = "root";
$dbPass = "";
$dbName = "blindbite";
$port = 3308;
```

Update these values to match the local MySQL installation:

| Setting | Purpose | Project default |
| --- | --- | --- |
| `$dbhost` | MySQL server address | `localhost` |
| `$dbUser` | MySQL username | `root` |
| `$dbPass` | MySQL password | Empty |
| `$dbName` | Database name | `blindbite` |
| `$port` | MySQL port | `3308` |

Many WampServer installations use port `3308`, while XAMPP and standard MySQL installations commonly use `3306`. If the page displays a database connection error, confirm the MySQL port and credentials first.

## Run the project

### WampServer

After Apache and MySQL are running, open:

```text
http://localhost/WAD-Food-Blind-Box/
```

The application automatically calculates its base URL, so pages, styles, scripts, and images work from the project subfolder.

### PHP built-in server (optional)

If PHP is available on the command line, open a terminal in the project root and run:

```powershell
php -S localhost:3000
```

Then visit:

```text
http://localhost:3000/
```

MySQL must still be running, and `config/db_connect.php` must contain the correct connection settings.

## Default administrator login

The sample data creates this administrator account:

```text
Username: admin
Password: admin123
```

Sign in through `authentication/login.php`. A customer account can be created from the Sign Up page.

> The default administrator password is intended for local demonstration only. Change it before placing the project on a public server.

## Project structure

```text
WAD-Food-Blind-Box/
├── admin/             Admin dashboard, restaurant, promotion, and enquiry pages
├── authentication/    Login, sign-up, logout, and profile pages
├── config/            Database connection settings
├── css/               Shared and page-specific stylesheets
├── database/          Database schema, sample data, and migration scripts
├── images/            Logos, backgrounds, and restaurant images
├── includes/          Shared headers, footers, navigation, and voucher helpers
├── js/                Shared and page-specific JavaScript files
├── pages/             Customer menu, cart, orders, wishlist, and information pages
├── index.php          Homepage
└── README.md          Project setup guide
```

## Troubleshooting

### Database connection failed

- Ensure MySQL is running.
- Check whether MySQL uses port `3308` or `3306`.
- Confirm the username and password in `config/db_connect.php`.
- Confirm that the `blindbite` database was created.

### Table not found

Import `database/create_table.sql`, followed by `database/insert_data.sql`, in that order.

### Page not found or styles are missing

- Confirm the project folder is inside the correct document root.
- Use `http://localhost/WAD-Food-Blind-Box/` for the recommended WampServer folder name.
- Avoid opening PHP files directly from File Explorer; access them through Apache or the PHP development server.

### Uploaded restaurant images are not displayed

Ensure the web server can write to `images/restaurants/` and that the uploaded file is a supported image type.

## Notes

- Application times use the `Asia/Kuala_Lumpur` timezone.
- TNG is a demonstration payment option; no real payment is processed.
- Delivery orders add a fixed RM5.00 delivery fee.
- Restaurant image uploads are stored in `images/restaurants/`.
