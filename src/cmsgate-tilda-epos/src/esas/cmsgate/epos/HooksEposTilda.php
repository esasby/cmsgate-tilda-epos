<?php


namespace esas\cmsgate\epos;


use esas\cmsgate\CloudRegistry;
use esas\cmsgate\controller\ControllerTildaNotify;
use esas\cmsgate\epos\protocol\EposCallbackRq;
use esas\cmsgate\epos\protocol\EposInvoiceGetRs;
use esas\cmsgate\Registry;
use esas\cmsgate\wrappers\OrderWrapper;

class HooksEposTilda extends HooksEpos
{
    public function onCallbackRqRead(EposCallbackRq $rq)
    {
        parent::onCallbackRqRead($rq);
        CloudRegistry::getRegistry()->getOrderCacheService()->loadSessionOrderCacheByExtId($rq->getInvoiceId());
    }

    public function onCallbackStatusPayed(OrderWrapper $orderWrapper, EposInvoiceGetRs $resp)
    {
        parent::onCallbackStatusPayed($orderWrapper, $resp);
        $controller = new ControllerTildaNotify();
        $controller->process(Registry::getRegistry()->getOrderWrapperForCurrentUser());
    }
}