<?php
try {
    // Update url_rewriter.tags to enable href rewrite.
    ini_set('url_rewriter.tags', 'form=,a=href');
    require_once(__DIR__.'/../src/csrf_init.php');
} catch (Exception $e) {
    // Show nice CSRF token error message for production use.
    unset($_GET['csrftk']); // Remove "csrftk" to avoid multiple values
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH). '?' .http_build_query($_GET);
    echo '<a href="'.  $uri .'">Click here to return page<a><br />';
    echo 'You have sent invalid or expired request.';
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
    </ul>
    </div>
</form>
<div>
    <a href='csrf_test.php'>TEST LINK</a>
</div>
</body>
</html>
