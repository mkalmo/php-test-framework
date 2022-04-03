<?php

namespace stf\browser;

use \SimpleGetEncoding;
use \SimplePostEncoding;
use \SimpleUrl;
use \SimpleCookieJar;
use \SimpleHttpRequest;
use \SimpleRoute;
use stf\FrameworkException;

class HttpClient {

    private SimpleCookieJar $cookieJar;

    public function __construct() {
        $this->cookieJar = new SimpleCookieJar();
    }

    function execute(HttpRequest $request) : HttpResponse {

        $encoding = $request->isPostMethod()
            ? new SimplePostEncoding()
            : new SimpleGetEncoding();

        if ($request->isPostMethod()) {
            foreach ($request->getParameters() as $key => $value) {
                $encoding->add($key, $value);
            }
        }

        $url = $request->getFullUrl()->asString();
        $simpleHttpRequest = new SimpleHttpRequest(
            new SimpleRoute(new SimpleUrl($url)), $encoding);

        $simpleHttpRequest->readCookiesFromJar($this->cookieJar, new SimpleUrl($url));

        $response = $simpleHttpRequest->fetch(TIMEOUT);

        if ($response->isError()) {
            throw new FrameworkException($response->getErrorCode(),
                $response->getError());
        }

        $response->getHeaders()->writeCookiesToJar(
            $this->cookieJar, new SimpleUrl($url));

        $headers = new HttpHeaders(
            $response->getHeaders()->getResponseCode(),
            $response->getHeaders()->getLocation() ?: null);

        return new HttpResponse($headers, $response->getContent());
    }

    public function deleteCookie(string $cookieName) {
        $this->cookieJar->deleteCookie($cookieName);
    }
}


