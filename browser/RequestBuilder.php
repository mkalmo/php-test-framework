<?php

namespace stf\browser;

use stf\browser\page\Form;
use stf\FrameworkException;

class RequestBuilder {

    private Form $form;
    private Url $currentUrl;

    public function __construct(Form $form, Url $currentUrl) {
        $this->form = $form;
        $this->currentUrl = $currentUrl;
    }

    public function requestFromButtonPress(
        string $buttonName, string $buttonValue) : HttpRequest {

        $button = $this->form->getButtonByNameAndValue($buttonName, $buttonValue);

        if ($button === null) {
            throw new FrameworkException(
                ERROR_W06,
                sprintf("Form does not contain submit button with name '%s'.", $buttonName));
        }

        $action = $button->getFormAction() ?: $this->form->getAction();

        $request = new HttpRequest($this->currentUrl, $action, $this->form->getMethod());

        $request->addParameter($button->getName(), $button->getValue());

        foreach ($this->form->getFields() as $field) {
            $request->addParameter($field->getName(), $field->getValue() ?? '');
        }

        return $request;
    }
}
