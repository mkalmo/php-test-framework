<?php

namespace stf\browser\page;

use tplLib\node\TagNode;

class FormBuilder {

    private TagNode $formNode;
    private array $formElements;
    private array $radios = [];

    public function __construct($formNode, array $formElements) {
        $this->formNode = $formNode;
        $this->formElements = $formElements;
    }

    public function buildForm() : Form {
        $form = new Form();

        $form->setAction($this->formNode->getAttributeValue('action') ?? '');
        $form->setMethod($this->formNode->getAttributeValue('method') ?? '');

        foreach ($this->formElements as $element) {
            if ($this->isButton($element)) {
                $form->addButton($this->createButton($element));
            } else if ($this->isRadio($element)) {

                $name = $element->getAttributeValue('name') ?? '';
                $radio = $this->radios[$name] ??= new RadioGroup($name);

                $value = $element->getAttributeValue('value') ?? '';
                $radio->addOption($value);
                if ($element->hasAttribute('checked')) {
                    $radio->selectOption($value);
                }

            } else if ($this->isCheckbox($element)) {

                $value = $element->getAttributeValue('value') ?? 'on';
                $name = $element->getAttributeValue('name') ?? '';
                $checkbox = new Checkbox($name, $value);
                $checkbox->check($element->hasAttribute('checked'));

                $form->addField($checkbox);

            } else if ($this->isTextArea($element)) {
                $name = $element->getAttributeValue('name') ?? '';
                $value = join("", PageParser::getTextLines($element, true));

                $form->addField(new TextField($name, $value));

            } else {
                $name = $element->getAttributeValue('name') ?? '';
                $value = $element->getAttributeValue('value') ?? '';

                $form->addField(new TextField($name, $value));
            }
        }

        foreach ($this->radios as $radio) {
            $form->addField($radio);
        }

        return $form;
    }

    private function isButton($element) : bool {
        return ($element->getTagName() === 'button' || $element->getTagName() === 'input')
                && $element->getAttributeValue('type') === 'submit';
    }

    private function isTextArea($element) : bool {
        return $element->getTagName() === 'textarea';
    }

    private function isRadio($element) : bool {
        return ($element->getTagName() === 'input')
                && $element->getAttributeValue('type') === 'radio';
    }

    private function isCheckbox($element) : bool {
        return ($element->getTagName() === 'input')
                && $element->getAttributeValue('type') === 'checkbox';
    }

    private function createButton($element) : Button {
        $name = $element->getAttributeValue('name') ?? '';
        $value = $element->getAttributeValue('value') ?? '';
        $formAction = $element->getAttributeValue('formaction') ?? '';

        return new Button($name, $value, $formAction);
    }
}


