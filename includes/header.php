<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * BASE_URL: the site's root URL path, worked out automatically so that
 * links/images/css/js work no matter how deep the current page is
 * (root, /pages/, /authentication/, /admin/ ...) and no matter which
 * local server is used:
 *   - WampServer, where the project sits in a subfolder of www
 *     (e.g. http://localhost/WAD-Food-Blind-Box/...)
 *   - VS Code's PHP built-in server, run with this project folder
 *     itself as the server root (e.g. http://localhost:PORT/...)
 */
if (!defined('BASE_URL')) {
    $projectRoot = str_replace('\\', '/', realpath(__DIR__ . '/..'));
    $docRoot = (!empty($_SERVER['DOCUMENT_ROOT']))
        ? str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT']))
        : $projectRoot;

    if ($docRoot && stripos($projectRoot, $docRoot) === 0) {
        // Project lives inside the server's document root (e.g. WampServer)
        $base = substr($projectRoot, strlen($docRoot));
    } else {
        // Server's document root IS the project root (e.g. VS Code PHP server)
        $base = '';
    }

    define('BASE_URL', rtrim($base, '/'));
}

require_once __DIR__ . '/restaurant_image.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blind Bite</title>

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/style.css?v=<?php echo filemtime(__DIR__ . '/../css/style.css'); ?>">
</head>
<body>
