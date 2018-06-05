<?php
// If you would like to avoid CSRF token error for landing page,
// you need to initialize CSRF token manually.
session_name('CSRFTK');
session_start();
require_once(__DIR__.'/../src/csrf_protection.php');
// Update url_rewriter.tags to enable href rewrite.
ini_set('url_rewriter.tags', 'form=,a=href');
$token = csrf_generate_token($_SESSION['CSRF_SECRET'], 15);
output_add_rewrite_var('csrftk', $token);
?>
<html>
<head><title>CSRF Test</title></head>
<body>
<div style="position: absolute; top: 0; right: 0; bottom: 0; left: 0; margin: auto; width: 80%; height: 30rem;">
<h1> CSRF protection example</h1>
<div>
<p>
Go to <a href="csrf_test.php">test page</a>.
</p>
</div>
</div>
</body>
</html>