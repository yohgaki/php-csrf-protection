<?php
/**
 * Sample CSRF protection script
 *
 * Simply include this file to add CSRF protection for all pages.
 */
require_once(__DIR__.'/csrf_protection.php');

if (!empty($GLOBAL['CSRF_DISABLE'])) {
    return;
}
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$_SESSION['CSRF_SECRET'] = $_SESSION['CSRF_SECRET'] ?? random_bytes(32);
$csrf_token = $_POST['csrftk'] ?? ($_GET['csrftk'] ?? '');

$keys = json_decode(base64_decode($csrf_token), true);
if (empty($keys['expire']) || $keys['expire'] < time() - 900) {
    $csrf_token_new = csrf_generate_token($_SESSION['CSRF_SECRET']);
}
output_add_rewrite_var('csrftk', $csrf_token_new ?? $csrf_token);

if (!$csrf_token) {
    throw new RuntimeException('CSRF Token validation error: No token');
}

$ret = csrf_validate_token($_SESSION['CSRF_SECRET'], $csrf_token);
if ($ret !== true) {
    throw new RuntimeException('CSRF Token validation error: '. $ret);
}
