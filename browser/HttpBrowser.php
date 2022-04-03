<?php

namespace stf\browser;

use Exception;
use stf\browser\page\AbstractInput;
use stf\browser\page\Checkbox;
use stf\browser\page\Element;
use stf\browser\page\FieldType;
use stf\browser\page\FormSet;
use stf\browser\page\NodeTree;
use stf\browser\page\Page;
use stf\browser\page\PageBuilder;
use stf\browser\page\PageParser;
use stf\browser\page\RadioGroup;
use stf\browser\page\Select;
use stf\browser\page\TextField;
use stf\FrameworkException;
use stf\Globals;
use function stf\mapAsString;

class HttpBrowser implements Browser {

    const MAX_REDIRECT_COUNT = 3;

    private Url $currentUrl;
    private int $maxRedirectCount = self::MAX_REDIRECT_COUNT;
    private Page $page;

    private HttpClient $httpClient;
    private Globals $globals;

    private string $responseContents;
    private int $responseCode;

    public function __construct(Globals $globals) {
        $this->globals = $globals;
        $this->reset();
    }

    function getResponseContents() : string {
        return $this->responseContents;
    }

    public function reset() : void {
        $this->httpClient = new HttpClient();
        $this->maxRedirectCount = self::MAX_REDIRECT_COUNT;
    }

    function setCurrentUrl(string $url) : void {
        $this->currentUrl = new Url($url);
    }

    function getCurrentUrl() : string {
        return $this->currentUrl->asString();
    }

    public function navigateTo(string $url) {
        $request = new HttpRequest($this->currentUrl, $url, 'GET');

        $this->executeRequestWithRedirects($request);
    }

    function getPageId() : ?string {
        return $this->page->getId();
    }

    public function hasLinkWithId(string $id) : bool {
        return $this->page->getLinkById($id) !== null;
    }

    public function hasLinkWithText(string $linkText) : bool {
        return $this->page->getLinkByText($linkText) !== null;
    }

    function clickLinkWithId($linkId) : void {
        $link = $this->page->getLinkById($linkId);

        navigateTo($link->getHref());
    }

    function clickLinkWithText(string $linkText) : void {
        $link = $this->page->getLinkByText($linkText);

        navigateTo($link->getHref());
    }

    public function setTextFieldValue(string $fieldName, string $value) : void {
        $this->page->getFormSet()->getTextFieldByName($fieldName)->setValue($value);
    }

    public function hasRadioOption($fieldName, $optionValue) : bool {
        return $this->getFormSet()
            ->getRadioByName($fieldName)
            ->hasOption($optionValue);
    }

    public function setRadioValue(string $fieldName, string $value) : void {
        $this->page->getFormSet()->getRadioByName($fieldName)->selectOption($value);
    }

    public function hasFieldByName(string $fieldName, string $type) : bool {
        if ($type === FieldType::TextField) {
            $class = TextField::class;
        } else if ($type === FieldType::Radio) {
            $class = RadioGroup::class;
        } else if ($type === FieldType::Checkbox) {
            $class = Checkbox::class;
        } else if ($type === FieldType::Select) {
            $class = Select::class;
        } else if ($type === FieldType::Any) {
            $class = AbstractInput::class;
        } else if ($type === FieldType::Button) {
            return $this->page->getFormSet()->getButtonByName($fieldName) !== null;
        } else {
            throw new \Error('unknown field type: '  . $type);
        }

        return $this->page->getFormSet()
                ->getFieldByNameAndType($fieldName, $class) !== null;
    }

    public function setCheckboxValue(string $fieldName, string $value) : void {
        $this->page->getFormSet()->getCheckboxByName($fieldName)->check($value);
    }


    private function getFormSet() : FormSet {
        $formSet = $this->page->getFormSet();

        if ($formSet->getFormCount() === 0) {
            fail(ERROR_W07, "Current page does not contain any form elements");
        }

        return $formSet;
    }

    private function executeRequestWithRedirects(HttpRequest $request) {

        $response = $this->executeRequest($request);

        $count = $this->maxRedirectCount;
        while ($response->isRedirect() && $count-- > 0) {

            $request = new HttpRequest($request->getFullUrl(),
                $response->getLocation(), 'GET');

            $response = $this->executeRequest($request);
        }

        $this->updateState($response);
    }

    private function executeRequest(HttpRequest $request) : HttpResponse {
        $globals = $this->globals;

        $url = $request->getFullUrl()->asString();

        $this->assertValidUrl($url);

        try {
            $response = $this->httpClient->execute($request);
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

        $this->currentUrl = $request->getFullUrl();

        return $response;
    }

    private function updateState(HttpResponse $response) : void {

        $this->assertValidResponse($response->getResponseCode());

        $this->responseCode = $response->getResponseCode();
        $this->responseContents = $response->getContents();

        $pageParser = new PageParser($response->getContents());

        $this->assertValidHtml($pageParser);

        $nodeTree = new NodeTree($pageParser->getNodeTree());

        $page = (new PageBuilder($nodeTree, $response->getContents()))->getPage();

        $this->page = $page;
    }

    function submitFormByButtonPress(string $buttonName, ?string $buttonValue) {
        $formSet = $this->getFormSet();

        $request = (new RequestBuilder($formSet, $this->currentUrl))
            ->requestFromButtonPress($buttonName, $buttonValue);

        $this->executeRequestWithRedirects($request);
    }

    function hasElementWithId(string $id) : bool {
        return $this->getElementWithId($id) !== null;
    }

    public function getLinkHrefById(string $id) : string {
        return $this->page->getLinkById($id)->getHref();
    }

    public function getLinkHrefByText(string $text) : string {
        return $this->page->getLinkByText($text)->getHref();
    }

    public function forceFieldValue(string $fieldName, string $value) : void {
        $form = $this->page->getFormSet()->findFormContainingField($fieldName);

        $form->deleteFieldByName($fieldName);

        $form->addTextField($fieldName, $value);
    }

    function hasSelectOptionWithLabel(string $fieldName, string $label) : bool {
        $select = $this->page->getFormSet()->getSelectByName($fieldName);

        return $select->hasOptionWithLabel($label);
    }

    public function getFieldValue(string $fieldName) {
        $field = $this->page->getFormSet()->getFieldByName($fieldName);

        return $field instanceof Checkbox
            ? $field->isChecked()
            : $field->getValue();
    }

    function getSelectedOptionText(string $fieldName) : string {
        $select = $this->page->getFormSet()->getSelectByName($fieldName);

        return $select->getSelectedOptionText();
    }

    public function selectOptionWithLabel(string $fieldName, string $label) : void {
        $this->page->getFormSet()
            ->getSelectByName($fieldName)
            ->selectOptionWithText($label);
    }

    private function getElementWithId($id) : ?Element {
        $elements = $this->page->getElements();

        foreach ($elements as $element) {
            if ($element->getId() === $id) {
                return $element;
            }
        }

        return null;
    }

    function getPageText() : string {
        return $this->page->getText();
    }

    function getPageSource() : string {
        return $this->page->getSource();
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

    function fail($code, $message): void {
        throw new FrameworkException($code, $message);
    }

}