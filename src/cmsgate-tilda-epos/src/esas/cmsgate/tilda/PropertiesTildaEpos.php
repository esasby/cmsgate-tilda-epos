<?php


namespace esas\cmsgate\tilda;


use esas\cmsgate\properties\ViewProperties;
use esas\cmsgate\tilda\properties\PropertiesTilda;

class PropertiesTildaEpos extends PropertiesTilda
{
    public function getPDO_DSN() {
        return "mysql:host=127.0.0.1;dbname=database;charset=utf8";
    }

    public function getPDOUsername() {
        return 'username';
    }

    public function getPDOPassword() {
        return 'password';
    }

    public function isSandbox() {
        return true;
    }

    public function getStorageDir() {
        return "/opt/cmsgate/storage";
    }

    public function getBootstrapVersion() {
        return ViewProperties::BOOTSTRAP_V5;
    }

    public function getDefaultClientUICssLink() {
        return "https://cmsgate-test.esas.by/cmsgate-tilda-epos/static/default.css";
    }
}