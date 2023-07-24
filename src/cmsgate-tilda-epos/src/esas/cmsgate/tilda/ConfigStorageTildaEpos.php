<?php


namespace esas\cmsgate\tilda;


use esas\cmsgate\epos\ConfigFieldsEpos;

class ConfigStorageTildaEpos extends ConfigStorageTilda
{
    public function getConfigFieldLogin() {
        return ConfigFieldsEpos::iiiClientId();
    }

    public function getConfigFieldPassword() {
        return ConfigFieldsEpos::iiiClientSecret();
    }
}