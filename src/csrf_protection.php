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
    $salt = base64_encode(random_bytes(33));
    $expire += time();
    $key  = base64_encode(hash_hkdf('sha256', $secret, 0, $uri."\0".$expire, $salt));
    $json = json_encode(['salt' => $salt, 'key' => $key, 'expire' => $expire]);
    $token = base64_encode($json);
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
    $keys = json_decode(base64_decode($token), true);
    if (empty($keys) || empty($keys['salt']) || empty($keys['key'] || empty($keys['expire']))) {
        return 'Invalid token';
    }
    if ($keys['expire'] < time()) {
        return 'Expired';
    }
    $key = base64_encode(hash_hkdf('sha256', $secret, 0, $uri."\0".$keys['expire'], $keys['salt']));
    if (hash_equals($key, $keys['key']) === false) {
        return 'Key mismatch';
    }
    return true;
}
