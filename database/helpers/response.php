<?php
// Bridge file — loads all helpers from api/helpers/
require_once __DIR__ . '/../api/helpers/response.php';
require_once __DIR__ . '/../api/helpers/validator.php';
if (file_exists(__DIR__ . '/../api/helpers/auth.php'))          require_once __DIR__ . '/../api/helpers/auth.php';
if (file_exists(__DIR__ . '/../api/helpers/mailer.php'))        require_once __DIR__ . '/../api/helpers/mailer.php';
if (file_exists(__DIR__ . '/../api/helpers/notifications.php')) require_once __DIR__ . '/../api/helpers/notifications.php';
