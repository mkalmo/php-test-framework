<?php

namespace stf;

class Link {

    private $text;
    private $href;
    private $id;

    public function __construct($text, $href, $id) {
        $this->text = $text;
        $this->href = $href;
        $this->id = $id;
    }

    public function getText() : ?string {
        return $this->text;
    }

    public function getHref() : ?string {
        return $this->href;
    }

    public function getId() : ?string {
        return $this->id;
    }

    public function __toString() {
        return sprintf('<a href="%s">%s</a>',
            $this->href, $this->text);
    }

}


