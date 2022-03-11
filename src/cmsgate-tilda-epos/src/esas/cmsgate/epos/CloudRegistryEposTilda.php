<?php


namespace esas\cmsgate\epos;


use esas\cmsgate\CloudRegistryPDO;
use esas\cmsgate\security\ApiAuthServiceTilda;
use esas\cmsgate\security\CryptServiceImpl;
use esas\cmsgate\tilda\RequestParamsTilda;
use esas\cmsgate\view\admin\AdminConfigPage;
use esas\cmsgate\view\admin\AdminLoginPage;
use esas\cmsgate\security\AuthConfigMapper;
use PDO;

class CloudRegistryEposTilda extends CloudRegistryPDO
{
    public function getPDO()
    {
        $opt = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        return new PDO(
            "mysql:host=127.0.0.1;dbname=cmsgate;charset=utf8",
            'username',
            'password',
            $opt);
    }

    protected function createApiAuthService()
    {
        return new ApiAuthServiceTilda(
            RequestParamsTilda::SIGNATURE);
    }

    public function createAdminConfigPage()
    {
        return new AdminConfigPage();
    }

    public function createAdminLoginPage()
    {
        return new AdminLoginPage();
    }

    public function isSandbox()
    {
        return true;
    }

    protected function createCryptService()
    {
        return new CryptServiceImpl('/opt/cmsgate/storage');
    }


    public function createAuthConfigMapper()
    {
        return new AuthConfigMapper(
            ConfigFieldsEpos::iiiClientId(),
            ConfigFieldsEpos::iiiClientSecret());
    }
}