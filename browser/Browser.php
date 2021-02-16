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
    private int $responseCode;
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

    public function getResponseCode(): int {
        return $this->responseCode;
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

        if ($this->settings->logRequests) {
            printf("%s (%s)" . PHP_EOL,
                $request->getFullUrl()->asString(), $response->code);
        }

        if ($this->settings->logPostParameters && $request->isPostMethod()) {
            printf("POST parameters: %s" . PHP_EOL,
                mapAsString($request->getParameters()));
        }

        $page = (new PageBuilder($response->contents))->getPage();

        $this->responseCode = $response->code;
        $this->page = $page;
    }

}


