<?php

namespace stf;

class Page {

    private string $source;
    private string $text;
    private array $links;
    private ?Form $form;
    private array $elements = [];

    public function __construct(
        string $source, string $text, array $links, ?Form $form) {

        $this->source = $source;
        $this->text = $text;
        $this->links = $links;
        $this->form = $form;
    }

    public function setElements(array $elements): void {
        $this->elements = $elements;
    }

    public function getElements(): array {
        return $this->elements;
    }

    public function getId(): ?string {
        $nodeList = array_filter($this->elements, function ($each) {
            return strtolower($each->getTagName()) === 'body';
        });

        $nodeList = array_values($nodeList);

        if (count($nodeList) < 1) {
            return null;
        }

        return $nodeList[0]->getId();
    }

    public function getForm(): ?Form {
        return $this->form;
    }

    public function containsForm(): bool {
        return $this->form !== null;
    }

    public function getLinkById(string $id) : ?Link {
        return array_values(array_filter($this->links, function ($link) use ($id) {
            return $link->getId() === $id;
        }))[0] ?? null;
    }

    public function getLinkByText(string $text) : ?Link {
        return array_values(array_filter($this->links, function ($link) use ($text) {
            return $link->getText() === $text;
        }))[0] ?? null;
    }

    public function getSource(): string {
        return $this->source;
    }

    public function getText(): string {
        return $this->text;
    }

}


