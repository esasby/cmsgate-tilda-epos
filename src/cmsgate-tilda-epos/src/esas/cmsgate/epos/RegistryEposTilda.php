<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 01.10.2018
 * Time: 12:05
 */

namespace esas\cmsgate\epos;

use esas\cmsgate\CmsConnectorTilda;
use esas\cmsgate\descriptors\ModuleDescriptor;
use esas\cmsgate\descriptors\VendorDescriptor;
use esas\cmsgate\descriptors\VersionDescriptor;
use esas\cmsgate\epos\view\client\CompletionPageEpos;
use esas\cmsgate\epos\view\client\CompletionPanelEposTilda;
use esas\cmsgate\tilda\RequestParamsTilda;
use esas\cmsgate\utils\CloudSessionUtils;
use esas\cmsgate\utils\CMSGateException;
use esas\cmsgate\utils\URLUtils;
use esas\cmsgate\view\admin\AdminViewFields;
use esas\cmsgate\view\admin\ConfigFormCloud;
use Exception;

class RegistryEposTilda extends RegistryEpos
{
    public function __construct()
    {
        $this->cmsConnector = new CmsConnectorTilda();
        $this->paysystemConnector = new PaysystemConnectorEpos();
    }


    /**
     * Переопределение для упрощения типизации
     * @return RegistryEposTilda
     */
    public static function getRegistry()
    {
        return parent::getRegistry();
    }

    /**
     * @throws \Exception
     */
    public function createConfigForm()
    {
        $managedFields = $this->getManagedFieldsFactory()->getManagedFieldsOnly(AdminViewFields::CONFIG_FORM_COMMON, [
            ConfigFieldsEpos::eposProcessor(),
            ConfigFieldsEpos::eposServiceProviderCode(),
            ConfigFieldsEpos::eposServiceCode(),
            ConfigFieldsEpos::eposRetailOutletCode(),
            ConfigFieldsEpos::dueInterval(),
            ConfigFieldsEpos::completionText(),
            ConfigFieldsEpos::instructionsSection(),
            ConfigFieldsEpos::qrcodeSection(),
            ConfigFieldsEpos::webpaySection(),
        ]);
        $configForm = new ConfigFormCloud(
            $managedFields,
            AdminViewFields::CONFIG_FORM_COMMON,
            null,
            ''
        );
        return $configForm;
    }


    function getUrlWebpay($orderWrapper)
    {
        $currentURL = URLUtils::getCurrentURLNoParams();
        $currentURL = str_replace(PATH_INVOICE_ADD, PATH_INVOICE_VIEW, $currentURL);
        if (strpos($currentURL, PATH_INVOICE_VIEW) !== false)
            return $currentURL . '?' . RequestParamsTilda::ORDER_ID . '=' . CloudSessionUtils::getOrderCacheUUID();
        else
            throw new CMSGateException('Incorrect URL genearation');
    }

    public function createModuleDescriptor()
    {
        return new ModuleDescriptor(
            "commerce-tilda-epos", // код должен совпадать с кодом решения в маркете (@id в Plugin\Commerce\PaymentGateway\xxx.php)
            new VersionDescriptor("1.17.1", "2022-03-28"),
            "Tilda EPOS",
            "https://bitbucket.org/esasby/cmsgate-tilda-epos/src/master/",
            VendorDescriptor::esas(),
            "Выставление пользовательских счетов в ЕРИП"
        );
    }

    public function getCompletionPanel($orderWrapper)
    {
        return new CompletionPanelEposTilda($orderWrapper);
    }

    /**
     * @param $orderWrapper
     * @param $completionPanel
     * @return CompletionPageEpos
     */
    public function getCompletionPage($orderWrapper, $completionPanel)
    {
        return new CompletionPageEpos($orderWrapper, $completionPanel);
    }

    public function createHooks()
    {
        return new HooksEposTilda();
    }
}