<?php
/**
 * Sample CSRF protection script
 *
 * Simply include this file to add CSRF protection for all pages.
 */
require_once(__DIR__.'/csrf_protection.php');

// Set these globals to adjust settings
$GLOBAL['_CSRF_DISABLE_'] = $GLOBAL['_CSRF_DISABLE_'] ?? false;
$GLOBAL['_CSRF_EXPIRE_']  = $GLOBAL['_CSRF_EXPIRE_'] ?? 300;
$GLOBAL['_CSRF_RENEW_']   = $GLOBAL['_CSRF_RENEW_'] ?? 60;

if (!empty($GLOBAL['_CSRF_DISABLE_'])) {
    return;
}
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$_SESSION['CSRF_SECRET'] = $_SESSION['CSRF_SECRET'] ?? random_bytes(32);
$csrf_token = $_POST['csrftk'] ?? ($_GET['csrftk'] ?? '');

$valid = csrf_validate_token($_SESSION['CSRF_SECRET'], $csrf_token);
$keys  = json_decode(base64_decode($csrf_token), true);
if ($valid !== true
    || empty($keys['expire'])
    || $keys['expire'] < time() + $GLOBAL['_CSRF_RENEW_']) {
    $csrf_token_new = csrf_generate_token($_SESSION['CSRF_SECRET'], $GLOBAL['_CSRF_EXPIRE_']);
}
output_add_rewrite_var('csrftk', $csrf_token_new ?? $csrf_token);

if (!$csrf_token) {
    throw new RuntimeException('CSRF Token validation error: No token');
}
if ($valid !== true) {
    throw new RuntimeException('CSRF Token validation error: '. $valid);
}
