<?php
// TEST & Usage
// $secret = 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';
// var_dump( $tk = csrf_generate_token($secret, 60, 'aaa') );
// var_dump(csrf_validate_token($secret, $tk, 'aaa'));


/**
 * Generate secure CSRF token
 *
 * @param string $secret Random secret string for key derivation.
 * @param int    $expire Expiration in seconds.
 * @param string $uri    URI for this request.
 *
 * @return string CSRF token.
 */
function csrf_generate_token($secret, $expire = 300)
{
    assert(is_string($secret) && strlen($secret) >= 32);
    assert(is_int($expire) && $expire >= 60);

    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
    $salt = bin2hex(random_bytes(33));
    $expire += time();
    $key = bin2hex(hash_hkdf('sha256', $secret, 0, $uri."\0".$expire, $salt));
    $token = join("-", [$salt, $key, $expire]);
    assert(strlen($token) > 32);
    return $token;
}


/**
 * Validate CSRF token
 *
 * @param string $secret Random secret string for key derivation.
 * @param int    $expire Expiration in seconds.
 * @param string $uri    URI for this request.
 *
 * @return bool or string  Returns TRUE for success, string error message for errors.
 */
function csrf_validate_token($secret, $token)
{
    assert(is_string($secret) && strlen($secret) >= 32);
    assert(is_string($token) && strlen($token) >= 32);

    $uri = @parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
    $tmp = explode("-", $token);
    if (count($tmp) !== 3) {
        return 'Atatck - Invalid token';
    }
    list($salt, $key, $expire) = $tmp;
    if (empty($salt) || empty($key) || empty($expire)) {
        return 'Attack - Invalid token';
    }
    if ($expire < time()) {
        return 'Expired';
    }
    $key2 = bin2hex(hash_hkdf('sha256', $secret, 0, $uri."\0".$expire, $salt));
    if (hash_equals($key, $key2) === false) {
        return 'Attack - Key mismatch';
    }
    return true;
}
