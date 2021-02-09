<?php

namespace stf;

use \RuntimeException;

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

    public function setFieldValue(string $fieldName, string $value) : void {
        $this->getFieldByName($fieldName)->setValue($value);
    }

    public function getButtonByName($buttonName) : Button {
        $buttons = array_filter($this->buttons, function ($button) use ($buttonName) {
            return $button->getName() === $buttonName;
        });

        $button = array_shift($buttons);

        if (!$button) {
            throw new RuntimeException(
                sprintf("button with name %s does not exist", $buttonName));
        }

        return $button;
    }

    public function getFieldByName($fieldName) : Input {
        $fields = array_filter($this->fields, function ($field) use ($fieldName) {
            return $field->getName() === $fieldName;
        });

        $field = array_shift($fields);

        if (!$field) {
            throw new RuntimeException(
                sprintf("field with name %s does not exist", $fieldName));
        }

        return $field;
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
        $fields = array_map(function ($each) {
            return "  " . $each->__toString();
        }, $this->fields);

        return "Form: " . PHP_EOL . join(PHP_EOL, $fields) . PHP_EOL;
    }


}

