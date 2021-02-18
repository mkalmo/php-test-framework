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
require_once 'TextField.php';
require_once 'Button.php';

use tplLib\TagNode;

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

    private function isRadio($element) : bool {
        return ($element->getTagName() === 'input')
                && $element->getAttributeValue('type') === 'radio';
    }

    private function createButton($element) : Button {
        $name = $element->getAttributeValue('name') ?? '';
        $value = $element->getAttributeValue('value') ?? '';
        $formAction = $element->getAttributeValue('formaction') ?? '';

        return new Button($name, $value, $formAction);
    }
}


