<?php


namespace esas\cmsgate\tilda\service;


use esas\cmsgate\epos\ConfigFieldsEpos;
use esas\cmsgate\tilda\security\CmsAuthServiceTilda;

class CmsAuthServiceTildaEpos extends CmsAuthServiceTilda
{
    public function getRequestFieldLogin() {
        return ConfigFieldsEpos::iiiClientId();
    }
}