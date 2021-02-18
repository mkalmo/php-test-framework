<?php

namespace stf;

abstract class AbstractInput {

    private string $name;

    public function __construct(string $name) {
        $this->name = $name;
    }

    public function getName() : string {
        return $this->name;
    }

    public abstract function getValue() : string;

}


