<?php

use esas\cmsgate\bridge\controllers\ControllerBridge;
use esas\cmsgate\bridge\dao\Order;
use esas\cmsgate\bridge\service\OrderService;
use esas\cmsgate\bridge\service\SessionServiceBridge;
use esas\cmsgate\bridge\service\ShopConfigService;
use esas\cmsgate\epos\controllers\ControllerEposCallback;
use esas\cmsgate\epos\controllers\ControllerEposCompletionPage;
use esas\cmsgate\epos\controllers\ControllerEposInvoiceAdd;
use esas\cmsgate\hro\pages\ClientOrderCompletionPageHROFactory;
use esas\cmsgate\Registry;
use esas\cmsgate\tilda\properties\PropertiesTilda;
use esas\cmsgate\tilda\protocol\RequestParamsTilda;
use esas\cmsgate\utils\JSONUtils;
use esas\cmsgate\utils\StringUtils;
use esas\cmsgate\utils\Logger as LoggerCms;

require_once((dirname(__FILE__)) . '/src/init.php');

$request = &$_SERVER['REDIRECT_URL'];
const PATH_INVOICE_ADD = '/api/invoice/add';
const PATH_INVOICE_VIEW = '/api/invoice/view';
const PATH_INVOICE_CALLBACK = '/api/invoice/callback';

$logger = LoggerCms::getLogger('index');
if (strpos($request, 'api') !== false) {
    try {
        $logger->info('Got request from Tilda: ' . JSONUtils::encodeArrayAndMask($_REQUEST, ["ps_iii_client_secret"]));
        if (StringUtils::endsWith($request, PATH_INVOICE_ADD)) {
            ShopConfigService::fromRegistry()->checkAuthAndLoadConfig($_REQUEST);
            $order = new Order();
            $order->setOrderData($_REQUEST);
            OrderService::fromRegistry()->addSessionOrder($order);
            $orderWrapper = Registry::getRegistry()->getOrderWrapperForCurrentUser();
            if ($orderWrapper->getExtId() == null || $orderWrapper->getExtId() == '') {
                $controller = new ControllerEposInvoiceAdd();
                $controller->process($orderWrapper);
            }
            renderCompletionPage($orderWrapper);
        } elseif (strpos($request, PATH_INVOICE_VIEW) !== false) {
            $uuid = $_REQUEST[RequestParamsTilda::ORDER_ID];
            SessionServiceBridge::fromRegistry()->setOrderUUID($uuid);
            $orderWrapper = Registry::getRegistry()->getOrderWrapperForCurrentUser();
            renderCompletionPage($orderWrapper);
        } elseif (strpos($request, PATH_INVOICE_CALLBACK) !== false) {
            $controller = new ControllerEposCallback();
            $controller->process();
        } else {
            http_response_code(404);
            return;
        }
    } catch (Exception $e) {
        $logger->error("Exception", $e);
        ClientOrderCompletionPageHROFactory::findBuilder()
            ->setOrderWrapper(Registry::getRegistry()->getOrderWrapperForCurrentUser())
            ->setElementCompletionPanel(null)
            ->addCssLink(PropertiesTilda::fromRegistry()->getDefaultClientUICssLink())
            ->render();
    } catch (Throwable $e) {
        $logger->error("Exception", $e);
        ClientOrderCompletionPageHROFactory::findBuilder()
            ->setOrderWrapper(Registry::getRegistry()->getOrderWrapperForCurrentUser())
            ->setElementCompletionPanel(null)
            ->addCssLink(PropertiesTilda::fromRegistry()->getDefaultClientUICssLink())
            ->render();
    }
} else {
    $controller = new ControllerBridge();
    $controller->process();
}

/**
 * @param $orderWrapper \esas\cmsgate\wrappers\OrderWrapper
 * @throws Throwable
 */
function renderCompletionPage($orderWrapper) {
    $controller = new ControllerEposCompletionPage();
    $completeionPage = $controller->process($orderWrapper);
    $completeionPage->addCssLink(PropertiesTilda::fromRegistry()->getDefaultClientUICssLink());
    $completeionPage->render();
}