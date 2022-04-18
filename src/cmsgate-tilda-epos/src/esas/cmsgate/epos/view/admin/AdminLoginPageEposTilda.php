<?php


namespace esas\cmsgate\epos\view\admin;


use esas\cmsgate\view\admin\AdminLoginPage;

class AdminLoginPageEposTilda extends AdminLoginPage
{
    protected function getLoginPlaceholder() {
        return "Client ID";
    }

    protected function getPasswordPlaceholder() {
        return "Secret";
    }
}