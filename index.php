<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Mount Heaven English School — application entry point (shim)
|--------------------------------------------------------------------------
| The real front controller is public/index.php. This root file exists so
| the site works no matter how the web server is pointed at the project:
|   - docroot = project root (XAMPP sub-folder setups)
|   - docroot = public/ (recommended hosting setup; Apache rewrites
|     unmatched requests to ../index.php which lands here)
| Nothing else in the project needs to be web-accessible.
*/

require __DIR__ . '/public/index.php';
