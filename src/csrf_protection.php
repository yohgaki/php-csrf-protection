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
    $salt = bin2hex(random_bytes(32));
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

    if ($token === '') {
        return 'No token';
    }
    if (!is_string($token)) {
        return 'Attack - Non-string token';
    }
    $uri = @parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
    $tmp = explode("-", $token);
    if (count($tmp) !== 3) {
        return 'Atatck - Invalid token';
    }
    list($salt, $key, $expire) = $tmp;
    if (empty($salt) || empty($key) || empty($expire)) {
        return 'Attack - Invalid token';
    }
    if (strlen($expire) != strspn($expire, '1234567890')) {
        return 'Attack - Invalid expire';
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


/**
 * Utility function that returns "csrftk" removed current URI.
 *
 * @return string URI string
 */
function csrf_get_uri()
{
    $q = $_GET; unset($q['csrftk']);
    $q = http_build_query($q);
    $p = parse_url(($_SERVER['REQUEST_URI'] ?? ''));

    $uri = '';
    if (!empty($p['host'])) {
        $uri = '//'. $p['host'];
    }
    if (!empty($p['user'])) {
        $uri .= ':'. $p['user'];
    }
    if (!empty($p['pass'])) {
        $uri .= '@'. $p['pass'];
    }
    if (!empty($p['port'])) {
        $uri .= ':'. $p['port'];
    }
    if ($q) {
        $uri .= $p['path'] .'?'. $q;
    } else {
        $uri .= $p['path'];
    }
    if (!empty($p['fragment'])) {
        $uri .= '#'. $p['fragment'];
    }

    return $uri;
}