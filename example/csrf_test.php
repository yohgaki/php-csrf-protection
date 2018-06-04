<?php
try {
    // Update url_rewriter.tags to enable href rewrite.
    ini_set('url_rewriter.tags', 'form=,a=href');
    // Set up config values
    $GLOBALS['_CSRF_DISABLE_'] = false;
    $GLOBALS['_CSRF_EXPIRE_']  = 60; // 60 sec expiration
    $GLOBALS['_CSRF_RENEW_']   = 55; // 55 sec renewal before expiration
    $GLOBALS['_CSRF_SESSION_'] = true; // Use dedicated session for CSRF
    $GLOBALS['_CSRF_BLACKLIST_'] = ['delete', 'add', 'edit']; // Set dangerous GET vars.
    require_once(__DIR__.'/../src/csrf_init.php');
} catch (Exception $e) {
    // Show nice CSRF token error message for production use.
    echo '<a href="'.  csrf_get_uri() .'">Click here to return page<a><br />';
    echo 'If this is not an access you intended, DO NOT CLICK above link!<br />';
    echo $e->getMessage();
    exit;
}
?>
<html>
<head><title>CSRF Test</title></head>
<body>
<form method='post'>
<h1> CSRF protection example</h1>
    <div style="width: 300px;text-align: left;margin: 1em;">
    <p>
     CSRF Test
    </p>
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
</body>
</html>
