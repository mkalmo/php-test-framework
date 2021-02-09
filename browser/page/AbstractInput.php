<?php

namespace stf;

class AbstractInput {

    private $name;
    private $value;

    public function __construct($name, $value) {
        $this->name = $name;
        $this->value = $value;
    }

    public function getName() : ?string {
        return $this->name;
    }

    public function getValue() : ?string {
        return $this->value;
    }

    public function setValue(string $value): void {
        $this->value = $value;
    }

}


