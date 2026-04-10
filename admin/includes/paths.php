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

/**
 * URL to the storefront (parent folder of admin), e.g. /ielts/index.php
 */
function storefront_url($file = 'index.php')
{
    $script = isset($_SERVER['SCRIPT_NAME']) ? (string) $_SERVER['SCRIPT_NAME'] : '';
    $adminDir = dirname($script);
    $adminDir = str_replace('\\', '/', $adminDir);
    $root = dirname($adminDir);
    $file = ltrim(str_replace('\\', '/', $file), '/');
    if ($root === '/' || $root === '.' || $root === '') {
        return '/' . $file;
    }
    return rtrim($root, '/') . '/' . $file;
}
