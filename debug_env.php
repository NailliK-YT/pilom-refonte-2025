<?php

require 'vendor/autoload.php';

use CodeIgniter\Config\DotEnv;

$env = new DotEnv(__DIR__);
$env->load();

echo "Hostname: (" . gettype(getenv('database.default.hostname')) . ") " . var_export(getenv('database.default.hostname'), true) . "\n";
echo "Username: (" . gettype(getenv('database.default.username')) . ") " . var_export(getenv('database.default.username'), true) . "\n";
echo "Password: (" . gettype(getenv('database.default.password')) . ") " . var_export(getenv('database.default.password'), true) . "\n";
echo "Driver: (" . gettype(getenv('database.default.DBDriver')) . ") " . var_export(getenv('database.default.DBDriver'), true) . "\n";
