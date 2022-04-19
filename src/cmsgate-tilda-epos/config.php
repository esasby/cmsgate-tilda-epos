<?php
if(!defined('read_config')) {
    die('Direct access not permitted');
}

const CONFIG_PDO_DSN = "pdo_dsn";
const CONFIG_PDO_USERNAME = 'pdo_username';
const CONFIG_PDO_PASSWORD = 'pdo_password';
const CONFIG_SANDBOX = 'sandbox';

return array(
    CONFIG_PDO_DSN => "mysql:host=127.0.0.1;dbname=cmsgate;charset=utf8",
    CONFIG_PDO_USERNAME => 'username',
    CONFIG_PDO_PASSWORD   => 'password',
    CONFIG_SANDBOX   => true,
);