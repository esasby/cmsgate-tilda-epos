<?php
use esas\cmsgate\CmsPluginCloud;
use esas\cmsgate\epos\CloudRegistryEposTilda;
use esas\cmsgate\epos\RegistryEposTilda;

if (!class_exists("esas\cmsgate\CmsPluginCloud")) {
    require_once(dirname(dirname(__FILE__)) . '/vendor/esas/cmsgate-cloud-lib/src/esas/cmsgate/CmsPluginCloud.php');

    (new CmsPluginCloud(dirname(dirname(__FILE__)) . '/vendor', dirname(__FILE__)))
        ->setRegistry(new RegistryEposTilda())
        ->setCloudRegistry(new CloudRegistryEposTilda())
        ->init();

}

