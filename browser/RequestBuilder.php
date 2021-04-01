<?php

namespace stf\browser;

use stf\browser\page\FormSet;

class RequestBuilder {

    private FormSet $formSet;
    private Url $currentUrl;

    public function __construct(FormSet $formSet, Url $currentUrl) {
        $this->formSet = $formSet;
        $this->currentUrl = $currentUrl;
    }

    public function requestFromButtonPress(
        string $buttonName, ?string $buttonValue) : HttpRequest {

        $form = $this->formSet->findFormContainingField($buttonName);

        $button = $form->getButtonByNameAndValue($buttonName, $buttonValue);

        $action = $button->getFormAction() ?: $form->getAction();

        $request = new HttpRequest($this->currentUrl, $action, $form->getMethod());

        $request->addParameter($button->getName(), $button->getValue());

        foreach ($form->getFields() as $field) {
            $request->addParameter($field->getName(), $field->getValue() ?? '');
        }

        return $request;
    }
}
