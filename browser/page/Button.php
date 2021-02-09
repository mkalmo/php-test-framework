<?php

namespace stf;

require_once 'AbstractInput.php';

class Button extends AbstractInput {

    private $formAction;

    public function __construct($name, $value, $formAction) {
        parent::__construct($name, $value);
        $this->formAction = $formAction;
    }

    public function __toString() {
        return sprintf("Button: %s %s %s",
            $this->getName(), $this->getValue(), $this->formAction);
    }


}


