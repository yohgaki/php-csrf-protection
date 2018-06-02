# PHP CSRF Protection Example

* URI unique CSRF token.
* CSRF token expiration.
* Automatic CSRF protection without code modification. i.e. CSRF unprotected PHP app can be protected w/o code modification.
* Recommended: PHP 7.1 or up.

## How it behaves

Cryptographicaly strong CSRF tokens with expiration are generated for each URI automatically.

* It automatically start session if session is not active.
* It automatically add CSRF token to web page output.
* CSRF token expires with configured expiration.
* CSRF token is updated with configured duration.
* FORMs will have hidden 'csrftk' input automatically.
* URLs for your site will have 'csrftk' parameter automatically.
* When CSRF validation is failed, it raises RuntimeException.
* If $GLOBAL['CSRF_DISABLE'] is defined and true, it disables CSRF protection.

## How to test drive

Clone repository and change directory to 'php-csrf-protection/example', then start PHP CLI server like;

```
$ php -S 127.0.0.1:8888
```

Access test script.

http://127.0.0.1:8888/csrf_test.php

First access will raise CSRF exception. Press "Click here to return page" link and there are tokens.

Live test script is here:

https://sample.ohgaki.net/php-csrf-protection/example/csrf_test.php


## FAQ

### HTML links do not have CSRF tokens

Since PHP 7.1.0, output rewriter uses its own output buffer. output_rewrite_var() only rewrites HTML form. To add CSRF token value to HTML links, change ini setting as follows;

```php
ini_set('url_rewriter.tags', 'form=,a=href');
```

### CSRF token is not added to page

This script uses output buffer. Therefore outputs already sent cannot be rewritten. Include 'csrf_init.php' before you start output anything.

Have fun!!
