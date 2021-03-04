<?php

namespace stf\browser;

class HttpResponse {

    public int $code;
    public string $contents;

    public function __construct(int $code, string $contents) {
        $this->code = $code;
        $this->contents = $contents;
    }


}


