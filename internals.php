<?php

namespace stf;

use \Exception;
use stf\browser\HttpRequest;
use stf\browser\HttpResponse;
use stf\browser\RequestBuilder;
use stf\browser\page\Element;
use stf\browser\page\FormSet;
use stf\browser\page\PageBuilder;
use stf\browser\page\PageParser;
use stf\browser\page\NodeTree;

function getFormSet() : FormSet {
    $form = getGlobals()->page->getFormSet();

    if ($form === null) {
        fail(ERROR_W07, "Current page does not contain any form elements");
    }

    return $form;
}

function getGlobals() : Globals {
    $key = "---STF-GLOBALS---";

    return $GLOBALS[$key] ??= new Globals();
}

function getElementWithId($id) : ?Element {
    $elements = getGlobals()->page->getElements();

    foreach ($elements as $element) {
        if ($element->getId() === $id) {
            return $element;
        }
    }

    return null;
}

function navigateTo(string $destination) : void {
    $request = new HttpRequest(getGlobals()->currentUrl, $destination, 'GET');

    executeRequestWithRedirects($request);
}

function submitFormByButtonPress(string $buttonName, ?string $buttonValue) {
    $globals = getGlobals();

    $formSet = $globals->page->getFormSet();

    $request = (new RequestBuilder($formSet, $globals->currentUrl))
        ->requestFromButtonPress($buttonName, $buttonValue);

    executeRequestWithRedirects($request);
}

function executeRequestWithRedirects(HttpRequest $request) {

    $response = executeRequest($request);

    $count = getGlobals()->maxRedirectCount;
    while ($response->isRedirect() && $count-- > 0) {

        $request = new HttpRequest($request->getFullUrl(),
            $response->getLocation(), 'GET');

        $response = executeRequest($request);
    }

    updateGlobals($response);
}

function updateGlobals(HttpResponse $response) : void {
    $globals = getGlobals();

    assertValidResponse($response->getResponseCode());

    $globals->responseCode = $response->getResponseCode();
    $globals->responseContents = $response->getContents();

    $pageParser = new PageParser($response->getContents());

    assertValidHtml($pageParser);

    $nodeTree = new NodeTree($pageParser->getNodeTree());

    $page = (new PageBuilder($nodeTree, $response->getContents()))->getPage();

    $globals->page = $page;
}

function executeRequest(HttpRequest $request) : HttpResponse {
    $globals = getGlobals();

    $url = $request->getFullUrl()->asString();

    assertValidUrl($url);

    try {
        $response = $globals->httpClient->execute($request);
    } catch (FrameworkException $e) {
        throw $e;
    } catch (Exception $e) {
        throw new FrameworkException(ERROR_G01, $e->getMessage());
    } finally {
        if ($globals->logRequests) {
            $responseCode = isset($response)
                ? $response->getResponseCode()
                : 'no response code';

            $method = $request->isPostMethod() ? "POST" : 'GET';

            printf("%s %s (%s)\n", $method, $url, $responseCode);
        }
    }

    if ($globals->logPostParameters && $request->isPostMethod()) {
        printf("   POST parameters: %s" . PHP_EOL,
            mapAsString($request->getParameters()));
    }

    $globals->currentUrl = $request->getFullUrl();

    return $response;
}

function assertValidResponse(int $code): void {
    if ($code >= 400) {
        fail(ERROR_N02, "Server responded with error " . $code);
    }
}

function assertValidHtml(PageParser $pageParser): void {
    $result = $pageParser->validate();

    if ($result->isSuccess()) {
        return;
    }

    $message = "Application responded with incorrect HTML\n";
    $message .= sprintf("%s at line %s, column %s\n\n",
        $result->getMessage(), $result->getLine(), $result->getColumn());
    $message .= sprintf("%s\n", $result->getSource());

    throw new FrameworkException(ERROR_H01, $message);
}

function assertValidUrl(string $url) : void {
    $invalidCharsRegex = "/[^0-9A-Za-z:\/?#\[\]@!$&'()*+,;=\-._~%]/";

    if (!preg_match($invalidCharsRegex, $url, $matches)) {
        return;
    }

    $message = sprintf("Url '%s' contains illegal character: '%s'",
        $url, $matches[0]);

    fail(ERROR_H02, $message);
}
