<?php

namespace stf\browser\page;

use tplLib\node\TagNode;
use tplLib\node\AbstractNode;

use \RuntimeException;

class PageBuilder {

    private string $html;
    private AbstractNode $tree;

    public function __construct(string $html, AbstractNode $tree) {
        $this->html = $html;
        $this->tree = $tree;
    }

    function getPage() : Page {
        $text = html_entity_decode($this->getText($this->tree));

        $page = new Page($this->html, $text,
            $this->getLinks($this->tree), $this->getForm());

        $page->setElements($this->getAllElements($this->tree));

        return $page;
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
            $forms[0], ['input', 'button', 'textarea']);

        return (new FormBuilder($forms[0], $formElements))->buildForm();
    }

    private function findNodesByTagNames($node, $names) : array {
        $nodeList = array_filter($this->getAllTagNodes($node), function ($each) use ($names) {
            return in_array($each->getTagName(), $names);
        });

        return array_values($nodeList);
    }

    private function getAllElements(AbstractNode $node) : array {
        $nodes = $this->getAllTagNodes($node);

        return array_map(function ($node) {
            return new Element($node);
        }, $nodes);
    }

    private function getAllTagNodes(AbstractNode $node) : array {
        $result = [];

        if ($node instanceof TagNode) {
            $result[] = $node;
        }

        foreach ($node->getChildren() as $child) {
            $result = array_merge(
                $result,
                $this->getAllTagNodes($child));
        }

        return $result;
    }

    private function getLinks($tree) : array {
        $nodes = $this->findNodesByTagNames($tree, ['a']);

        return array_map(function ($linkNode) {
            return new Link($this->getLinkText($linkNode),
                $linkNode->getAttributeValue('href') ?? '',
                $linkNode->getAttributeValue('id') ?? '');
        }, $nodes);
    }

    private function getLinkText($linkNode) : string {
        return join("", PageParser::getTextLines($linkNode, true));
    }

    private function getText($node) : string {
        return join("\n", PageParser::getTextLines($node));
    }
}


