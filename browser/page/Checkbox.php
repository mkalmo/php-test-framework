<?php

namespace stf;

require_once 'AbstractInput.php';

class Checkbox extends AbstractInput {

    private string $value;
    private bool $isChecked = false;

    public function __construct(string $name, string $value) {
        parent::__construct($name);
        $this->value = $value;
    }

    public function check(bool $isChecked) : void {
        $this->isChecked = $isChecked;
    }

    public function isChecked() : bool {
        return $this->isChecked;
    }

    public function __toString() : string {
        return sprintf("Checkbox: '%s', value: '%s', %s\n",
            $this->getName(), $this->value,
            $this->isChecked ? 'checked' : 'unchecked');
    }

    public function getValue() : string {
        return $this->isChecked ? $this->value : '';
    }
}


