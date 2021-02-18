<?php

namespace stf;

require_once 'AbstractInput.php';

class Button extends AbstractInput {

    private string $formAction;
    private string $value;

    public function __construct(string $name, string $value, string $formAction) {
        parent::__construct($name);
        $this->value = $value;
        $this->formAction = $formAction;
    }

    public function __toString() : string {
        return sprintf("Button: %s %s %s",
            $this->getName(), $this->getValue(), $this->formAction);
    }

    public function getValue(): string {
        return $this->value;
    }
}


