<?php

namespace stf\browser;

use \SimpleUserAgent;
use \SimpleGetEncoding;
use \SimplePostEncoding;
use \SimpleUrl;
use stf\FrameworkException;

class HttpClient {

    function execute(HttpRequest $request) : HttpResponse {

        $url = $request->getFullUrl()->asString();

        $agent = new SimpleUserAgent();

        $encoding = $request->isPostMethod() ? new SimplePostEncoding() : new SimpleGetEncoding();

        foreach ($request->getParameters() as $key => $value) {
            $encoding->add($key, $value);
        }

        $response = $agent->fetchResponse(new SimpleUrl($url), $encoding);

        $headers = $response->getHeaders();

        if ($response->isError()) {
            throw new FrameworkException($response->getErrorCode(),
                $response->getError());
        }

        return new HttpResponse($headers->getResponseCode(), $response->getContent());
    }

}


