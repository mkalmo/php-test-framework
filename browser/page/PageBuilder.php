<?php

namespace stf\browser\page;

use \RuntimeException;

class PageBuilder {

    private NodeTree $nodeTree;
    private string $source;

    public function __construct(NodeTree $nodeTree, string $source) {
        $this->nodeTree = $nodeTree;
        $this->source = $source;
    }

    function getPage() : Page {
        $text = html_entity_decode($this->nodeTree->getFullText());

        $page = new Page($this->source, $text,
            $this->getLinks(), $this->getForm());

        $page->setElements($this->getAllElements());

        return $page;
    }

    private function getForm() : ?Form {
        $formBuilder = new FormBuilder($this->nodeTree);

        $formCount = $formBuilder->getFormCount();

        if ($formCount === 0) {
            return null;
        }

        if ($formCount > 1) {
            throw new RuntimeException("This framework supports only one form per page");
        }

        return $formBuilder->getForm();
    }

    private function getAllElements() : array {
        $nodes = $this->nodeTree->getAllTagNodes();

        return array_map(function ($node) {
            return new Element($node);
        }, $nodes);
    }

    private function getLinks() : array {
        $nodes = $this->nodeTree->findNodesByTagNames(['a']);

        return array_map(function ($linkNode) {
            return new Link($this->getLinkText($linkNode),
                $linkNode->getAttributeValue('href') ?? '',
                $linkNode->getAttributeValue('id') ?? '');
        }, $nodes);
    }

    private function getLinkText($linkNode) : string {
        return join("", $this->nodeTree->getTextLines($linkNode, true));
    }
}


