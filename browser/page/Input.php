<?php

namespace stf;

require_once 'AbstractInput.php';

class Input extends AbstractInput {

    public function __toString() {
        return sprintf("Input: %s %s",
            $this->getName(), $this->getValue());
    }

}


