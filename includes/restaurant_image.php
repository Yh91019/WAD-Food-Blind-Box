<?php

function restaurant_image_url(?string $filename): string
{
    $default_url = BASE_URL . '/images/BBbox.png';

    if (!$filename || basename($filename) !== $filename) {
        return $default_url;
    }

    $image_path = __DIR__ . '/../images/restaurants/' . $filename;

    if (!is_file($image_path)) {
        return $default_url;
    }

    return BASE_URL . '/images/restaurants/' . rawurlencode($filename);
}

function store_restaurant_image(array $file, string &$error)
{
    $upload_error = $file['error'] ?? UPLOAD_ERR_NO_FILE;

    if ($upload_error === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($upload_error !== UPLOAD_ERR_OK) {
        $error = 'The image could not be uploaded. Please try again.';
        return false;
    }

    if (($file['size'] ?? 0) < 1 || $file['size'] > 5 * 1024 * 1024) {
        $error = 'Blind box image must be smaller than 5 MB.';
        return false;
    }

    $allowed_types = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];
    $temporary_path = $file['tmp_name'] ?? '';

    if ($temporary_path === '' || !is_uploaded_file($temporary_path)) {
        $error = 'The uploaded image is not valid.';
        return false;
    }

    $mime_type = $temporary_path !== ''
        ? (new finfo(FILEINFO_MIME_TYPE))->file($temporary_path)
        : false;

    if (!$mime_type || !isset($allowed_types[$mime_type]) || @getimagesize($temporary_path) === false) {
        $error = 'Upload a valid JPG, PNG, or WebP image.';
        return false;
    }

    $upload_directory = __DIR__ . '/../images/restaurants';

    if (!is_dir($upload_directory) && !mkdir($upload_directory, 0755, true)) {
        $error = 'The image folder could not be created.';
        return false;
    }

    $filename = 'blind-box-' . bin2hex(random_bytes(16)) . '.' . $allowed_types[$mime_type];
    $destination = $upload_directory . '/' . $filename;

    if (!move_uploaded_file($temporary_path, $destination)) {
        $error = 'The image could not be saved. Please try again.';
        return false;
    }

    return $filename;
}

function delete_restaurant_image(?string $filename): void
{
    if (!$filename || basename($filename) !== $filename) {
        return;
    }

    $image_path = __DIR__ . '/../images/restaurants/' . $filename;

    if (is_file($image_path)) {
        unlink($image_path);
    }
}

?>
