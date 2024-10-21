<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 01.10.2018
 * Time: 12:05
 */

namespace esas\cmsgate\tilda;

use esas\cmsgate\bridge\security\CmsAuthService;
use esas\cmsgate\bridge\service\SessionServiceBridge;
use esas\cmsgate\bridge\view\admin\ConfigFormBridge;
use esas\cmsgate\descriptors\ModuleDescriptor;
use esas\cmsgate\descriptors\VendorDescriptor;
use esas\cmsgate\descriptors\VersionDescriptor;
use esas\cmsgate\epos\ConfigFieldsEpos;
use esas\cmsgate\epos\hro\client\CompletionPanelEposHRO;
use esas\cmsgate\epos\hro\client\CompletionPanelEposHRO_v2;
use esas\cmsgate\epos\hro\sections\FooterSectionCompanyInfoHROTunerEpos;
use esas\cmsgate\epos\hro\sections\HeaderSectionLogoContactsHROTunerEpos;
use esas\cmsgate\epos\PaysystemConnectorEpos;
use esas\cmsgate\epos\RegistryEpos;
use esas\cmsgate\hro\HROManager;
use esas\cmsgate\hro\pages\AdminLoginPageHRO;
use esas\cmsgate\hro\sections\FooterSectionCompanyInfoHRO;
use esas\cmsgate\hro\sections\HeaderSectionLogoContactsHRO;
use esas\cmsgate\tilda\hro\AdminLoginPageHROTunerTildaEpos;
use esas\cmsgate\tilda\protocol\RequestParamsTilda;
use esas\cmsgate\tilda\service\CmsAuthServiceTildaEpos;
use esas\cmsgate\tilda\service\ServiceProviderTilda;
use esas\cmsgate\utils\CMSGateException;
use esas\cmsgate\utils\URLUtils;
use esas\cmsgate\view\admin\AdminViewFields;

class RegistryEposTilda extends RegistryEpos
{
    public function __construct() {
        $this->cmsConnector = new CmsConnectorTilda();
        $this->paysystemConnector = new PaysystemConnectorEpos();
    }

    public function init() {
        parent::init();
        $this->registerServicesFromProvider(new ServiceProviderTilda());
        $this->registerService(CmsAuthService::class, new CmsAuthServiceTildaEpos());

        HROManager::fromRegistry()->addImplementation(CompletionPanelEposHRO::class, CompletionPanelEposHRO_v2::class);
        HROManager::fromRegistry()->addTuner(AdminLoginPageHRO::class, AdminLoginPageHROTunerTildaEpos::class);
        HROManager::fromRegistry()->addTuner(FooterSectionCompanyInfoHRO::class, FooterSectionCompanyInfoHROTunerEpos::class);
        HROManager::fromRegistry()->addTuner(HeaderSectionLogoContactsHRO::class, HeaderSectionLogoContactsHROTunerEpos::class);
    }

    /**
     * Переопределение для упрощения типизации
     * @return RegistryEposTilda
     */
    public static function getRegistry() {
        return parent::getRegistry();
    }

    public function createConfigForm() {
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
        $configForm = new ConfigFormBridge(
            $managedFields,
            AdminViewFields::CONFIG_FORM_COMMON,
            null,
            ''
        );
        return $configForm;
    }


    function getUrlWebpay($orderWrapper) {
        $currentURL = URLUtils::getCurrentURLNoParams();
        $currentURL = str_replace(PATH_INVOICE_ADD, PATH_INVOICE_VIEW, $currentURL);
        if (strpos($currentURL, PATH_INVOICE_VIEW) !== false)
            return $currentURL . '?' . RequestParamsTilda::ORDER_ID . '=' . SessionServiceBridge::fromRegistry()->getOrderUUID();
        else
            throw new CMSGateException('Incorrect URL generation');
    }

    public function createModuleDescriptor() {
        return new ModuleDescriptor(
            "commerce-tilda-epos", // код должен совпадать с кодом решения в маркете (@id в Plugin\Commerce\PaymentGateway\xxx.php)
            new VersionDescriptor("2.0.1", "2024-10-18"),
            "Tilda EPOS",
            "https://github.com/esasby/cmsgate-tilda-epos",
            VendorDescriptor::esas(),
            "Выставление пользовательских счетов в ЕРИП"
        );
    }

    public function createHooks() {
        return new HooksEposTilda();
    }

    public function createConfigStorage() {
        return new ConfigStorageTildaEpos();
    }

    public function createProperties() {
        return new PropertiesTildaEpos();
    }
}