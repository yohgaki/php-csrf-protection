<?php
require_once(__DIR__.'/csrf_protection.php');

if (!empty($GLOBAL['DISABLE_CSRF'])) {
    return;
}
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$_SESSION['CSRF_SECRET'] = $_SESSION['CSRF_SECRET'] ?? random_bytes(32);
$csrf_token = $_POST['csrftk'] ?? ($_GET['csrftk'] ?? '');

if ($csrf_token) {
    $ret = csrf_validate_token($_SESSION['CSRF_SECRET'], $csrf_token);
    if ($ret !== true) {
        throw new RuntimeException('CSRF Token validation error: '. $ret);
    }
} else {
    $csrf_token = csrf_generate_token($_SESSION['CSRF_SECRET']);
}
output_add_rewrite_var('csrftk', $csrf_token);
