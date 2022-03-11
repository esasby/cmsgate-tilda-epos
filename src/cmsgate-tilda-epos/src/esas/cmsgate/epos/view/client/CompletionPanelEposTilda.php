<?php
/**
 * Created by IntelliJ IDEA.
 * User: nikit
 * Date: 24.06.2019
 * Time: 14:11
 */

namespace esas\cmsgate\epos\view\client;

use esas\cmsgate\utils\htmlbuilder\Attributes as attribute;
use esas\cmsgate\utils\htmlbuilder\Elements as element;

class CompletionPanelEposTilda extends CompletionPanelEpos
{
    public function getCssClass4MsgSuccess()
    {
        return "alert alert-info";
    }

    public function getCssClass4MsgUnsuccess()
    {
        return "alert alert-danger";
    }

    public function getCssClass4Button()
    {
        return "btn btn-primary";
    }

    public function getCssClass4TabsGroup()
    {
        return "accordion";
    }

    public function getCssClass4Tab()
    {
        return "accordion-item";
    }

    public function elementTab($key, $header, $body, $selectable = true)
    {
        return
            element::div(
                attribute::clazz("accordion-item"),
                $this->elementTabHeader($key, $header),
                $this->elementTabBody($key, $body)
            )->__toString();
    }

    public function elementTabHeader($key, $header)
    {
        return
            element::h2(
                attribute::clazz("accordion-header"),
                attribute::id($this->getTabHeaderId($key)),
                element::button(
                    attribute::type("button"),
                    attribute::data_bs_toggle("collapse"),
                    attribute::data_bs_target("#" . $this->getTabBodyId($key)),
                    attribute::aria_controls("#" . $this->getTabBodyId($key)),
                    attribute::clazz('accordion-button collapsed'),
                    element::content($header)
                )
            );
    }

    public function getTabHeaderId($key) {
        return "heading" . $key;
    }

    public function getTabBodyId($key) {
        return "collapse" . $key;
    }

    public function elementTabBody($key, $body)
    {
        return
            element::div(
                attribute::id($this->getTabBodyId($key)),
                attribute::clazz("accordion-collapse collapse"),
                attribute::aria_labelledby($this->getTabHeaderId($key)),
                attribute::data_bs_parent("#hutkigrosh-completion-tabs"),
                element::div(
                    attribute::clazz("accordion-body"),
                    element::content($body)
                )
            );
    }

    public function getCssClass4AlfaclickForm()
    {
        return "form-inline";
    }

    public function getCssClass4FormInput()
    {
        return "form-control";
    }

    public function getModuleCSSFilePath()
    {
        return "";
    }

}