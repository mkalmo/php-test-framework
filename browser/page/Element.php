<?php

namespace stf;

use tplLib\TagNode;

class Element {

    private TagNode $node;

    public function __construct(TagNode $node) {
        $this->node = $node;
    }

    public function getId(): ?string {
        return $this->node->getAttributeValue('id');
    }

    public function getAttributeValue(string $name): string {
        return $this->node->getAttributeValue($name);
    }

    public function getTagName(): string {
        return $this->node->getTagName();
    }

}


