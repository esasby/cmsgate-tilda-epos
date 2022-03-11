<?php

use esas\cmsgate\CloudRegistry;
use esas\cmsgate\controllers\ControllerCloudConfig;
use esas\cmsgate\controllers\ControllerCloudLogin;
use esas\cmsgate\controllers\ControllerCloudLogout;
use esas\cmsgate\controllers\ControllerCloudSecretGenerate;
use esas\cmsgate\epos\controllers\ControllerEposCallback;
use esas\cmsgate\epos\controllers\ControllerEposCompletionPage;
use esas\cmsgate\epos\controllers\ControllerEposInvoiceAdd;
use esas\cmsgate\Registry;
use esas\cmsgate\tilda\RequestParamsTilda;
use esas\cmsgate\utils\CloudSessionUtils;
use esas\cmsgate\utils\JSONUtils;
use esas\cmsgate\utils\StringUtils;
use esas\cmsgate\utils\Logger as LoggerCms;

require_once((dirname(__FILE__)) . '/src/init.php');

$request = $_SERVER['REDIRECT_URL'];
const PATH_CONFIG = '/config';
const PATH_CONFIG_SECRET_NEW = '/config/secret/new';
const PATH_CONFIG_LOGIN = '/config/login';
const PATH_CONFIG_LOGOUT = '/config/logout';
const PATH_INVOICE_ADD = '/api/invoice/add';
const PATH_INVOICE_VIEW = '/api/invoice/view';
const PATH_INVOICE_CALLBACK = '/api/invoice/callback';

$logger = LoggerCms::getLogger('index');
if (strpos($request, 'api') !== false) {
    try {
        $logger->info('Got request from Tilda: ' . JSONUtils::encodeArrayAndMask($_REQUEST, ["ps_iii_client_secret"]));
        if (StringUtils::endsWith($request, PATH_INVOICE_ADD)) {
            // приходится сохрянть заказ где-то в кэше, для возможнсоти повторного отображения страницы в случае возврата с webpay
            CloudRegistry::getRegistry()->getConfigCacheService()->checkAuthAndLoadConfig($_REQUEST);
            CloudRegistry::getRegistry()->getOrderCacheService()->addSessionOrderCache($_REQUEST);
            $orderWrapper = Registry::getRegistry()->getOrderWrapperForCurrentUser();
            if ($orderWrapper->getExtId() == null || $orderWrapper->getExtId() == '') {
                $controller = new ControllerEposInvoiceAdd();
                $controller->process($orderWrapper);
            }
            $controller = new ControllerEposCompletionPage();
            $completeionPage = $controller->process($orderWrapper);
            $completeionPage->render();
        } elseif (strpos($request, PATH_INVOICE_VIEW) !== false) {
            $uuid = $_REQUEST[RequestParamsTilda::ORDER_ID];
            CloudSessionUtils::setOrderCacheUUID($uuid);
            $orderWrapper = Registry::getRegistry()->getOrderWrapperForCurrentUser();
            $controller = new ControllerEposCompletionPage();
            $completeionPage = $controller->process($orderWrapper);
            $completeionPage->render();
        } elseif (strpos($request, PATH_INVOICE_CALLBACK) !== false) {
            $controller = new ControllerEposCallback();
            $controller->process();
        } else {
            http_response_code(404);
            return;
        }
    } catch (Exception $e) {
        $logger->error("Exception", $e);
        $errorPage = Registry::getRegistry()->getCompletionPage(
            Registry::getRegistry()->getOrderWrapperForCurrentUser(),
            null
        );
        $errorPage->render();
    } catch (Throwable $e) {
        $logger->error("Exception", $e);
        $errorPage = Registry::getRegistry()->getCompletionPage(
            Registry::getRegistry()->getOrderWrapperForCurrentUser(),
            null
        );
        $errorPage->render();
    }
} else {
    if (StringUtils::endsWith($request, PATH_CONFIG_LOGIN)) {
        $controller = new ControllerCloudLogin();
        $controller->process();
    } elseif (StringUtils::endsWith($request, PATH_CONFIG_LOGOUT)) {
        $controller = new ControllerCloudLogout();
        $controller->process();
    } elseif (StringUtils::endsWith($request, PATH_CONFIG_SECRET_NEW)) {
        $controller = new ControllerCloudSecretGenerate();
        $controller->process();
    } elseif (StringUtils::endsWith($request, PATH_CONFIG)) {
        $controller = new ControllerCloudConfig();
        $controller->process();
    } else {
        http_response_code(404);
    }
}