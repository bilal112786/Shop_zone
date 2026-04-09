<?php

/**
 * Web path from site root (e.g. assets/images/file.jpg) for product images.
 */
function product_image_url($filename)
{
    $fn = basename((string) $filename);
    if ($fn === '') {
        return 'assets/images/';
    }
    $root = dirname(__DIR__);
    $candidates = [
        $root . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . $fn,
        $root . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $fn,
        $root . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'imgs' . DIRECTORY_SEPARATOR . $fn,
    ];
    foreach ($candidates as $path) {
        if (is_file($path)) {
            if (strpos($path, DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR) !== false) {
                return 'uploads/' . $fn;
            }
            if (strpos($path, DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'imgs' . DIRECTORY_SEPARATOR) !== false) {
                return 'assets/imgs/' . $fn;
            }

            return 'assets/images/' . $fn;
        }
    }

    return 'assets/images/' . $fn;
}

/**
 * Relative path for <img> inside /admin/ pages.
 */
function product_image_url_from_admin($filename)
{
    return '../' . product_image_url($filename);
}

/**
 * Remove image file from every known product folder.
 */
function product_image_unlink_all($filename)
{
    $fn = basename((string) $filename);
    if ($fn === '') {
        return;
    }
    $root = dirname(__DIR__);
    foreach (['assets/images', 'uploads', 'assets/imgs'] as $sub) {
        $p = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $sub) . DIRECTORY_SEPARATOR . $fn;
        if (is_file($p)) {
            @unlink($p);
        }
    }
}
