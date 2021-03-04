<?php

namespace stf\browser\page;

class TextField extends AbstractInput {

    private string $value;

    public function __construct(string $name, string $value) {
        parent::__construct($name);
        $this->value = $value;
    }

    public function __toString() : string {
        return sprintf("Input %s=%s",
            $this->getName(), $this->value);
    }

    public function getValue() : string {
        return $this->value;
    }

    public function setValue(string $value): void {
        $this->value = $value;
    }
}


