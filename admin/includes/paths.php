<?php

/**
 * Build a URL path to a file in the admin folder (works with subfolder installs like /ielts/admin/).
 */
function admin_asset_url($file)
{
    $script = isset($_SERVER['SCRIPT_NAME']) ? (string) $_SERVER['SCRIPT_NAME'] : '';
    $dir = dirname($script);
    $dir = str_replace('\\', '/', $dir);
    $dir = rtrim($dir, '/');
    $file = ltrim($file, '/');
    if ($dir === '' || $dir === '.' || $dir === '/') {
        return $file;
    }

    return $dir . '/' . $file;
}
