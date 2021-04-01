<?php

namespace stf;

use \Exception;
use stf\browser\HttpClient;
use stf\browser\HttpRequest;
use stf\browser\HttpResponse;
use stf\browser\RequestBuilder;
use stf\browser\page\Element;
use stf\browser\page\Form;
use stf\browser\page\PageBuilder;
use stf\browser\page\PageParser;
use stf\browser\page\NodeTree;

function getForm() : Form {
    $form = getGlobals()->page->getForm();

    if ($form === null) {
        fail(ERROR_W07, "Current page does not contain a form");
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
    $g = getGlobals();

    $form = $g->page->getForm();

    $request = (new RequestBuilder($form, $g->currentUrl))
        ->requestFromButtonPress($buttonName, $buttonValue);

    executeRequestWithRedirects($request);
}

function executeRequestWithRedirects(HttpRequest $request) {

    $response = executeRequest($request);

    $count = 3;
    while ($response->isRedirect() && $count-- > 0) {

        $request = new HttpRequest($request->getFullUrl(),
            $response->getLocation(), 'GET');

        $response = executeRequest($request);
    }

    updatePage($response);
}

function updatePage(HttpResponse $response) : void {
    $pageParser = new PageParser($response->getContents());

    assertValidResponse($response->getResponseCode());
    assertValidHtml($pageParser);

    $nodeTree = new NodeTree($pageParser->getNodeTree());

    $page = (new PageBuilder($nodeTree, $response->getContents()))->getPage();

    $globals = getGlobals();
    $globals->responseCode = $response->getResponseCode();
    $globals->page = $page;
}

function executeRequest(HttpRequest $request) : HttpResponse {
    $globals = getGlobals();

    $url = $request->getFullUrl()->asString();

    assertValidUrl($url);

    try {
        $response = (new HttpClient())->execute($request);
    } catch (FrameworkException $e) {
        throw $e;
    } catch (Exception $e) {
        throw new FrameworkException(ERROR_G01, $e->getMessage());
    } finally {
        if ($globals->logRequests) {
            printf("%s (%s)\n", $url,
                $response->getResponseCode() ?? 'no response code');
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

    $message = sprintf("Application responded with incorrect HTML\n");
    $message .= sprintf("%s at line %s, column %s\n\n",
        $result->getMessage(), $result->getLine(), $result->getColumn());
    $message .= sprintf("%s\n", $result->getSource());

    throw new FrameworkParseException(ERROR_H01, $message, $pageParser->getHtml());
}

function assertValidUrl(string $url) : void {
    $invalidCharsRegex = "/[^0-9A-Za-z:\/?#\[\]@!$&\'()*+,;=\-._~%]/";

    if (!preg_match($invalidCharsRegex, $url, $matches)) {
        return;
    }

    $message = sprintf("Url '%s' contains illegal character: '%s'",
        $url, $matches[0]);

    fail(ERROR_H02, $message);
}
