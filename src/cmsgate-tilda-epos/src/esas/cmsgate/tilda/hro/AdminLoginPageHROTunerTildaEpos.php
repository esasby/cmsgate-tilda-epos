<?php


namespace esas\cmsgate\tilda\hro;


use esas\cmsgate\bridge\view\client\RequestParamsBridge;
use esas\cmsgate\Registry;
use esas\cmsgate\hro\HRO;
use esas\cmsgate\hro\HROTuner;
use esas\cmsgate\hro\pages\AdminLoginPageHRO;
use esas\cmsgate\tilda\PropertiesTildaEpos;

class AdminLoginPageHROTunerTildaEpos implements HROTuner
{
    /**
     * @param AdminLoginPageHRO $hroBuilder
     * @return HRO|void
     */
    public function tune($hroBuilder) {
        return $hroBuilder
            ->setLoginField(RequestParamsBridge::LOGIN_FORM_LOGIN, "Client ID")
            ->setPasswordField(RequestParamsBridge::LOGIN_FORM_PASSWORD, 'Secret')
            ->setSandbox(PropertiesTildaEpos::fromRegistry()->isSandbox())
            ->setMessage("Login to Tilda " . Registry::getRegistry()->getPaysystemConnector()->getPaySystemConnectorDescriptor()->getPaySystemMachinaName());
    }
}