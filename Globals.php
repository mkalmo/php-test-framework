<?php

namespace stf;

use stf\browser\HttpClient;
use stf\browser\Url;
use stf\browser\page\Page;

class Globals {

    const MAX_REDIRECT_COUNT = 3;

    public HttpClient $httpClient;

    public Url $baseUrl;
    public Url $currentUrl;
    public string $responseContents;
    public int $responseCode;
    public Page $page;

    public bool $logRequests = false;
    public bool $logPostParameters = false;
    public bool $printStackTrace = false;
    public bool $printPageSourceOnError = false;

    public int $maxRedirectCount = self::MAX_REDIRECT_COUNT;

    public function __construct() {
        $this->reset();
    }

    public function reset() : void {
        $this->httpClient = new HttpClient();
        $this->maxRedirectCount = self::MAX_REDIRECT_COUNT;
    }
}

