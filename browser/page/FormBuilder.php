<?php

namespace stf;

require_once __DIR__ . '/../parser/HtmlLexer.php';
require_once __DIR__ . '/../parser/HtmlParser.php';
require_once __DIR__ . '/../parser/TreeBuilderActions.php';
require_once __DIR__ . '/../parser/node/AbstractNode.php';
require_once __DIR__ . '/../parser/node/WsNode.php';
require_once __DIR__ . '/../parser/node/TextNode.php';
require_once __DIR__ . '/../parser/node/MiscNode.php';

require_once 'Form.php';
require_once 'Input.php';
require_once 'Button.php';

use tplLib\TagNode;

class FormBuilder {

    private TagNode $formNode;
    private array $formElements;

    public function __construct($formNode, array $formElements) {
        $this->formNode = $formNode;
        $this->formElements = $formElements;
    }

    public function buildForm() : Form {
        $form = new Form();

        $form->setAction($this->formNode->getAttributeValue('action'));
        $form->setMethod($this->formNode->getAttributeValue('method'));

        foreach ($this->formElements as $element) {
            if ($this->isButton($element)) {
                $form->addButton($this->createButton($element));
            } else {
                $name = $element->getAttributeValue('name');
                $value = $element->getAttributeValue('value');

                $form->addField(new Input($name, $value));
            }
        }

        return $form;
    }

    private function isButton($element) : bool {
        return $this->createButton($element) !== null;
    }

    private function createButton($element) : ?Button {
        if ($element->getTagName() === 'button'
            || $element->getTagName() === 'input'
               && $element->getAttributeValue('type') === 'submit') {

            $name = $element->getAttributeValue('name');
            $value = $element->getAttributeValue('value');
            $formAction = $element->getAttributeValue('formaction');

            return new Button($name, $value, $formAction);

        } else {
            return null;
        }
    }
}


