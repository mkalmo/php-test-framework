<?php

namespace stf;

use Exception;

require_once 'Globals.php';
require_once 'browser/Url.php';
require_once 'browser/HttpClient.php';
require_once 'browser/HttpRequest.php';
require_once 'browser/HttpResponse.php';
require_once 'browser/RequestBuilder.php';
require_once 'browser/page/PageBuilder.php';
require_once 'browser/page/PageParser.php';

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

//function getBrowser() : Browser {
//    $key = "---STF-BROWSER---";
//
//    return $GLOBALS[$key] = $GLOBALS[$key] ?? new Browser(getSettings());
//}
//
//function getSettings() : Settings {
//    $key = "---STF-SETTINGS---";
//
//    return $GLOBALS[$key] = $GLOBALS[$key] ?? new Settings();
//}

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

    executeRequest($request);
}

function submitFormByButtonPress(string $buttonName) {
    $g = getGlobals();

    $form = $g->page->getForm();

    $request = (new RequestBuilder($form, $g->currentUrl))
        ->requestFromButtonPress($buttonName);

    executeRequest($request);
}

function executeRequest(HttpRequest $request) {
    $g = getGlobals();

    $url = $request->getFullUrl()->asString();

    assertValidUrl($url);

    try {
        $response = (new HttpClient())->execute($request);
    } catch (FrameworkException $e) {
        throw $e;
    } catch (Exception $e) {
        throw new FrameworkException(ERROR_G01, $e->getMessage());
    } finally {
        if ($g->logRequests) {
            printf("%s (%s)\n", $url, $response->code ?? 'no response code');
        }
    }


    $g->currentUrl = $request->getFullUrl();


    if ($g->logPostParameters && $request->isPostMethod()) {
        printf("   POST parameters: %s" . PHP_EOL,
            mapAsString($request->getParameters()));
    }

    $pageParser = new PageParser($response->contents);

    assertValidResponse($response->code);
    assertValidHtml($pageParser);

    $page = (new PageBuilder($response->contents,
        $pageParser->getNodeTree()))->getPage();

    $g->responseCode = $response->code;
    $g->page = $page;
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

    fail(ERROR_H01, $message);
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
