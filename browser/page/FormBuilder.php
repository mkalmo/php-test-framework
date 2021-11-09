<?php

namespace stf\browser\page;

use tplLib\node\TagNode;

class FormBuilder {

    private NodeTree $nodeTree;

    public function __construct(NodeTree $nodeTree) {
        $this->nodeTree = $nodeTree;
    }

    public function getFormSet() : FormSet {
        $formSet = new FormSet();

        $formNodes = $this->nodeTree->findNodesByTagNames(['form']);

        foreach ($formNodes as $formNode) {
            $formSet->addForm($this->buildForm($formNode));
        }

        return $formSet;
    }

    private function buildForm(TagNode $formNode) : Form {
        $formElements = $this->nodeTree->findChildNodesByTagNames(
            $formNode, ['input', 'button', 'textarea', 'select']);

        $form = new Form();

        $form->setAction($formNode->getAttributeValue('action') ?? '');
        $form->setMethod($formNode->getAttributeValue('method') ?? '');

        $radios = [];
        foreach ($formElements as $element) {
            if ($this->isButton($element)) {
                $form->addButton($this->createButton($element));
            } else if ($this->isRadio($element)) {

                $name = $element->getAttributeValue('name') ?? '';
                $radio = $radios[$name] ??= new RadioGroup($name);

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
                $value = join("", $this->nodeTree->getTextLines($element, true));

                $form->addField(new TextField($name, $value));

            } else if ($this->isSelect($element)) {
                $form->addField($this->createSelect($element));

            } else {
                $name = $element->getAttributeValue('name') ?? '';
                $value = $element->getAttributeValue('value') ?? '';

                $form->addField(new TextField($name, $value));
            }
        }

        foreach ($radios as $radio) {
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

    private function isSelect($element) : bool {
        return $element->getTagName() === 'select';
    }

    private function createSelect($element) : Select {
        $name = $element->getAttributeValue('name') ?? '';

        $select = new Select($name);

        $options = $this->nodeTree->findChildNodesByTagNames($element, ['option']);

        foreach ($options as $option) {
            $value = $option->getAttributeValue('value');
            $label = implode('', $this->nodeTree->getTextLines($option));
            $select->addOption($value, trim($label), $option->hasAttribute('selected'));
        }

        return $select;
    }

    private function createButton($element) : Button {
        $name = $element->getAttributeValue('name') ?? '';
        $value = $element->getAttributeValue('value') ?? '';
        $formAction = $element->getAttributeValue('formaction') ?? '';

        $label = $element->getTagName() === 'input'
            ? $value
            : implode('', $this->nodeTree->getTextLines($element));

        return new Button($name, $value, $label, $formAction);
    }
}
