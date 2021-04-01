<?php

namespace stf\browser;

class HttpHeaders {

    public int $responseCode;
    public ?string $location;

    public function __construct(int $responseCode, ?string $location) {
        $this->responseCode = $responseCode;
        $this->location = $location;
    }


}


