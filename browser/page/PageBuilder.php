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
require_once 'Page.php';
require_once 'Link.php';

require_once 'FormBuilder.php';

use tplLib\HtmlLexer;
use tplLib\HtmlParser;
use tplLib\TextNode;
use tplLib\TreeBuilderActions;
use \RuntimeException;

class PageBuilder {

    private string $html;
    private $tree;

    public function __construct(string $html) {
        $this->html = $html;
        $this->tree = $this->buildNodeTree($html);
    }

    function getPage() : Page {
        $text = html_entity_decode($this->getText($this->tree));

        return new Page($this->html, $text,
            $this->getLinks($this->tree), $this->getForm());
    }

    private function getForm() : ?Form {
        $forms = $this->findNodesByTagNames($this->tree, ['form']);

        if (count($forms) === 0) {
            return null;
        }

        if (count($forms) > 1) {
            throw new RuntimeException("This framework supports only one form per page");
        }

        $formElements = $this->findNodesByTagNames(
            $forms[0], ['input', 'button']);

        return (new FormBuilder($forms[0], $formElements))->buildForm();
    }

    private function buildNodeTree($html) {
        $tokens = (new HtmlLexer($html))->tokenize();

        $builder = new TreeBuilderActions();

        (new HtmlParser($tokens, $builder))->parse();

        return $builder->getResult();
    }

    private function findNodesByTagNames($node, $names) : array {
        $result = [];

        if (in_array($node->getTagName(), $names)) {
            $result[] = $node;
        }

        foreach ($node->getChildren() as $child) {
            $result = array_merge($result, $this->findNodesByTagNames($child, $names));
        }

        return $result;
    }

    private function getLinks($tree) : array {
        $linkNodes = $this->findNodesByTagNames($tree, ['a']);

        return array_map(function ($linkNode) {
            return new Link($this->getLinkText($linkNode),
                $linkNode->getAttributeValue('href'),
                $linkNode->getAttributeValue('id'));
        }, $linkNodes);
    }

    private function getLinkText($linkNode) : string {
        $text = '';

        $textNodes = $this->findNodesByTagNames($linkNode, ['']);

        foreach ($textNodes as $textNode) {
            $text .= $textNode->getText();
        }

        return $text;
    }

    private function getText($node) {
        if ($node instanceof TextNode) {
            return $node->getText();
        }

        $childTexts = [];
        foreach ($node->getChildren() as $child) {
            $childTexts[] = $this->getText($child);
        }

        $childTexts = array_filter($childTexts);

        return join("\n", $childTexts);
    }

}


