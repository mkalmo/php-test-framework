<?php

namespace stf;

function getForm() : Form {
    $form = getBrowser()->getPage()->getForm();

    if ($form === null) {
        fail(ERROR_W07, "Current page does not contain form");
    }

    return $form;
}

function getBrowser() : Browser {
    $key = "---STF-BROWSER---";

    return $GLOBALS[$key] = $GLOBALS[$key] ?? new Browser(getSettings());
}

function getSettings() : Settings {
    $key = "---STF-SETTINGS---";

    return $GLOBALS[$key] = $GLOBALS[$key] ?? new Settings();
}

function getElementWithId($id) : ?Element {
    $elements = getBrowser()->getPage()->getElements();

    foreach ($elements as $element) {
        if ($element->getId() === $id) {
            return $element;
        }
    }

    return null;
}
