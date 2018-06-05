<?php
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
<form method='post'>
<h1> CSRF protection example</h1>
    <div style="width: 300px;text-align: left;margin: 1em;">
    <p>
     Go to <a href="csrf_test.php">test page</a>.
    </p>
    </div>
</body>
</html>