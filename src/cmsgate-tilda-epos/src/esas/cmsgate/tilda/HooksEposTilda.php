<?php


namespace esas\cmsgate\tilda;


use esas\cmsgate\bridge\service\OrderService;
use esas\cmsgate\epos\HooksEpos;
use esas\cmsgate\epos\protocol\EposCallbackRq;
use esas\cmsgate\epos\protocol\EposInvoiceGetRs;
use esas\cmsgate\Registry;
use esas\cmsgate\tilda\controllers\ControllerTildaNotify;
use esas\cmsgate\wrappers\OrderWrapper;

class HooksEposTilda extends HooksEpos
{
    public function onCallbackRqRead(EposCallbackRq $rq)
    {
        parent::onCallbackRqRead($rq);
        OrderService::fromRegistry()->loadSessionOrderByExtId($rq->getInvoiceId());
    }

    public function onCallbackStatusPayed(OrderWrapper $orderWrapper, EposInvoiceGetRs $resp)
    {
        parent::onCallbackStatusPayed($orderWrapper, $resp);
        $controller = new ControllerTildaNotify();
        $controller->process(Registry::getRegistry()->getOrderWrapperForCurrentUser());
    }
}