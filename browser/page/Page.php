<?php

namespace stf;

class Page {

    private string $source;
    private string $text;
    private array $links;
    private ?Form $form;
    private ?string $id;

    public function __construct(
        string $source, string $text, array $links, ?Form $form) {

        $this->source = $source;
        $this->text = $text;
        $this->links = $links;
        $this->form = $form;
    }

    public function setId(?string $id): void {
        $this->id = $id;
    }

    public function getId(): ?string {
        return $this->id;
    }

    public function getForm(): Form {
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


