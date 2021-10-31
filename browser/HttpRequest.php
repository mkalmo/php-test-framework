<?php

namespace stf\browser;

class HttpRequest {

    private Url $baseUrl;
    private string $subPath;
    private string $method;
    private array $parameters = [];

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
        $this->parameters[$name] = $value;
    }

    public function getParameters() : array {
        return $this->parameters;
    }

    public function getFullUrl() : Url {
        $url = $this->baseUrl->navigateTo($this->subPath);

        if ($this->isPostMethod()) {
            return $url;
        }

        foreach ($this->parameters as $key => $value) {
            $url->addRequestParameter($key, urlencode($value));
        }

        return $url;
    }
}
