<?php
define('APP_NAME', 'HelpDesk Pro');
define('APP_URL',  'http://localhost/helpdesk');

define('DB_HOST', 'localhost');
define('DB_PORT', '3307');      
define('DB_NAME', 'helpdesk');
define('DB_USER', 'root');
define('DB_PASS', '');

define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('UPLOAD_URL',  APP_URL . '/uploads/');
define('MAX_FILE_SIZE', 10 * 1024 * 1024); 
define('ALLOWED_EXTENSIONS', ['jpg','jpeg','png','gif','pdf','doc','docx','xls','xlsx','zip','txt','csv']);

define('SESSION_TIMEOUT', 3600); 

date_default_timezone_set('America/Sao_Paulo');

define('COOKIE_SECRET', 'h3lpd3sk_s3cr3t_k3y_2025_@UP');
