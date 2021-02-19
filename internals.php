<?php

namespace stf;

require_once 'Globals.php';
require_once 'browser/Url.php';
require_once 'browser/HttpClient.php';
require_once 'browser/HttpRequest.php';
require_once 'browser/HttpResponse.php';
require_once 'browser/RequestBuilder.php';
require_once 'browser/page/PageBuilder.php';

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

    $response = (new HttpClient())->execute($request);

    $g->currentUrl = $request->getFullUrl();

    if ($g->logRequests) {
        printf("%s (%s)" . PHP_EOL,
            $request->getFullUrl()->asString(), $response->code);
    }

    if ($g->logPostParameters && $request->isPostMethod()) {
        printf("   POST parameters: %s" . PHP_EOL,
            mapAsString($request->getParameters()));
    }

    $page = (new PageBuilder($response->contents))->getPage();

    $g->responseCode = $response->code;
    $g->page = $page;
}
