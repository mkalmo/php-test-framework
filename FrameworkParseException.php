<?php

namespace stf;

class FrameworkParseException extends FrameworkException {

    private string $fullSource;

    public function __construct(string $code, string $message, string $fullSource) {
        parent::__construct($code, $message);

        $this->fullSource = $fullSource;
    }

    public function getFullSource(): string {
        return $this->fullSource;
    }
}

