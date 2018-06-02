<?php
try {
    // Update url_rewriter.tags to enable href rewrite.
    ini_set('url_rewriter.tags', 'form=,a=href');
    require_once(__DIR__.'/../src/csrf_init.php');
} catch (Exception $e) {
    unset($_GET['csrftk']);
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
        <li><div>Age: </div><input type="text" name="age" value="<?php echo htmlspecialchars($_POST['age'] ?? 34);?>"  /></li>
        <li><div>Weight: </div><input type="text" name="weight" value="<?php echo htmlspecialchars($_POST['weight'] ?? '1234');?>"  /></li>
        <li><div>Country: </div>
        <input type="radio" name="country" value="japan" <?php if (isset($_POST['country']) && $_POST['country']=='japan') echo 'checked="checked"'; ?> />Japan<br />
        <input type="radio" name="country" value="other" <?php if (isset($_POST['country']) && $_POST['country']=='other') echo 'checked="checked"'; ?> />Other<br /></li>
        <li><div>Comment: </div><textarea name="comment"><?php echo htmlspecialchars($_POST['comment'] ?? 'Write comment');?></textarea>
        <li><div>Send These!</div><input type="submit" name="submit" value="submit" /></li>
    </ul>
    </div>
</form>
<div>
    <a href='csrf_test.php'>TEST LINK</a>
</div>
</body>
</html>
