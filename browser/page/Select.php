<?php

namespace stf\browser\page;

use \RuntimeException;

class Select extends AbstractInput {

    private array $options = [];

    public function __construct(string $name) {
        parent::__construct($name);
    }

    public function addOption(?string $value, string $text) {
        $this->options[] = new Option($text, $value);
    }

    public function hasOptionWithLabel(string $text) : bool {
        foreach ($this->options as $each) {
            if ($each->getText() === $text) {
                return true;
            }
        }

        return false;
    }

    public function selectOptionByValue(string $value) {
        foreach ($this->options as $each) {
            if ($each->getValue() === $value) {
                $each->select();
                return;
            }
        }

        throw new RuntimeException("unknown option value: " . $value);
    }

    public function selectOptionWithText(string $text) {
        foreach ($this->options as $each) {
            $each->unSelect();
        }

        foreach ($this->options as $each) {
            if ($each->getText() === $text) {
                $each->select();
                return;
            }
        }

        throw new RuntimeException("unknown option text: " . $text);
    }

    public function getSelectedOptionText() : string {
        if (count($this->options) < 1) {
            return '';
        }

        foreach ($this->options as $each) {
            if ($each->isSelected()) {
                return $each->getText();
            }
        }

        return $this->options[0]->getText();
    }

    public function __toString() : string {
        $values = array_map(function ($each) {
            return $each->getValue();
        }, $this->options);

        return sprintf("Select: %s (%s) selected: %s\n",
            $this->getName(), implode(", ", $values), $this->getValue());
    }

    public function getValue() : string {
        if (empty($this->options)) {
            return '';
        }

        foreach ($this->options as $each) {
            if ($each->isSelected()) {
                return $each->getValue();
            }
        }

        return $this->options[0]->getValue();
    }
}
