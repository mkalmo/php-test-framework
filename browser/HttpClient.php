<?php

namespace stf;

include_once __DIR__ . '/../simpletest/user_agent.php';
include_once __DIR__ . '/../FrameworkException.php';

include_once 'Response.php';

use \SimpleUserAgent;
use \SimpleGetEncoding;
use \SimplePostEncoding;
use \SimpleUrl;
use \RuntimeException;

class HttpClient {

    function execute(Request $request) : Response {

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

        return new Response($headers->getResponseCode(), $response->getContent());
    }

}


