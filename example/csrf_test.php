<?php
try {
    // Update url_rewriter.tags to enable href rewrite.
    ini_set('url_rewriter.tags', 'form=,a=href');
    // Set up config values
    $GLOBALS['_CSRF_DISABLE_'] = false;
    $GLOBALS['_CSRF_EXPIRE_']  = 15; // 15 sec expiration
    $GLOBALS['_CSRF_RENEW_']   = 10; // 10 sec renewal before expiration
    $GLOBALS['_CSRF_SESSION_'] = true; // Use dedicated session for CSRF
    $blacklist = ['delete', 'add', 'edit']; // Set dangerous GET vars.
    // Whitelist is better. Use https://github.com/yohgaki/validate-php-scr
    require_once(__DIR__.'/../src/csrf_init.php');
} catch (Exception $e) {
    // Show nice CSRF token error message for production use.
    if (empty($_POST)) {
        echo '
<html><head></head><body>
<div style="position: absolute; top: 0; right: 0; bottom: 0; left: 0; margin: auto; width: 80%; height: 30rem;">
<h1>'. htmlspecialchars($e->getMessage()). '</h1>
<a href="'.  csrf_get_uri($blacklist) .'">Click here to return page<a><br />
<br />
<b>If this is not an access you intended, DO NOT CLICK above link!</b><br />
<br />
Return to <a href="index.php">home</a>. <br />
</div>
</body></html>
';
    } else {
        echo '
<html><head></head><body>
<div style="position: absolute; top: 0; right: 0; bottom: 0; left: 0; margin: auto; width: 80%; height: 30rem;">
<h1>'. htmlspecialchars($e->getMessage()). '</h1>
<div>
'. csrf_get_form() .'
</div>
<a href="'. csrf_get_uri($blacklist) .'">Click here to return page<a><br />
<br />
<b>If this is not an access you intended, DO NOT CLICK above button nor link!</b><br />
<br />
Return to <a href="index.php">home</a>. <br />
</div>
</body></html>
';
    }
    exit;
}
?>
<html>
<head><title>CSRF Test</title></head>
<body>
<div style="position: absolute; top: 0; right: 0; bottom: 0; left: 0; margin: auto; width: 80%; height: 30rem;">
    <form method='post'>
    <h1> CSRF protection example</h1>
        <div>
        <p>CSRF Test</p>
        </div>
        <div>
        <ul>
            <li><div>Username: </div><input type="text" name="username" value="<?php echo htmlspecialchars($_POST['username'] ?? 'Test User');?>" /></li>
            <li><div>Email: </div><input type="text" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? 'user@example.com');?>" /></li>
            <li><div>Send These!</div><input type="submit" name="submit" value="submit" /></li>
        </ul>
        </div>
    </form>
    <div>
        <p><a href='<?php echo csrf_get_uri(); ?>'>TEST LINK</a></p>
    </div>
    <div>
        CSRF Expire: <?php echo htmlspecialchars($GLOBALS['_CSRF_EXPIRE_']); ?> sec.
    </div>
    <div>
        CSRF Renew: <?php echo htmlspecialchars($GLOBALS['_CSRF_RENEW_']); ?> sec before expiration.
    </div>
    <div>
        CSRF Token: <?php echo htmlspecialchars($_POST['csrftk'] ?? ($_GET['csrftk'] ?? '')); ?>
    </div>
</div>
</body>
</html>
