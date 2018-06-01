# PHP CSRF Protection Example

* URI unique CSRF token.
* CSRF token expiration.
* Automatic CSRF protection without code modification. i.e. CSRF unprotected PHP app can be protected w/o code modification.
* Recommended: PHP 7.1 or up.

## How to use

 Simply include 'csrf_init.php'.

```php
require_once('/path/to/csrf_init.php');
```

This script requires PHP 7.0 or up.

## How it behaves

Cryptographicaly strong CSRF token is generated for each URI automatically.

* It automatically start session if session is not active.
* It automatically add CSRF token to web page output.
* CSRF token is expired after 1800 seconds by default.
* URLs for your site have 'csrftk' parameter and protected.
* When CSRF validation is failed, it raises RuntimeException.
* If $GLOBAL['DISABLE_CSRF'] is defined and true, it disables CSRF protection.

## How to test drive

Clone repository and change directory to 'example', then start PHP CLI server like;

```
$ php -S 127.0.0.1:888
```

Access test script and submit page.

http://127.0.0.1:8888/csrf_test.php

## FAQ

### HTML links do not have CSRF tokens

Since PHP 7.1.0, output rewriter uses its own output buffer. output_rewrite_var() only rewrites HTML form. To add CSRF token value to HTML links, change ini setting as follows;

```php
ini_set('url_rewriter.tags', 'form=,a=href');
```

### CSRF token is not added to page

This script uses output buffer. Therefore outputs already sent cannot be rewritten. Include 'csrf_init.php' before you start output anything.

Have fun!!
