<?php

namespace stf\browser\page;

class Form {

    private string $action = '';
    private string $method = '';
    private array $fields = [];
    private array $buttons = [];

    public function addField($field) {
        $this->fields[] = $field;
    }

    public function addButton($button) {
        $this->buttons[] = $button;
    }

    public function getFields() : array {
        return $this->fields;
    }

    public function getButtonByName(string $buttonName) : ?Button {
        return $this->getButtonByNameAndValue($buttonName, null);
    }

    public function getButtonByNameAndValue(
        string $buttonName, ?string $buttonValue) : ?Button {

        $buttons = array_filter($this->buttons,
            function ($button) use ($buttonName, $buttonValue) {

                return $buttonValue === null
                    ? $button->getName() === $buttonName
                    : $button->getName() === $buttonName
                    && $button->getValue() === $buttonValue;
            });

        $button = array_shift($buttons);

        return $button ?? null;
    }

    public function getTextFieldByName($fieldName) : ?TextField {
        return $this->getFieldByNameCommon($fieldName, TextField::class);
    }

    private function getFieldByNameCommon($fieldName, $type) {
        $fields = array_filter($this->fields, function ($field) use ($fieldName, $type) {
            return $field->getName() === $fieldName
                && (get_class($field) === $type || is_subclass_of($field, $type));
        });

        $field = array_shift($fields);

        return $field ?? null;
    }

    public function getRadioByName($fieldName) : ?RadioGroup {
        return $this->getFieldByNameCommon($fieldName, RadioGroup::class);
    }

    public function getFieldByName($fieldName) : ?AbstractInput {
        return $this->getFieldByNameCommon($fieldName, AbstractInput::class);
    }

    public function getCheckboxByName($fieldName) : ?Checkbox {
        return $this->getFieldByNameCommon($fieldName, Checkbox::class);
    }

    public function getAction() : ?string {
        return $this->action;
    }

    public function setAction($action) : void {
        $this->action = $action;
    }

    public function getMethod(): string {
        return $this->method;
    }

    public function setMethod(string $method): void {
        $this->method = $method;
    }

    public function __toString() : string {
        $elements = [...$this->fields, ...$this->buttons];

        $strings = array_map(function ($each) {
            return "  " . $each->__toString();
        }, $elements);

        return "Form: " . PHP_EOL
            . join(PHP_EOL, $strings) . PHP_EOL;
    }
}
