<?php

namespace stf\browser;

class HttpRequest {

    private Url $baseUrl;
    private string $subPath;
    private string $method;
    private array $getParams = [];

    public function __construct(
        Url $baseUrl, string $subPath, string $method) {

        $this->baseUrl = $baseUrl;
        $this->subPath = $subPath;
        $this->method = $method;
    }

    public function isPostMethod() : bool {
        return strtoupper($this->method) === 'POST';
    }

    public function addParameter(string $name, string $value) {
        $this->getParams[$name] = $value;
    }

    public function getParameters() : array {
        return $this->getParams;
    }

    public function getFullUrl() : Url {
        return $this->baseUrl->navigateTo($this->subPath);
    }
}


