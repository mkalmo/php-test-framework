<?php

namespace stf;

require_once 'AbstractInput.php';

class Button extends AbstractInput {

    private string $formAction;

    public function __construct(string $name, string $value, string $formAction) {
        parent::__construct($name, $value);
        $this->formAction = $formAction;
    }

    public function __toString() : string {
        return sprintf("Button: %s %s %s",
            $this->getName(), $this->getValue(), $this->formAction);
    }


}


