<?php

namespace stf;

use stf\browser\Browser;
use stf\browser\HttpBrowser;
use stf\browser\Url;

class Globals {

    public Url $baseUrl;
    public Browser $browser;

    public bool $logRequests = false;
    public bool $logPostParameters = false;
    public bool $printStackTrace = false;
    public bool $printPageSourceOnError = false;

    public function __construct() {
        $this->browser = new HttpBrowser($this);
    }
}

