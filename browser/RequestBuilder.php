<?php

namespace stf;

use \RuntimeException;

require_once 'HttpRequest.php';

class RequestBuilder {

    private Form $form;
    private Url $currentUrl;

    public function __construct(Form $form, Url $currentUrl) {
        $this->form = $form;
        $this->currentUrl = $currentUrl;
    }

    public function requestFromButtonPress(string $buttonName) : HttpRequest {
        $button = $this->form->getButtonByName($buttonName);

        if ($button === null) {
            throw new RuntimeException('no such button: ' . $buttonName);
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
