<?php

namespace stf;

require_once 'HttpClient.php';
require_once 'Request.php';
require_once 'page/PageBuilder.php';
require_once 'RequestBuilder.php';
require_once 'Url.php';

class Browser {

    private Url $currentUrl;
    private Page $page;
    private Settings $settings;

    public function __construct(Settings $settings) {
        $this->settings = $settings;
    }

    public function setCurrentUrl(string $url) {
        $this->currentUrl = new Url($url);
    }

    public function getCurrentUrl() : string {
        return $this->currentUrl->asString();
    }

    public function getPage() : Page {
        return $this->page;
    }

    function navigateTo(string $destination) {
        $request = new Request($this->currentUrl, $destination, 'GET');

        $this->executeRequest($request);
    }

    public function submitFormByButtonPress(string $buttonName) {
        $form = $this->page->getForm();

        $request = (new RequestBuilder($form, $this->currentUrl))
            ->requestFromButtonPress($buttonName);

        $this->executeRequest($request);
    }

    private function executeRequest(Request $request) {
        $response = (new HttpClient())->execute($request);

        $this->currentUrl = $request->getFullUrl();

//        var_dump($this->settings->logRequests);

        if ($this->settings->logRequests) {
            printf("%s (%s)" . PHP_EOL,
                $request->getFullUrl()->asString(), $response->code);
        }

        $page = (new PageBuilder($response->contents))->getPage();

        $this->page = $page;
    }



}


