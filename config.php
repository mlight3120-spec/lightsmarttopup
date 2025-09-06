<?php
return [
    'BASE_URL' => getenv('BASE_URL') ?: 'http://localhost/',
    'DB_HOST' => getenv('DB_HOST') ?: '127.0.0.1',
    'DB_PORT' => getenv('DB_PORT') ?: '3306',
    'DB_NAME' => getenv('DB_NAME') ?: 'lightsmart',
    'DB_USER' => getenv('DB_USER') ?: 'root',
    'DB_PASS' => getenv('DB_PASS') ?: '',
    'APP_KEY' => getenv('APP_KEY') ?: 'randomkey123',
    'PAYSTACK_PUBLIC' => getenv('PAYSTACK_PUBLIC') ?: '',
    'PAYSTACK_SECRET' => getenv('PAYSTACK_SECRET') ?: '',
    'FUNDING_COMMISSION_FIXED' => getenv('FUNDING_COMMISSION_FIXED') ?: 50,
    'PROVIDER' => getenv('PROVIDER') ?: 'mock'
];
?>
