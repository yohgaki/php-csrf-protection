<?php
/**
 * Sample CSRF protection script
 *
 * Simply include this file to add CSRF protection for all pages.
 * Function is not used intentionally to keep namespace clean.
 */
require_once(__DIR__.'/csrf_protection.php');

assert(is_null($GLOBALS['_CSRF_DISABLE_']) || is_bool($GLOBALS['_CSRF_DISABLE_']));
assert(is_null($GLOBALS['_CSRF_EXPIRE_']) || is_int($GLOBALS['_CSRF_EXPIRE_']));
assert(is_null($GLOBALS['_CSRF_RENEW_']) || is_int($GLOBALS['_CSRF_RENEW_']));
assert(is_null($GLOBALS['_CSRF_SESSION_']) || is_bool($GLOBALS['_CSRF_SESSION_']));

// Set these globals to adjust settings
// I choose to pollute $GLOBALS, you may choose whatever namespace
// to pollute. e.g. Constant.
$GLOBALS['_CSRF_DISABLE_'] = $GLOBALS['_CSRF_DISABLE_'] ?? false;
$GLOBALS['_CSRF_EXPIRE_']  = $GLOBALS['_CSRF_EXPIRE_'] ?? 300;
$GLOBALS['_CSRF_RENEW_']   = $GLOBALS['_CSRF_RENEW_'] ?? 60;
$GLOBALS['_CSRF_SESSION_'] = $GLOBALS['_CSRF_SESSION_'] ?? true;

if (!empty($GLOBALS['_CSRF_DISABLE_'])) {
    return;
}
if ($GLOBALS['_CSRF_SESSION_'] && session_status() !== PHP_SESSION_ACTIVE) {
    $orig_name = session_name('CSRFTK');
    session_start();
}

$_SESSION['CSRF_SECRET'] = $_SESSION['CSRF_SECRET'] ?? random_bytes(32);

$csrftk = $_POST['csrftk'] ?? $_GET['csrftk'] ?? '';
// WARNING: csrf_validate_token() returns TRUE or error message. Never do if ($valid)
$valid = csrf_validate_token($_SESSION['CSRF_SECRET'], $csrftk);
if ($valid !== true) {
    $token = csrf_generate_token($_SESSION['CSRF_SECRET'], $GLOBALS['_CSRF_EXPIRE_']);
    output_add_rewrite_var('csrftk', $token);
    throw new RuntimeException('CSRF Token validation error: '. $valid);
}

list($salt, $key, $expire) = explode('-', $csrftk);
if ($expire < time() + $GLOBALS['_CSRF_RENEW_']) {
    $csrftk = csrf_generate_token($_SESSION['CSRF_SECRET'], $GLOBALS['_CSRF_EXPIRE_']);
}
output_add_rewrite_var('csrftk', $csrftk);

if (!empty($orig_name)) {
    // Session is started by this code. Cleanup.
    session_commit();
    session_name($orig_name);
    unset($_SESSION);
}
