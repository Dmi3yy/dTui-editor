# CSRF upload regression checks

Run the browser upload request checks with Node.js 18+:

```sh
node --test tests/image-upload-csrf.js
```

Run the registered upload route through Evolution's real CSRF middleware and
Illuminate router with PHP 8.3+ and an Evolution CMS 3.5.8+ checkout:

```sh
php tests/image-upload-csrf.php /path/to/evolution/core
```

If that checkout has no `vendor/`, point `EVO_AUTOLOAD` at a compatible Evolution
Composer autoloader. These checks use an isolated container and temporary upload
directory; they do not boot the CMS or connect to its database.

The PHP checks cover missing, mismatched, malformed and valid tokens, body/header
token precedence, anonymous requests, upload permissions, and successful PNG
storage. Forged requests must be rejected before an upload directory is created.
