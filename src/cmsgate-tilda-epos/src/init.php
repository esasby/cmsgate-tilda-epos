<?php
use esas\cmsgate\CmsPlugin;
use esas\cmsgate\tilda\RegistryEposTilda;

if (!class_exists("esas\cmsgate\CmsPlugin")) {
    require_once(dirname(dirname(__FILE__)) . '/vendor/esas/cmsgate-core/src/esas/cmsgate/CmsPlugin.php');

    (new CmsPlugin(dirname(dirname(__FILE__)) . '/vendor', dirname(__FILE__)))
        ->setRegistry(new RegistryEposTilda())
        ->init();

}

