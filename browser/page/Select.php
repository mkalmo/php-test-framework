<?php

namespace stf\browser\page;

use \RuntimeException;

class Select extends AbstractInput {

    private array $options = [];

    public function __construct(string $name) {
        parent::__construct($name);
    }

    public function addOption(string $value, string $label) {
        $this->options[] = [$value, $label, false];
    }

    public function hasOptionWithLabel(string $label) : bool {
        foreach ($this->options as $each) {
            if ($each[1] === $label) {
                return true;
            }
        }

        return false;
    }

    public function selectOptionByValue(string $value) {
        foreach ($this->options as &$each) {
            if ($each[0] === $value) {
                $each[2] = true;
                return;
            }
        }

        throw new RuntimeException("unknown option value: " . $value);
    }

    public function selectOptionWithText(string $text) {
        $found = false;
        foreach ($this->options as &$each) {
            if ($each[1] === $text) {
                $each[2] = true;
                $found = true;
            } else {
                $each[2] = false;
            }
        }

        if (!$found) {
            throw new RuntimeException("unknown option text: " . $text);
        }
    }

    public function getSelectedOptionText() : string {
        if (count($this->options) < 1) {
            return '';
        }

        $text = $this->options[0][1];
        foreach ($this->options as $each) {
            if ($each[2]) {
                $text = $each[1];
            }
        }

        return $text;
    }

    public function __toString() : string {
        $values = array_map(function ($each) {
            return $each[0];
        }, $this->options);

        return sprintf("Select: %s (%s) selected: %s\n",
            $this->getName(), implode(", ", $values), $this->getValue());
    }

    public function getValue() : string {
        foreach ($this->options as $each) {
            if ($each[2]) {
                return $each[0];
            }
        }

        return '';
    }
}


