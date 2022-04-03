<?php

namespace stf\browser;

interface Browser {
    function setCurrentUrl(string $url) : void;

    function getCurrentUrl() : string;

    function getResponseContents() : string;

    function reset() : void;

    function navigateTo(string $url);

    function getPageId() : ?string;

    function getLinkHrefById(string $id) : string;

    function getLinkHrefByText(string $text) : string;

    function hasLinkWithId(string $id) : bool;

    function hasLinkWithText(string $linkText) : bool;

    function hasElementWithId(string $id) : bool;

    function clickLinkWithId(string $linkId) : void;

    function clickLinkWithText(string $linkText) : void;

    function hasFieldByName(string $fieldName, string $type) : bool;

    function setTextFieldValue(string $fieldName, string $value) : void;

    function hasRadioOption(string $fieldName, string $optionValue) : bool;

    function hasSelectOptionWithLabel(string $fieldName, string $label) : bool;

    function selectOptionWithLabel(string $fieldName, string $label) : void;

    function getSelectedOptionText(string $fieldName) : string;

    function setRadioValue(string $fieldName, string $value) : void;

    function setCheckboxValue(string $fieldName, string $value) : void;

    function forceFieldValue(string $fieldName, string $value) : void;

    function getFieldValue(string $fieldName); // union type string | bool

    function submitFormByButtonPress(string $buttonName, ?string $buttonValue);

    function getPageText() : string;

    function getPageSource() : string;

}