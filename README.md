# PHP CSRF Protection Example


 * URI unique CSRF token.
 * CSRF token expiration.
 * Automatic CSRF protection without code modification. i.e. CSRF unprotected PHP app can be protected w/o code modification.

 ## How to use

 Simply include 'csrf_init.php'.

```php
require_once('/path/to/csrf_init.php');
```

## How it behaves

Cryptographically strong CSRF token is generated for each URI automatically.

 * It automatically start session if session is not active.
 * It automatically add CSRF token to web page output.
 * If $GLOBAL['DISABLE_CSRF'] is defined and true, it disables CSRF protection.


Have fun!
